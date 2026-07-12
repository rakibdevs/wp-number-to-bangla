<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for BanglaNumberConverter.
 */
class ConverterTest extends TestCase
{
    /* ---- bnNum --------------------------------------------------------- */

    public function testBnNum()
    {
        $this->assertSame('১১১', BanglaNumberConverter::bnNum('111'));
        $this->assertSame('১৩৪৫.৫', BanglaNumberConverter::bnNum('1345.5'));
        $this->assertSame('০', BanglaNumberConverter::bnNum(0));
    }

    public function testBnNumRejectsInvalid()
    {
        $this->assertFalse(BanglaNumberConverter::bnNum('abc'));
        $this->assertFalse(BanglaNumberConverter::bnNum('1e5'));
        $this->assertFalse(BanglaNumberConverter::bnNum('1.2.3'));
        $this->assertFalse(BanglaNumberConverter::bnNum(''));
    }

    /* ---- bnWord -------------------------------------------------------- */

    public function testBnWordBasics()
    {
        $this->assertSame('শূন্য', BanglaNumberConverter::bnWord(0));
        $this->assertSame('এক শত এগারো', BanglaNumberConverter::bnWord(111));
        $this->assertSame('তেরো হাজার চার শত ঊনষাট', BanglaNumberConverter::bnWord(13459));
    }

    public function testBnWordDecimals()
    {
        $this->assertSame(
            'এক হাজার তিন শত পঁয়তাল্লিশ দশমিক শূন্য পাঁচ',
            BanglaNumberConverter::bnWord('1345.05')
        );
    }

    public function testBnWordNegative()
    {
        $this->assertSame('ঋণাত্মক পাঁচ', BanglaNumberConverter::bnWord(-5));
    }

    public function testBnWordNoTrailingSpace()
    {
        $word = BanglaNumberConverter::bnWord(1000);
        $this->assertSame(trim($word), $word);
        $this->assertSame('এক হাজার', $word);
    }

    public function testBnWordMaxNumber()
    {
        $this->assertIsString(BanglaNumberConverter::bnWord(999999999));
        $this->assertFalse(BanglaNumberConverter::bnWord(1000000000));
    }

    /* ---- bnMoney ------------------------------------------------------- */

    public function testBnMoney()
    {
        $this->assertSame('শূন্য টাকা', BanglaNumberConverter::bnMoney(0));
        $this->assertSame(
            'এক হাজার তিন শত পঁয়তাল্লিশ টাকা পঞ্চাশ পয়সা',
            BanglaNumberConverter::bnMoney(1345.50)
        );
        $this->assertSame('পাঁচ টাকা', BanglaNumberConverter::bnMoney(5));
    }

    /* ---- bnMonth / Bengali month / season ----------------------------- */

    public function testBnMonth()
    {
        $this->assertSame('ডিসেম্বর', BanglaNumberConverter::bnMonth(12));
        $this->assertSame('জানুয়ারি', BanglaNumberConverter::bnMonth(1));
        $this->assertFalse(BanglaNumberConverter::bnMonth(13));
        $this->assertFalse(BanglaNumberConverter::bnMonth(0));
    }

    public function testBnBengaliMonth()
    {
        $this->assertSame('বৈশাখ', BanglaNumberConverter::bnBengaliMonth(1));
        $this->assertSame('চৈত্র', BanglaNumberConverter::bnBengaliMonth(12));
        $this->assertFalse(BanglaNumberConverter::bnBengaliMonth(13));
    }

    public function testBnSeason()
    {
        $this->assertSame('গ্রীষ্ম', BanglaNumberConverter::bnSeason(1));
        $this->assertSame('বসন্ত', BanglaNumberConverter::bnSeason(6));
        $this->assertFalse(BanglaNumberConverter::bnSeason(7));
    }

    /* ---- bnCommaLakh --------------------------------------------------- */

    public function testBnCommaLakh()
    {
        $this->assertSame('১২,১২,১২,১২১', BanglaNumberConverter::bnCommaLakh('121212121'));
    }

