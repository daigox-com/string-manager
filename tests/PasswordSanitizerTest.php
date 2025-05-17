<?php

final class PasswordSanitizerTest extends TestCase
{
    /** basic ASCII ➜ lower-case */
    public function testAsciiLower()
    {
        $this->assertSame('myp@ss123', SM::sanitizePassword('MyP@ss123'));
    }

    /** Persian digits mapped */
    public function testPersianDigits()
    {
        $this->assertSame('pass123', SM::sanitizePassword('pass۱۲۳'));
    }

    /** Arabic-Indic digits mapped */
    public function testArabicDigits()
    {
        $this->assertSame('123abc', SM::sanitizePassword('١٢٣ABC'));
    }

    /** Trim whitespace */
    public function testTrim()
    {
        $this->assertSame('abc', SM::sanitizePassword(" \t\nABC \r"));
    }

    /** Remove control char (NUL) */
    public function testControlRemoval()
    {
        $this->assertSame('abc', SM::sanitizePassword("A\0BC"));
    }

    /** Keep ZWNJ */
    public function testKeepsZwnj()
    {
        $in  = "پر\u{200C}یناز";
        $out = "پر\u{200C}یناز";
        $this->assertSame($out, SM::sanitizePassword($in));
    }

    /** Remove Bidi Override U+202E */
    public function testRemoveBidi()
    {
        $this->assertSame('abc123', SM::sanitizePassword("abc\u{202E}123"));
    }

    /** NFKC collapses full-width letters */
    public function testFullWidth()
    {
        $this->assertSame('abc', SM::sanitizePassword("ＡＢＣ"));
    }

    /** Too-long input throws */
    public function testTooLong()
    {
        $this->expectException(\InvalidArgumentException::class);
        SM::sanitizePassword(str_repeat('A', 2000));
    }

    /** Empty after cleaning throws */
    public function testEmptyAfterCleaning()
    {
        $this->expectException(\InvalidArgumentException::class);
        SM::sanitizePassword("\u{202E}");
    }
}
