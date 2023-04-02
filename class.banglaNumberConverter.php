<?php

class BanglaNumberConverter
{
    protected static $words = [
        'শূন্য', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়', 'দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোল', 'সতেরো', 'আঠারো', 'উনিশ', 'বিশ', 'একুশ', 'বাইশ', 'তেইশ', 'চব্বিশ', 'পঁচিশ', 'ছাব্বিশ', 'সাতাশ', 'আঠাশ', 'ঊনত্রিশ', 'ত্রিশ', 'একত্রিশ', 'বত্রিশ', 'তেত্রিশ', 'চৌত্রিশ', 'পঁয়ত্রিশ', 'ছত্রিশ', 'সাঁইত্রিশ', 'আটত্রিশ', 'ঊনচল্লিশ', 'চল্লিশ', 'একচল্লিশ', 'বিয়াল্লিশ', 'তেতাল্লিশ', 'চুয়াল্লিশ', 'পঁয়তাল্লিশ', 'ছেচল্লিশ', 'সাতচল্লিশ', 'আটচল্লিশ', 'ঊনপঞ্চাশ', 'পঞ্চাশ', 'একান্ন', 'বাহান্ন', 'তিপ্পান্ন', 'চুয়ান্ন', 'পঞ্চান্ন', 'ছাপ্পান্ন', 'সাতান্ন', 'আটান্ন', 'ঊনষাট', 'ষাট', 'একষট্টি', 'বাষট্টি', 'তেষট্টি', 'চৌষট্টি', 'পঁয়ষট্টি', 'ছেষট্টি', 'সাতষট্টি', 'আটষট্টি', 'ঊনসত্তর', 'সত্তর', 'একাত্তর', 'বাহাত্তর', 'তিয়াত্তর', 'চুয়াত্তর', 'পঁচাত্তর', 'ছিয়াত্তর', 'সাতাত্তর', 'আটাত্তর', 'ঊনআশি', 'আশি', 'একাশি', 'বিরাশি', 'তিরাশি', 'চুরাশি', 'পঁচাশি', 'ছিয়াশি', 'সাতাশি', 'আটাশি', 'ঊননব্বই', 'নব্বই', 'একানব্বই', 'বিরানব্বই', 'তিরানব্বই', 'চুরানব্বই', 'পঁচানব্বই', 'ছিয়ানব্বই', 'সাতানব্বই', 'আটানব্বই', 'নিরানব্বই'
    ];

    protected static $bnMonth = [
        '1' => 'জানুয়ারি',
        '2' => 'ফেব্রুয়ারি',
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

    protected static $numbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    public static function bnNum($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        return strtr($num, self::$numbers);
    }

    public static  function bnWord($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        if ($num == 0) {
            return 'শূন্য';
        }

        if (is_float($num) || strpos($num, '.') !== false) {
            $decimal = explode(".", $num);
            $text = self::numToWord($decimal[0]);
            if (isset($decimal[1]) && $decimal[1] > 0) {
                $text .= ' দশমিক ' . self::convertDecimalPartToWords((string)$decimal[1]);
            }
            return $text;
        }

        return self::numToWord($num);
    }

    public static function bnMoney($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        if ($num == 0) {
            return 'শূন্য টাকা';
        }

        $money = number_format((float) $num, 2, '.', '');
        $decimal = explode(".", $money);
        $text = self::numToWord($decimal[0]) . ' টাকা ';
        if (isset($decimal[1]) && $decimal[1] > 0) {
            $text .= self::$words[(int) $decimal[1]] . ' পয়সা';
        }

        return $text;
    }

    public static function bnMonth($num)
    {
        return (is_numeric($num) && $num >= 1 && $num <= 12)
            ? self::$bnMonth[(int)$num]
            : false;
    }

    public static function bnCommaLakh($num)
    {
        if (!self::isValidNumber($num)) {
            return false;
        }

        $withCommaNumber = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

        return strtr($withCommaNumber, self::$numbers);
    }

    /**
     * Checks if a given number is valid or not.
     *
     * @param float|string $num The number to validate.
     * @return bool Returns true if the number is valid, false otherwise.
     */
    protected static function isValidNumber($number)
    {
        return is_numeric($number) &&
            !preg_match('/\.\d+\./', $number) &&
            !preg_match('/\d+E\d+/', $number) &&
            abs($number) <= 999999999999999;
    }

    /**
     * Converts a number to its Bangla word representation.
     *
     * @param int $num The number to convert.
     *
     * @return string|false 
     */
    protected static function numToWord(int $num)
    {
        $text = '';
        $toCrore = (int)($num / 10000000);
        if ($toCrore !== 0) {
            if ($toCrore > 99) {
                $text .= self::bnWord($toCrore) . ' কোটি ';
            } else {
                $text .= self::$words[$toCrore] . ' কোটি ';
            }
        }

        $croreDiv = $num % 10000000;

        $toLakh = (int)($croreDiv / 100000);
        if ($toLakh > 0) {
            $text .= self::$words[$toLakh] . ' লক্ষ ';
        }

        $lakhDiv = $croreDiv % 100000;

        $toThousand = (int)($lakhDiv / 1000);
        if ($toThousand > 0) {
            $text .= self::$words[$toThousand] . ' হাজার ';
        }

        $thousandDiv = $lakhDiv % 1000;

        $toHundred = (int)($thousandDiv / 100);
        if ($toHundred > 0) {
            $text .= self::$words[$toHundred] . ' শত ';
        }

        $hundredDiv = (int)($thousandDiv % 100);
        if ($hundredDiv > 0) {
            $text .= self::$words[$hundredDiv];
        }

        return $text;
    }


    private static function convertDecimalPartToWords($number)
    {
        $word = '';
        $numberLength = strlen($number);

        // Loop through each digit of the number
        for ($i = 0; $i < $numberLength; $i++) {
            $digit = (int)$number[$i];
            $word .= ' ' . self::$words[$digit];
        }

        return $word;
    }
}
