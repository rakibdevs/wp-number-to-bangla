<?php

/**
 * Core converter for the "Number to Bangla" plugin.
 *
 * Framework-free, dependency-free static converter. Used by the shortcode,
 * the Gutenberg block render callback, the REST endpoint and the template
 * helper functions so that every interface shares a single code path.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class BanglaNumberConverter
{
    /**
     * The documented maximum convertible number.
     *
     * Capped just under 1 arab (10^9): numToWord() only names magnitudes up
     * to কোটি (crore, 10^7) and recurses for the crore digit group itself
     * once it exceeds 99, which reads as non-idiomatic Bangla ("X কোটি Y
     * কোটি...") for values at or above 1,000,000,000.
     */
    const MAX_NUMBER = 999999999;

    protected static $words = [
        'শূন্য', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়', 'দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোল', 'সতেরো', 'আঠারো', 'উনিশ', 'বিশ', 'একুশ', 'বাইশ', 'তেইশ', 'চব্বিশ', 'পঁচিশ', 'ছাব্বিশ', 'সাতাশ', 'আঠাশ', 'ঊনত্রিশ', 'ত্রিশ', 'একত্রিশ', 'বত্রিশ', 'তেত্রিশ', 'চৌত্রিশ', 'পঁয়ত্রিশ', 'ছত্রিশ', 'সাঁইত্রিশ', 'আটত্রিশ', 'ঊনচল্লিশ', 'চল্লিশ', 'একচল্লিশ', 'বিয়াল্লিশ', 'তেতাল্লিশ', 'চুয়াল্লিশ', 'পঁয়তাল্লিশ', 'ছেচল্লিশ', 'সাতচল্লিশ', 'আটচল্লিশ', 'ঊনপঞ্চাশ', 'পঞ্চাশ', 'একান্ন', 'বাহান্ন', 'তিপ্পান্ন', 'চুয়ান্ন', 'পঞ্চান্ন', 'ছাপ্পান্ন', 'সাতান্ন', 'আটান্ন', 'ঊনষাট', 'ষাট', 'একষট্টি', 'বাষট্টি', 'তেষট্টি', 'চৌষট্টি', 'পঁয়ষট্টি', 'ছেষট্টি', 'সাতষট্টি', 'আটষট্টি', 'ঊনসত্তর', 'সত্তর', 'একাত্তর', 'বাহাত্তর', 'তিয়াত্তর', 'চুয়াত্তর', 'পঁচাত্তর', 'ছিয়াত্তর', 'সাতাত্তর', 'আটাত্তর', 'ঊনআশি', 'আশি', 'একাশি', 'বিরাশি', 'তিরাশি', 'চুরাশি', 'পঁচাশি', 'ছিয়াশি', 'সাতাশি', 'আটাশি', 'ঊননব্বই', 'নব্বই', 'একানব্বই', 'বিরানব্বই', 'তিরানব্বই', 'চুরানব্বই', 'পঁচানব্বই', 'ছিয়ানব্বই', 'সাতানব্বই', 'আটানব্বই', 'নিরানব্বই'
    ];

    protected static $bnMonth = [
        '1' => 'জানুয়ারি',
        '2' => 'ফেব্রুয়ারি',
        '3' => 'মার্চ',
        '4' => 'এপ্রিল',
        '5' => 'মে',
        '6' => 'জুন',
        '7' => 'জুলাই',
        '8' => 'আগস্ট',
        '9' => 'সেপ্টেম্বর',
        '10' => 'অক্টোবর',
        '11' => 'নভেম্বর',
        '12' => 'ডিসেম্বর'
    ];

    /**
     * Bengali (Bangla) calendar months.
     */
    protected static $bnBengaliMonth = [
        '1' => 'বৈশাখ',
        '2' => 'জ্যৈষ্ঠ',
        '3' => 'আষাঢ়',
        '4' => 'শ্রাবণ',
        '5' => 'ভাদ্র',
        '6' => 'আশ্বিন',
        '7' => 'কার্তিক',
        '8' => 'অগ্রহায়ণ',
        '9' => 'পৌষ',
        '10' => 'মাঘ',
        '11' => 'ফাল্গুন',
        '12' => 'চৈত্র'
    ];

    /**
     * The six Bengali seasons.
     */
    protected static $bnSeason = [
        '1' => 'গ্রীষ্ম',
        '2' => 'বর্ষা',
        '3' => 'শরৎ',
        '4' => 'হেমন্ত',
        '5' => 'শীত',
        '6' => 'বসন্ত'
    ];

    /**
     * Weekday names indexed 0 (Sunday) .. 6 (Saturday) to match PHP date('w').
     */
    protected static $bnDay = [
        0 => 'রবিবার',
        1 => 'সোমবার',
        2 => 'মঙ্গলবার',
        3 => 'বুধবার',
        4 => 'বৃহস্পতিবার',
        5 => 'শুক্রবার',
        6 => 'শনিবার'
    ];

    /**
     * English weekday name => index map for string input.
     */
    protected static $dayNameMap = [
        'sunday'    => 0,
        'monday'    => 1,
        'tuesday'   => 2,
        'wednesday' => 3,
        'thursday'  => 4,
        'friday'    => 5,
        'saturday'  => 6,
    ];

    /**
     * Irregular Bangla ordinals (1..10). Larger values fall back to <digit>তম.
     */
    protected static $ordinals = [
        1  => '১ম',
        2  => '২য়',
        3  => '৩য়',
        4  => '৪র্থ',
        5  => '৫ম',
        6  => '৬ষ্ঠ',
        7  => '৭ম',
        8  => '৮ম',
        9  => '৯ম',
        10 => '১০ম',
    ];

    protected static $numbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    /**
     * Convert English digits to Bangla digits.
     *
     * @param int|float|string $num
     * @return string|false
     */
    public static function bnNum($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        return strtr((string) $num, self::$numbers);
    }

    /**
     * Convert a number to its Bangla word representation.
     *
     * @param int|float|string $num
     * @return string|false
     */
    public static function bnWord($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        if ($num == 0) {
            return 'শূন্য';
        }

        $prefix = '';
        $num = (string) $num;
        if (strpos($num, '-') === 0) {
            $prefix = 'ঋণাত্মক ';
            $num = substr($num, 1);
        }

        if (strpos($num, '.') !== false) {
            $decimal = explode('.', $num);
            $text = trim(self::numToWord((int) $decimal[0]));
            if (isset($decimal[1]) && (int) $decimal[1] > 0) {
                $text .= ' দশমিক ' . self::convertDecimalPartToWords($decimal[1]);
            }
            return $prefix . $text;
        }

        return $prefix . trim(self::numToWord((int) $num));
    }

    /**
     * Convert a number to Bangla currency (Taka/Poisha) words.
     *
     * @param int|float|string $num
     * @return string|false
     */
    public static function bnMoney($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        if ($num == 0) {
            return 'শূন্য টাকা';
        }

        $prefix = '';
        if ($num < 0) {
            $prefix = 'ঋণাত্মক ';
            $num = abs($num);
        }

        $money = number_format((float) $num, 2, '.', '');
        $decimal = explode('.', $money);
        $text = trim(self::numToWord((int) $decimal[0])) . ' টাকা';
        if (isset($decimal[1]) && (int) $decimal[1] > 0) {
            $text .= ' ' . self::$words[(int) $decimal[1]] . ' পয়সা';
        }

        return $prefix . $text;
    }

    /**
     * Gregorian month number (1-12) to Bangla month name.
     *
     * @param int|string $num
     * @return string|false
     */
    public static function bnMonth($num)
    {
        return (is_numeric($num) && $num >= 1 && $num <= 12)
            ? self::$bnMonth[(int) $num]
            : false;
    }

    /**
     * Bengali calendar month number (1-12) to Bangla name.
     *
     * @param int|string $num
     * @return string|false
     */
    public static function bnBengaliMonth($num)
    {
        return (is_numeric($num) && $num >= 1 && $num <= 12)
            ? self::$bnBengaliMonth[(int) $num]
            : false;
    }

    /**
     * Bengali season number (1-6) to name.
     *
     * @param int|string $num
     * @return string|false
     */
    public static function bnSeason($num)
    {
        return (is_numeric($num) && $num >= 1 && $num <= 6)
            ? self::$bnSeason[(int) $num]
            : false;
    }

    /**
     * Weekday to Bangla name. Accepts 0-6 (Sunday-Saturday) or an English name.
     *
     * @param int|string $day
     * @return string|false
     */
    public static function bnDay($day)
    {
        if (is_numeric($day)) {
            $index = (int) $day;
            return isset(self::$bnDay[$index]) ? self::$bnDay[$index] : false;
        }

        $key = strtolower(trim((string) $day));
        if (isset(self::$dayNameMap[$key])) {
            return self::$bnDay[self::$dayNameMap[$key]];
        }

        return false;
    }

    /**
     * Format numbers with lakh-style (Indian) comma grouping in Bangla digits.
     *
     * @param int|float|string $num
     * @return string|false
     */
    public static function bnCommaLakh($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        $withCommaNumber = preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/', '$1,', (string) $num);

        return strtr($withCommaNumber, self::$numbers);
    }

    /**
     * Convert a number to a Bangla percentage.
     *
     * @param int|float|string $num
     * @param bool             $inWords When true, spell the value out in words.
     * @return string|false
     */
    public static function bnPercentage($num, $inWords = false)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        $value = $inWords ? self::bnWord($num) : self::bnNum($num);

        return $value . ' শতাংশ';
    }

    /**
     * Convert a date to a Bangla-formatted date string.
     *
     * Recognised format tokens (PHP date() style): d, j, m, n, F, Y, y, l, D.
     * Any other character is emitted as-is.
     *
     * @param string|int $date   A date string or unix timestamp.
     * @param string     $format Output format. Default "j F, Y".
     * @return string|false
     */
    public static function bnDate($date, $format = 'j F, Y')
    {
        try {
            $dt = is_numeric($date)
                ? new DateTime('@' . (int) $date)
                : new DateTime((string) $date, new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return false;
        }

        $out = '';
        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            $token = $format[$i];
            switch ($token) {
                case 'd':
                case 'j':
                case 'm':
                case 'n':
                case 'Y':
                case 'y':
                    $out .= self::bnNum($dt->format($token));
                    break;
                case 'F':
                    $out .= self::bnMonth((int) $dt->format('n'));
                    break;
                case 'l':
                case 'D':
                    $out .= self::bnDay((int) $dt->format('w'));
                    break;
                default:
                    $out .= $token;
                    break;
            }
        }

        return $out;
    }

    /**
     * Convert a time to Bangla, either as digits ("১৪:৩০") or as words with a
     * time-of-day prefix ("দুপুর দুইটা ত্রিশ মিনিট").
     *
     * @param string $time    Any time string parseable by DateTime.
     * @param bool   $inWords When true, return the spoken form.
     * @return string|false
     */
    public static function bnTime($time, $inWords = false)
    {
        try {
            $dt = new DateTime((string) $time, new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return false;
        }

        if (!$inWords) {
            return self::bnNum($dt->format('H')) . ':' . self::bnNum($dt->format('i'));
        }

        $hour24 = (int) $dt->format('G');
        $hour12 = (int) $dt->format('g');
        $minute = (int) $dt->format('i');

        if ($hour24 >= 4 && $hour24 < 6) {
            $period = 'ভোর';
        } elseif ($hour24 >= 6 && $hour24 < 12) {
            $period = 'সকাল';
        } elseif ($hour24 >= 12 && $hour24 < 16) {
            $period = 'দুপুর';
        } elseif ($hour24 >= 16 && $hour24 < 18) {
            $period = 'বিকাল';
        } elseif ($hour24 >= 18 && $hour24 < 20) {
            $period = 'সন্ধ্যা';
        } else {
            $period = 'রাত';
        }

        $text = $period . ' ' . self::bnWord($hour12) . 'টা';
        if ($minute > 0) {
            $text .= ' ' . self::bnWord($minute) . ' মিনিট';
        }

        return $text;
    }

    /**
     * Convert a duration in seconds to Bangla (e.g. "১ ঘণ্টা ১ মিনিট ৫ সেকেন্ড").
     *
     * @param int|string $seconds
     * @return string|false
     */
    public static function bnDuration($seconds)
    {
        if (!is_numeric($seconds)) {
            return false;
        }

        $seconds = (int) abs($seconds);
        $hours = (int) ($seconds / 3600);
        $minutes = (int) (($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = self::bnNum($hours) . ' ঘণ্টা';
        }
        if ($minutes > 0) {
            $parts[] = self::bnNum($minutes) . ' মিনিট';
        }
        if ($secs > 0 || empty($parts)) {
            $parts[] = self::bnNum($secs) . ' সেকেন্ড';
        }

        return implode(' ', $parts);
    }

    /**
     * Calculate age from a birth date in Bangla (e.g. "৩৫ বছর ২ মাস ৫ দিন").
     *
     * @param string      $birthDate A date string parseable by DateTime.
     * @param string|null $asOf      Reference date (defaults to now); enables
     *                               deterministic testing.
     * @param bool        $detailed  Include months and days when true.
     * @return string|false
     */
    public static function bnAge($birthDate, $asOf = null, $detailed = true)
    {
        try {
            $birth = new DateTime((string) $birthDate);
            $now = new DateTime($asOf !== null ? (string) $asOf : 'now');
        } catch (Exception $e) {
            return false;
        }

        if ($birth > $now) {
            return false;
        }

        $diff = $birth->diff($now);

        if (!$detailed) {
            return self::bnNum($diff->y) . ' বছর';
        }

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = self::bnNum($diff->y) . ' বছর';
        }
        if ($diff->m > 0) {
            $parts[] = self::bnNum($diff->m) . ' মাস';
        }
        if ($diff->d > 0 || empty($parts)) {
            $parts[] = self::bnNum($diff->d) . ' দিন';
        }

        return implode(' ', $parts);
    }

    /**
     * Convert a number to a Bangla ordinal (e.g. 1 => ১ম, 25 => ২৫তম).
     *
     * @param int|string $num
     * @return string|false
     */
    public static function bnOrdinal($num)
    {
        if (!is_numeric($num) || (int) $num != $num || $num < 1 || $num > self::MAX_NUMBER) {
            return false;
        }

        $num = (int) $num;
        if (isset(self::$ordinals[$num])) {
            return self::$ordinals[$num];
        }

        return self::bnNum($num) . 'তম';
    }

    /**
     * Reverse conversion: Bangla digits back to English digits.
     *
     * @param string $str
     * @return string
     */
    public static function parseNum($str)
    {
        $map = [];
        foreach (self::$numbers as $english => $bangla) {
            $map[$bangla] = (string) $english;
        }

        return strtr((string) $str, $map);
    }

    /**
     * Checks if a given number is valid for conversion.
     *
     * @param float|int|string $number The number to validate.
     * @return bool
     */
    protected static function isValidNumber($number)
    {
        return is_numeric($number) &&
            !preg_match('/\.\d+\./', (string) $number) &&
            !preg_match('/\d+E\d+/i', (string) $number) &&
            abs($number) <= self::MAX_NUMBER;
    }

    /**
     * Converts an integer to its Bangla word representation.
     *
     * @param int $num
     * @return string
     */
    protected static function numToWord(int $num)
    {
        $text = '';
        $toCrore = (int) ($num / 10000000);
        if ($toCrore !== 0) {
            if ($toCrore > 99) {
                $text .= self::bnWord($toCrore) . ' কোটি ';
            } else {
                $text .= self::$words[$toCrore] . ' কোটি ';
            }
        }

        $croreDiv = $num % 10000000;

        $toLakh = (int) ($croreDiv / 100000);
        if ($toLakh > 0) {
            $text .= self::$words[$toLakh] . ' লক্ষ ';
        }

        $lakhDiv = $croreDiv % 100000;

        $toThousand = (int) ($lakhDiv / 1000);
        if ($toThousand > 0) {
            $text .= self::$words[$toThousand] . ' হাজার ';
        }

        $thousandDiv = $lakhDiv % 1000;

        $toHundred = (int) ($thousandDiv / 100);
        if ($toHundred > 0) {
            $text .= self::$words[$toHundred] . ' শত ';
        }

        $hundredDiv = (int) ($thousandDiv % 100);
        if ($hundredDiv > 0) {
            $text .= self::$words[$hundredDiv];
        }

        return $text;
    }

    /**
     * Spell out the fractional part of a number digit-by-digit.
     *
     * @param string $number
     * @return string
     */
    private static function convertDecimalPartToWords($number)
    {
        $word = '';
        $numberLength = strlen($number);

        for ($i = 0; $i < $numberLength; $i++) {
            $digit = (int) $number[$i];
            $word .= ' ' . self::$words[$digit];
        }

        return trim($word);
    }
}