    /* ---- bnPercentage ------------------------------------------------- */

    public function testBnPercentage()
    {
        $this->assertSame('৭৫ শতাংশ', BanglaNumberConverter::bnPercentage(75));
        $this->assertSame('পঁচাত্তর শতাংশ', BanglaNumberConverter::bnPercentage(75, true));
    }

    /* ---- bnDay --------------------------------------------------------- */

    public function testBnDay()
    {
        $this->assertSame('রবিবার', BanglaNumberConverter::bnDay(0));
        $this->assertSame('সোমবার', BanglaNumberConverter::bnDay(1));
        $this->assertSame('সোমবার', BanglaNumberConverter::bnDay('monday'));
        $this->assertSame('শনিবার', BanglaNumberConverter::bnDay('Saturday'));
        $this->assertFalse(BanglaNumberConverter::bnDay('notaday'));
        $this->assertFalse(BanglaNumberConverter::bnDay(9));
    }

    /* ---- bnDate -------------------------------------------------------- */

    public function testBnDate()
    {
        $this->assertSame('১৫ জানুয়ারি, ২০২৪', BanglaNumberConverter::bnDate('2024-01-15'));
        $this->assertSame('১৫/০১/২০২৪', BanglaNumberConverter::bnDate('2024-01-15', 'd/m/Y'));
        $this->assertFalse(BanglaNumberConverter::bnDate('not-a-date'));
    }

    /* ---- bnTime -------------------------------------------------------- */

    public function testBnTimeDigits()
    {
        $this->assertSame('১৪:৩০', BanglaNumberConverter::bnTime('14:30'));
    }

    public function testBnTimeWords()
    {
        $this->assertSame('দুপুর দুইটা ত্রিশ মিনিট', BanglaNumberConverter::bnTime('14:30', true));
        $this->assertSame('সকাল নয়টা', BanglaNumberConverter::bnTime('09:00', true));
    }

    /* ---- bnDuration ---------------------------------------------------- */

    public function testBnDuration()
    {
        $this->assertSame('১ ঘণ্টা ১ মিনিট ৫ সেকেন্ড', BanglaNumberConverter::bnDuration(3665));
        $this->assertSame('০ সেকেন্ড', BanglaNumberConverter::bnDuration(0));
        $this->assertSame('৫ মিনিট', BanglaNumberConverter::bnDuration(300));
    }

    /* ---- bnAge --------------------------------------------------------- */

    public function testBnAge()
    {
        $this->assertSame('৩৫ বছর ২ মাস ৫ দিন', BanglaNumberConverter::bnAge('1990-01-15', '2025-03-20'));
        $this->assertSame('৩৫ বছর', BanglaNumberConverter::bnAge('1990-01-15', '2025-03-20', false));
        $this->assertFalse(BanglaNumberConverter::bnAge('2025-01-01', '2024-01-01'));
        $this->assertFalse(BanglaNumberConverter::bnAge('not-a-date'));
    }

    /* ---- bnOrdinal ----------------------------------------------------- */

    public function testBnOrdinal()
    {
        $this->assertSame('১ম', BanglaNumberConverter::bnOrdinal(1));
        $this->assertSame('৬ষ্ঠ', BanglaNumberConverter::bnOrdinal(6));
        $this->assertSame('২৫তম', BanglaNumberConverter::bnOrdinal(25));
        $this->assertFalse(BanglaNumberConverter::bnOrdinal(0));
        $this->assertFalse(BanglaNumberConverter::bnOrdinal(1.5));
    }

    /* ---- parseNum ------------------------------------------------------ */

    public function testParseNum()
    {
        $this->assertSame('12345', BanglaNumberConverter::parseNum('১২৩৪৫'));
        $this->assertSame('12,345', BanglaNumberConverter::parseNum('১২,৩৪৫'));
        $this->assertSame('100.50', BanglaNumberConverter::parseNum('১০০.৫০'));
    }
}
