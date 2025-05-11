<?php
declare(strict_types=1);

namespace Daigox\StringManager;

use Random\RandomException;

/**
 * Class StringManager
 *
 * A collection of stateless string‑handling helpers with first‑class support for
 * multilingual input (English / Persian) and UTF‑8‑safe operations.
 *
 * — Added form‑input sanitizers (sanitizePlainText, sanitizePersonName, escapeHtml, sanitizeNumericInput)
 *
 * @author  DaigoX.com
 * @license MIT
 *
 * @psalm-immutable
 */
final class StringManager
{
    /** Prevent instantiation & cloning. */
    private function __construct() {}
    private function __clone() {}

    // ──────────────────────────────── Sanitisers ────────────────────────────────

    public static function sanitizeUsername(string $input, bool $toLowerCase = true): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        $input = preg_replace('/\s+/u', '_', $input);
        $input = preg_replace('/\W/u', '', $input);
        $input = preg_replace('/_{2,}/u', '_', $input);
        $input = trim((string) $input, '_');

        return $toLowerCase ? mb_strtolower($input, 'UTF-8') : (string) $input;
    }

    /** Password sanitiser: converts Persian digits → English & lower‑cases. */
    public static function sanitizePassword(string $input): string
    {
        return mb_strtolower(self::convertPersianNumbersToEnglish($input), 'UTF-8');
    }

    /** SEO‑friendly slug generator. */
    public static function sanitizeSlug(string $input, bool $toLowerCase = true): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        $input = preg_replace('/^\d+/u', '', $input);
        $input = preg_replace('/[^\p{L}\p{N}\-]+/u', '-', $input);
        $input = preg_replace('/-{2,}/u', '-', $input);
        $input = trim((string) $input, '-');

        return $toLowerCase ? mb_strtolower($input, 'UTF-8') : (string) $input;
    }

    /** Converts Persian digits to English digits within a string. */
    public static function convertPersianNumbersToEnglish(string $input): string
    {
        static $map = ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9'];
        return strtr($input, $map);
    }

    // ────────────────── New: Generic form‑input sanitizers ────────────────────

    /**
     * Removes tags, control chars, condenses whitespace, enforces length.
     */
    public static function sanitizePlainText(string $input, int $maxLength = 255): string
    {
        $input = trim($input);
        $input = self::convertPersianNumbersToEnglish($input);
        $input = preg_replace('/[\x00-\x1F\x7F]/u', '', $input); // control chars
        $input = strip_tags($input);
        $input = preg_replace('/\s+/u', ' ', $input);
        return mb_substr($input, 0, $maxLength, 'UTF-8');
    }

    /**
     * Sanitises human names – letters, spaces, apostrophes & hyphens only.
     */
    public static function sanitizePersonName(string $input, int $maxLength = 100): string
    {
        $input = self::sanitizePlainText($input, $maxLength);
        $input = preg_replace("/[^\p{L}\s'\-]/u", '', $input);
        $input = preg_replace('/\s{2,}/u', ' ', $input);
        return trim($input);
    }

    /**
     * Escapes a string for safe insertion into HTML context.
     */
    public static function escapeHtml(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Allows only digits (Persian & English) – useful for numeric form fields.
     */
    public static function sanitizeNumericInput(string $input): string
    {
        $input = self::convertPersianNumbersToEnglish($input);
        return preg_replace('/[^0-9]/', '', $input);
    }

    // ───────────────────────────── Condition checks ─────────────────────────────

    public static function isInArray(?string $input, array $array): bool
    {
        return $input !== null && in_array($input, $array, true);
    }

    public static function containsArray(string $input, array $array, bool $all = false): bool
    {
        $foundCount = 0;
        foreach ($array as $needle) {
            if (str_contains($input, $needle)) {
                ++$foundCount;
                if (!$all) return true;
            } elseif ($all) {
                return false;
            }
        }
        return $all ? $foundCount === count($array) : false;
    }

    public static function containsPattern(string $input, string $pattern): bool
    {
        return (bool) preg_match($pattern, $input);
    }

    public static function containsText(string $input, string $searchTerm): bool
    {
        return str_contains($input, $searchTerm);
    }

    // ────────────────────────────────── Filters ─────────────────────────────────

    public static function filterEnglishAlphabetCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^a-z]/i' : '/[^a-z ]/i';
        return trim((string) preg_replace($pattern, '', $input));
    }

    public static function filterEnglishAlphanumericCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^a-z0-9]/i' : '/[^a-z0-9 ]/i';
        return trim((string) preg_replace($pattern, '', $input));
    }

    public static function filterNumericCharacters(string $input, bool $removeSpaces = true): string
    {
        $pattern = $removeSpaces ? '/[^\d]/' : '/[^\d ]/';
        return trim((string) preg_replace($pattern, '', $input));
    }

    // ──────────────────────── Matching / Validation ────────────────────────────

    public static function matchPattern(string $input, string $pattern): bool
    {
        return (bool) preg_match($pattern, $input);
    }

    public static function validateUuid(string $input): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $input);
    }

    // ──────────────────────────── String mutations ─────────────────────────────

    public static function removeCharacters(string $input, string|array $charactersToRemove): string
    {
        $chars = is_array($charactersToRemove) ? implode('', $charactersToRemove) : $charactersToRemove;
        return (string) preg_replace('/[' . preg_quote($chars, '/') . ']/u', '', $input);
    }

    public static function removeSpaces(string $input): string
    {
        return str_replace(' ', '', $input);
    }

    public static function reverse(string $input): string
    {
        return strrev($input);
    }

    public static function truncate(string $input, int $maxChars, string $ellipsis = '...'): string
    {
        if ($maxChars < 1) return '';
        if (mb_strlen($input, 'UTF-8') <= $maxChars) return $input;
        $truncated = rtrim(mb_substr($input, 0, $maxChars, 'UTF-8'));
        return $truncated . $ellipsis;
    }

    public static function capitalizeFirstLetter(string $input): string
    {
        return mb_convert_case($input, MB_CASE_TITLE, 'UTF-8');
    }

    public static function convertToCamelCase(string $input): string
    {
        $input = preg_replace('/[^A-Za-z0-9]+/u', ' ', mb_strtolower($input, 'UTF-8'));
        $input = str_replace(' ', '', ucwords($input));
        return lcfirst($input);
    }

    public static function convertToSnakeCase(string $input): string
    {
        $input = preg_replace('/\s+/u', '', ucwords($input));
        return mb_strtolower((string) preg_replace('/(?<!^)[A-Z]/u', '_$0', $input), 'UTF-8');
    }

    public static function convertToKebabCase(string $input): string
    {
        $input = preg_replace('/\s+/u', '-', ucwords($input));
        $input = preg_replace('/(?<!^)[A-Z]/u', '-$0', $input);
        return trim(mb_strtolower($input, 'UTF-8'), '-');
    }

    public static function convertToTitleCase(string $input): string
    {
        return mb_convert_case($input, MB_CASE_TITLE, 'UTF-8');
    }

    // ────────────────────────────── Generators ────────────────────────────────

    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxIndex   = strlen($characters) - 1;
        $result     = '';
        for ($i = 0; $i < $length; ++$i) {
            $result .= $characters[random_int(0, $maxIndex)];
        }
        return $result;
    }

    /** @throws RandomException */
    public static function generateUuidV4(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);
        $hex     = bin2hex($data);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));
    }

    // ─────────────────────────── Parsing & detection ───────────────────────────

    public static function extractEmails(string $input): array
    {
        preg_match_all('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $input, $m);
        return $m[0];
    }

    public static function identifyLoginFieldType(string $input): string
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) return 'email';
        return preg_match('/^\+?\d{10,15}$/', $input) ? 'phone_number' : 'username';
    }

    // ───────────────────────────── Formatting helpers ──────────────────────────

    public static function obfuscateEmail(string $email): string
    {
        return strtr($email, ['@' => ' at ', '.' => ' dot ']);
    }

    public static function formatAsBold(string ...$strings): string
    {
        return '<b>' . implode(' ', $strings) . '</b>';
    }

    public static function formatAsItalic(string ...$strings): string
    {
        return '<i>' . implode(' ', $strings) . '</i>';
    }

    public static function formatAsUnderline(string ...$strings): string
    {
        return '<u>' . implode(' ', $strings) . '</u>';
    }

    public static function formatAsStrike(string ...$strings): string
    {
        return '<s>' . implode(' ', $strings) . '</s>';
    }

    public static function createLink(string $text, string $url): string
    {
        return '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '">' . htmlspecialchars($text) . '</a>';
    }

    public static function formatAsCode(string $text): string
    {
        return '<code>' . htmlspecialchars($text) . '</code>';
    }

    public static function formatAsPre(string ...$strings): string
    {
        return '<pre>' . implode(' ', $strings) . '</pre>';
    }

    public static function formatAsBlockquote(string ...$strings): string
    {
        return '<blockquote>' . implode(' ', $strings) . '</blockquote>';
    }

    // ───────────────────────────── Composition helpers ─────────────────────────

    public static function concatenate(string ...$strings): string
    {
        return implode('', $strings);
    }

    public static function concatenateWithSpace(string ...$strings): string
    {
        return implode(' ', $strings);
    }

    public static function concatenateWithNewLine(string ...$strings): string
    {
        return implode(' ', $strings) . PHP_EOL;
    }

    public static function concatenateWithDoubleNewLine(string ...$strings): string
    {
        return implode(' ', $strings) . PHP_EOL . PHP_EOL;
    }

    public static function concatenateWithLeadingNewLine(string ...$strings): string
    {
        return PHP_EOL . implode(' ', $strings);
    }

    public static function applyEach(array $array, callable $callable, ?int $limit = null): string
    {
        $result = array_map($callable, $array);
        if ($limit !== null) $result = array_slice($result, 0, $limit);
        return implode(' ', $result);
    }

    public static function repeatString(string $text, int $times): string
    {
        return str_repeat($text, $times);
    }

    public static function repeatStringWithNewLine(string $text, int $times): string
    {
        return rtrim(str_repeat($text . PHP_EOL, $times), PHP_EOL);
    }

    // ───────────────────────────── New line helpers ───────────────────────────

    public static function singleNewLine(): string
    {
        return PHP_EOL;
    }

    public static function doubleNewLine(): string
    {
        return PHP_EOL . PHP_EOL;
    }

    public static function joinWithNewLine(array $array): string
    {
        return implode(PHP_EOL, $array);
    }

    // ───────────────────────────── Miscellaneous ──────────────────────────────

    public static function levenshteinDistance(string $input1, string $input2): int
    {
        return levenshtein($input1, $input2);
    }

    public static function similarTextPercentage(string $input1, string $input2): float
    {
        similar_text($input1, $input2, $percent);
        return round($percent, 1);
    }

    public static function conditionalOutput(bool $condition, string $trueValue, string $falseValue = ''): string
    {
        return $condition ? $trueValue : $falseValue;
    }
}
