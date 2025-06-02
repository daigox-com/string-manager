<?php

namespace Daigox\StringManager;

use DateTime;
use InvalidArgumentException;
use Normalizer;
use Random\RandomException;
use Transliterator;

/**
 * Ultimate String Manager
 *
 * The most comprehensive string manipulation library with full UTF-8 support,
 * advanced text processing, multilingual capabilities, and extensive utilities.
 *
 * @author DaigoX.com
 * @license MIT
 * @version 2.0.0
 */
class StringManager
{
    // Encoding
    const DEFAULT_ENCODING = 'UTF-8';

    // Common regex patterns
    const PATTERN_EMAIL = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    const PATTERN_URL = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
    const PATTERN_DOMAIN = '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i';
    const PATTERN_IP = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
    const PATTERN_IPV6 = '/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/';
    const PATTERN_MAC = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';
    const PATTERN_UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
    const PATTERN_PHONE = '/^\+?[1-9]\d{1,14}$/';
    const PATTERN_CREDIT_CARD = '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})$/';
    const PATTERN_HEX_COLOR = '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/i';
    const PATTERN_RGB_COLOR = '/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i';
    const PATTERN_SLUG = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
    const PATTERN_USERNAME = '/^[a-zA-Z0-9_]{3,20}$/';
    const PATTERN_PASSWORD_STRONG = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    const PATTERN_HASHTAG = '/#[a-zA-Z0-9_]+/';
    const PATTERN_MENTION = '/@[a-zA-Z0-9_]+/';

    // Prevent instantiation
    private function __construct()
    {
    }

    // ============================= Core String Operations =============================

    /**
     * Get string length (UTF-8 safe)
     */
    public static function length(string $string): int
    {
        return mb_strlen($string, self::DEFAULT_ENCODING);
    }

    /**
     * Get byte length
     */
    public static function byteLength(string $string): int
    {
        return strlen($string);
    }

    /**
     * Substring extraction (UTF-8 safe)
     */
    public static function substring(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, self::DEFAULT_ENCODING);
    }

    /**
     * Get character at position
     */
    public static function charAt(string $string, int $position): string
    {
        return mb_substr($string, $position, 1, self::DEFAULT_ENCODING);
    }

    /**
     * String position search (UTF-8 safe)
     */
    public static function indexOf(string $haystack, string $needle, int $offset = 0): int|false
    {
        return mb_strpos($haystack, $needle, $offset, self::DEFAULT_ENCODING);
    }

    /**
     * Last position search (UTF-8 safe)
     */
    public static function lastIndexOf(string $haystack, string $needle, int $offset = 0): int|false
    {
        return mb_strrpos($haystack, $needle, $offset, self::DEFAULT_ENCODING);
    }

    /**
     * Count occurrences of substring
     */
    public static function countOccurrences(string $haystack, string $needle): int
    {
        return mb_substr_count($haystack, $needle, self::DEFAULT_ENCODING);
    }

    // ============================= Case Conversion =============================

    /**
     * Convert to lowercase (UTF-8 safe)
     */
    public static function lower(string $string): string
    {
        return mb_strtolower($string, self::DEFAULT_ENCODING);
    }

    /**
     * Convert to uppercase (UTF-8 safe)
     */
    public static function upper(string $string): string
    {
        return mb_strtoupper($string, self::DEFAULT_ENCODING);
    }

    /**
     * Convert to title case
     */
    public static function title(string $string): string
    {
        return mb_convert_case($string, MB_CASE_TITLE, self::DEFAULT_ENCODING);
    }

    /**
     * Capitalize first letter
     */
    public static function capitalize(string $string): string
    {
        if (empty($string)) return $string;
        return self::upper(self::substring($string, 0, 1)) . self::substring($string, 1);
    }

    /**
     * Capitalize each word
     */
    public static function capitalizeWords(string $string): string
    {
        return preg_replace_callback('/\b\w/u', fn($m) => self::upper($m[0]), $string);
    }

    /**
     * Convert to sentence case
     */
    public static function sentence(string $string): string
    {
        $string = self::lower($string);
        return preg_replace_callback('/([.!?]\s*)(\w)/u', fn($m) => $m[1] . self::upper($m[2]), self::capitalize($string));
    }

    /**
     * Swap case
     */
    public static function swapCase(string $string): string
    {
        return preg_replace_callback('/\p{L}/u', function ($match) {
            $char = $match[0];
            return mb_strtolower($char, self::DEFAULT_ENCODING) === $char
                ? mb_strtoupper($char, self::DEFAULT_ENCODING)
                : mb_strtolower($char, self::DEFAULT_ENCODING);
        }, $string);
    }

    // ============================= Case Formats =============================

    /**
     * Convert to camelCase
     */
    public static function camelCase(string $string): string
    {
        $string = preg_replace('/[^a-zA-Z0-9]+/u', ' ', $string);
        $string = trim($string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return lcfirst($string);
    }

    /**
     * Convert to PascalCase (StudlyCase)
     */
    public static function pascalCase(string $string): string
    {
        return ucfirst(self::camelCase($string));
    }

    /**
     * Convert to snake_case
     */
    public static function snakeCase(string $string): string
    {
        $string = preg_replace('/\s+/u', '', ucwords($string));
        $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
        return self::lower($string);
    }

    /**
     * Convert to kebab-case (dash-case)
     */
    public static function kebabCase(string $string): string
    {
        return str_replace('_', '-', self::snakeCase($string));
    }

    /**
     * Convert to CONSTANT_CASE
     */
    public static function constantCase(string $string): string
    {
        return self::upper(self::snakeCase($string));
    }

    /**
     * Convert to dot.case
     */
    public static function dotCase(string $string): string
    {
        return str_replace('_', '.', self::snakeCase($string));
    }

    /**
     * Convert to Train-Case (HTTP-Header-Case)
     */
    public static function trainCase(string $string): string
    {
        $string = self::pascalCase($string);
        return preg_replace('/(?<!^)(?=[A-Z])/u', '-', $string);
    }

    /**
     * Convert to colon:case
     */
    public static function colonCase(string $string): string
    {
        return str_replace('_', ':', self::snakeCase($string));
    }

    /**
     * Convert to path/case
     */
    public static function pathCase(string $string): string
    {
        return str_replace('_', '/', self::snakeCase($string));
    }

    // ============================= Trimming & Padding =============================

    /**
     * Trim whitespace or specified characters
     */
    public static function trim(string $string, string $characters = " \t\n\r\0\x0B"): string
    {
        return trim($string, $characters);
    }

    /**
     * Left trim
     */
    public static function trimLeft(string $string, string $characters = " \t\n\r\0\x0B"): string
    {
        return ltrim($string, $characters);
    }

    /**
     * Right trim
     */
    public static function trimRight(string $string, string $characters = " \t\n\r\0\x0B"): string
    {
        return rtrim($string, $characters);
    }

    /**
     * Trim consecutive spaces
     */
    public static function trimConsecutiveSpaces(string $string): string
    {
        return preg_replace('/\s+/', ' ', trim($string));
    }

    /**
     * Pad string
     */
    public static function pad(string $string, int $length, string $padString = ' ', int $padType = STR_PAD_RIGHT): string
    {
        return str_pad($string, $length, $padString, $padType);
    }

    /**
     * Pad left
     */
    public static function padLeft(string $string, int $length, string $padString = ' '): string
    {
        return self::pad($string, $length, $padString, STR_PAD_LEFT);
    }

    /**
     * Pad right
     */
    public static function padRight(string $string, int $length, string $padString = ' '): string
    {
        return self::pad($string, $length, $padString);
    }

    /**
     * Pad both sides
     */
    public static function padBoth(string $string, int $length, string $padString = ' '): string
    {
        return self::pad($string, $length, $padString, STR_PAD_BOTH);
    }

    /**
     * Zero pad number
     */
    public static function zeroPad(string|int $number, int $length): string
    {
        return str_pad((string)$number, $length, '0', STR_PAD_LEFT);
    }

    // ============================= String Manipulation =============================

    /**
     * Repeat string
     */
    public static function repeat(string $string, int $times): string
    {
        return str_repeat($string, max(0, $times));
    }

    /**
     * Reverse string (UTF-8 safe)
     */
    public static function reverse(string $string): string
    {
        preg_match_all('/./us', $string, $matches);
        return implode('', array_reverse($matches[0]));
    }

    /**
     * Shuffle string characters
     */
    public static function shuffle(string $string): string
    {
        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        shuffle($chars);
        return implode('', $chars);
    }

    /**
     * Replace string occurrences
     */
    public static function replace(string $search, string $replace, string $subject, int $limit = -1): string
    {
        if ($limit === -1) {
            return str_replace($search, $replace, $subject);
        }

        $pos = 0;
        $count = 0;
        while (($pos = strpos($subject, $search, $pos)) !== false && $count < $limit) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
            $pos += strlen($replace);
            $count++;
        }

        return $subject;
    }

    /**
     * Replace multiple strings
     */
    public static function replaceMany(array $replacements, string $subject): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $subject);
    }

    /**
     * Replace using regex
     */
    public static function replaceRegex(string $pattern, string $replacement, string $subject, int $limit = -1): string
    {
        return preg_replace($pattern, $replacement, $subject, $limit);
    }

    /**
     * Replace using callback
     */
    public static function replaceCallback(string $pattern, callable $callback, string $subject, int $limit = -1): string
    {
        return preg_replace_callback($pattern, $callback, $subject, $limit);
    }

    /**
     * Remove string occurrences
     */
    public static function remove(string|array $search, string $subject): string
    {
        return str_replace($search, '', $subject);
    }

    /**
     * Insert string at position
     */
    public static function insert(string $string, string $insert, int $position): string
    {
        return self::substring($string, 0, $position) . $insert . self::substring($string, $position);
    }

    /**
     * Wrap string
     */
    public static function wrap(string $string, string $before, ?string $after = null): string
    {
        $after = $after ?? $before;
        return $before . $string . $after;
    }

    /**
     * Unwrap string
     */
    public static function unwrap(string $string, string $before, ?string $after = null): string
    {
        $after = $after ?? $before;

        if (self::startsWith($string, $before) && self::endsWith($string, $after)) {
            $string = self::substring($string, self::length($before));
            $string = self::substring($string, 0, -self::length($after));
        }

        return $string;
    }

    /**
     * Quote string
     */
    public static function quote(string $string, string $style = '"'): string
    {
        return self::wrap($string, $style);
    }

    /**
     * Unquote string
     */
    public static function unquote(string $string): string
    {
        if (preg_match('/^(["\'])(.*)\\1$/', $string, $matches)) {
            return $matches[2];
        }
        return $string;
    }

    // ============================= Truncation =============================

    /**
     * Truncate string
     */
    public static function truncate(string $string, int $length, string $suffix = '...'): string
    {
        if (self::length($string) <= $length) {
            return $string;
        }

        return self::substring($string, 0, $length - self::length($suffix)) . $suffix;
    }

    /**
     * Truncate preserving words
     */
    public static function truncateWords(string $string, int $words, string $suffix = '...'): string
    {
        $wordArray = explode(' ', $string);

        if (count($wordArray) <= $words) {
            return $string;
        }

        return implode(' ', array_slice($wordArray, 0, $words)) . $suffix;
    }

    /**
     * Truncate from middle
     */
    public static function truncateMiddle(string $string, int $length, string $separator = '...'): string
    {
        if (self::length($string) <= $length) {
            return $string;
        }

        $sepLength = self::length($separator);
        $charsToShow = $length - $sepLength;
        $frontChars = (int)ceil($charsToShow / 2);
        $backChars = (int)floor($charsToShow / 2);

        return self::substring($string, 0, $frontChars) . $separator . self::substring($string, -$backChars);
    }

    /**
     * Truncate on word boundary
     */
    public static function truncateOnWordBoundary(string $string, int $length, string $suffix = '...'): string
    {
        if (self::length($string) <= $length) {
            return $string;
        }

        $truncated = self::substring($string, 0, $length - self::length($suffix));
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = self::substring($truncated, 0, $lastSpace);
        }

        return $truncated . $suffix;
    }

    // ============================= String Analysis =============================

    /**
     * Check if string contains substring
     */
    public static function contains(string $haystack, string $needle, bool $caseSensitive = true): bool
    {
        if ($caseSensitive) {
            return str_contains($haystack, $needle);
        }

        return mb_stripos($haystack, $needle, 0, self::DEFAULT_ENCODING) !== false;
    }

    /**
     * Check if string contains any of the needles
     */
    public static function containsAny(string $haystack, array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (self::contains($haystack, $needle, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if string contains all needles
     */
    public static function containsAll(string $haystack, array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (!self::contains($haystack, $needle, $caseSensitive)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if string starts with substring
     */
    public static function startsWith(string $haystack, string $needle, bool $caseSensitive = true): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($caseSensitive) {
            return str_starts_with($haystack, $needle);
        }

        return mb_stripos($haystack, $needle, 0, self::DEFAULT_ENCODING) === 0;
    }

    /**
     * Check if string starts with any of the needles
     */
    public static function startsWithAny(string $haystack, array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (self::startsWith($haystack, $needle, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if string ends with substring
     */
    public static function endsWith(string $haystack, string $needle, bool $caseSensitive = true): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($caseSensitive) {
            return str_ends_with($haystack, $needle);
        }

        $haystack = self::lower($haystack);
        $needle = self::lower($needle);

        return str_ends_with($haystack, $needle);
    }

    /**
     * Check if string ends with any of the needles
     */
    public static function endsWithAny(string $haystack, array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (self::endsWith($haystack, $needle, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if string matches pattern
     */
    public static function matches(string $string, string $pattern): bool
    {
        return preg_match($pattern, $string) === 1;
    }

    /**
     * Get all matches
     */
    public static function matchAll(string $string, string $pattern): array
    {
        preg_match_all($pattern, $string, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Check if string is empty
     */
    public static function isEmpty(string $string): bool
    {
        return $string === '';
    }

    /**
     * Check if string is blank (empty or whitespace only)
     */
    public static function isBlank(string $string): bool
    {
        return trim($string) === '';
    }

    /**
     * Check if string is numeric
     */
    public static function isNumeric(string $string): bool
    {
        return is_numeric($string);
    }

    /**
     * Check if string is integer
     */
    public static function isInteger(string $string): bool
    {
        return preg_match('/^-?\d+$/', $string) === 1;
    }

    /**
     * Check if string is float
     */
    public static function isFloat(string $string): bool
    {
        return is_numeric($string) && str_contains($string, '.');
    }

    /**
     * Check if string is alphabetic
     */
    public static function isAlpha(string $string): bool
    {
        return preg_match('/^\p{L}+$/u', $string) === 1;
    }

    /**
     * Check if string is alphanumeric
     */
    public static function isAlphanumeric(string $string): bool
    {
        return preg_match('/^[\p{L}\p{N}]+$/u', $string) === 1;
    }

    /**
     * Check if string is lowercase
     */
    public static function isLower(string $string): bool
    {
        return $string === self::lower($string);
    }

    /**
     * Check if string is uppercase
     */
    public static function isUpper(string $string): bool
    {
        return $string === self::upper($string);
    }

    /**
     * Check if string is title case
     */
    public static function isTitle(string $string): bool
    {
        return $string === self::title($string);
    }

    /**
     * Check if string is valid JSON
     */
    public static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Check if string is valid XML
     */
    public static function isXml(string $string): bool
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($string);
        return $doc !== false;
    }

    /**
     * Check if string is valid HTML
     */
    public static function isHtml(string $string): bool
    {
        return $string !== strip_tags($string);
    }

    /**
     * Check if string is valid base64
     */
    public static function isBase64(string $string): bool
    {
        return base64_encode(base64_decode($string, true)) === $string;
    }

    /**
     * Check if string is valid hex
     */
    public static function isHex(string $string): bool
    {
        return preg_match('/^[0-9a-fA-F]+$/', $string) === 1;
    }

    /**
     * Check if string is binary
     */
    public static function isBinary(string $string): bool
    {
        return preg_match('/^[01]+$/', $string) === 1;
    }

    /**
     * Check if string is ASCII
     */
    public static function isAscii(string $string): bool
    {
        return mb_check_encoding($string, 'ASCII');
    }

    /**
     * Check if string is UTF-8
     */
    public static function isUtf8(string $string): bool
    {
        return mb_check_encoding($string, 'UTF-8');
    }

    /**
     * Check if string is palindrome
     */
    public static function isPalindrome(string $string, bool $ignoreCase = true, bool $ignoreSpaces = true): bool
    {
        if ($ignoreSpaces) {
            $string = str_replace(' ', '', $string);
        }

        if ($ignoreCase) {
            $string = self::lower($string);
        }

        return $string === self::reverse($string);
    }

    /**
     * Check if string is anagram
     */
    public static function isAnagram(string $string1, string $string2, bool $ignoreCase = true, bool $ignoreSpaces = true): bool
    {
        if ($ignoreSpaces) {
            $string1 = str_replace(' ', '', $string1);
            $string2 = str_replace(' ', '', $string2);
        }

        if ($ignoreCase) {
            $string1 = self::lower($string1);
            $string2 = self::lower($string2);
        }

        $chars1 = str_split($string1);
        $chars2 = str_split($string2);

        sort($chars1);
        sort($chars2);

        return $chars1 === $chars2;
    }

    // ============================= Extraction =============================

    /**
     * Extract substring between delimiters
     */
    public static function between(string $string, string $start, string $end, bool $greedy = false): ?string
    {
        $startPos = self::indexOf($string, $start);
        if ($startPos === false) {
            return null;
        }

        $startPos += self::length($start);

        if ($greedy) {
            $endPos = self::lastIndexOf($string, $end, $startPos);
        } else {
            $endPos = self::indexOf($string, $end, $startPos);
        }

        if ($endPos === false) {
            return null;
        }

        return self::substring($string, $startPos, $endPos - $startPos);
    }

    /**
     * Extract all substrings between delimiters
     */
    public static function betweenAll(string $string, string $start, string $end): array
    {
        $results = [];
        $offset = 0;

        while (($startPos = self::indexOf($string, $start, $offset)) !== false) {
            $startPos += self::length($start);
            $endPos = self::indexOf($string, $end, $startPos);

            if ($endPos === false) {
                break;
            }

            $results[] = self::substring($string, $startPos, $endPos - $startPos);
            $offset = $endPos + self::length($end);
        }

        return $results;
    }

    /**
     * Extract substring before delimiter
     */
    public static function before(string $string, string $delimiter): string
    {
        $pos = self::indexOf($string, $delimiter);

        if ($pos === false) {
            return $string;
        }

        return self::substring($string, 0, $pos);
    }

    /**
     * Extract substring before last occurrence
     */
    public static function beforeLast(string $string, string $delimiter): string
    {
        $pos = self::lastIndexOf($string, $delimiter);

        if ($pos === false) {
            return $string;
        }

        return self::substring($string, 0, $pos);
    }

    /**
     * Extract substring after delimiter
     */
    public static function after(string $string, string $delimiter): string
    {
        $pos = self::indexOf($string, $delimiter);

        if ($pos === false) {
            return '';
        }

        return self::substring($string, $pos + self::length($delimiter));
    }

    /**
     * Extract substring after last occurrence
     */
    public static function afterLast(string $string, string $delimiter): string
    {
        $pos = self::lastIndexOf($string, $delimiter);

        if ($pos === false) {
            return '';
        }

        return self::substring($string, $pos + self::length($delimiter));
    }

    // ============================= Splitting & Joining =============================

    /**
     * Split string by delimiter
     */
    public static function split(string $string, string $delimiter, int $limit = PHP_INT_MAX): array
    {
        if ($delimiter === '') {
            return self::toArray($string);
        }

        return explode($delimiter, $string, $limit);
    }

    /**
     * Split string by regex
     */
    public static function splitRegex(string $string, string $pattern, int $limit = -1, int $flags = 0): array
    {
        return preg_split($pattern, $string, $limit, $flags | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Split string into array of characters
     */
    public static function toArray(string $string): array
    {
        return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Split string into chunks
     */
    public static function chunk(string $string, int $length): array
    {
        if ($length <= 0) {
            throw new InvalidArgumentException('Chunk length must be greater than 0');
        }

        $chunks = [];
        $stringLength = self::length($string);

        for ($i = 0; $i < $stringLength; $i += $length) {
            $chunks[] = self::substring($string, $i, $length);
        }

        return $chunks;
    }

    /**
     * Split string into lines
     */
    public static function lines(string $string, bool $removeEmpty = false): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $string);

        if ($removeEmpty) {
            return array_filter($lines, fn($line) => $line !== '');
        }

        return $lines;
    }

    /**
     * Split string into words
     */
    public static function words(string $string, string $pattern = '/\s+/'): array
    {
        return preg_split($pattern, trim($string), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Split string into sentences
     */
    public static function sentences(string $string): array
    {
        return preg_split('/(?<=[.!?])\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Join array elements
     */
    public static function join(array $pieces, string $glue = ''): string
    {
        return implode($glue, $pieces);
    }

    /**
     * Join with different last separator
     */
    public static function joinLast(array $pieces, string $glue = ', ', string $lastGlue = ' and '): string
    {
        if (count($pieces) <= 1) {
            return implode('', $pieces);
        }

        $last = array_pop($pieces);
        return implode($glue, $pieces) . $lastGlue . $last;
    }

    // ============================= Encoding & Decoding =============================

    /**
     * Encode to base64
     */
    public static function base64Encode(string $string): string
    {
        return base64_encode($string);
    }

    /**
     * Decode from base64
     */
    public static function base64Decode(string $string): string|false
    {
        return base64_decode($string, true);
    }

    /**
     * URL encode
     */
    public static function urlEncode(string $string): string
    {
        return urlencode($string);
    }

    /**
     * URL decode
     */
    public static function urlDecode(string $string): string
    {
        return urldecode($string);
    }

    /**
     * Raw URL encode
     */
    public static function rawUrlEncode(string $string): string
    {
        return rawurlencode($string);
    }

    /**
     * Raw URL decode
     */
    public static function rawUrlDecode(string $string): string
    {
        return rawurldecode($string);
    }

    /**
     * HTML encode
     */
    public static function htmlEncode(string $string, int $flags = ENT_QUOTES | ENT_SUBSTITUTE): string
    {
        return htmlspecialchars($string, $flags, self::DEFAULT_ENCODING);
    }

    /**
     * HTML decode
     */
    public static function htmlDecode(string $string, int $flags = ENT_QUOTES): string
    {
        return htmlspecialchars_decode($string, $flags);
    }

    /**
     * HTML entity encode
     */
    public static function htmlEntityEncode(string $string, int $flags = ENT_QUOTES): string
    {
        return htmlentities($string, $flags, self::DEFAULT_ENCODING);
    }

    /**
     * HTML entity decode
     */
    public static function htmlEntityDecode(string $string, int $flags = ENT_QUOTES): string
    {
        return html_entity_decode($string, $flags, self::DEFAULT_ENCODING);
    }

    /**
     * Escape for JavaScript
     */
    public static function escapeJs(string $string): string
    {
        return strtr($string, [
            '\\' => '\\\\',
            '"' => '\\"',
            "'" => "\\'",
            "\n" => '\\n',
            "\r" => '\\r',
            "\t" => '\\t',
            "\x08" => '\\b',
            "\x0C" => '\\f'
        ]);
    }

    /**
     * Escape for regex
     */
    public static function escapeRegex(string $string, string $delimiter = '/'): string
    {
        return preg_quote($string, $delimiter);
    }

    /**
     * Escape for shell
     */
    public static function escapeShell(string $string): string
    {
        return escapeshellarg($string);
    }

    /**
     * Escape for SQL LIKE
     */
    public static function escapeLike(string $string, string $escape = '\\'): string
    {
        return strtr($string, [
            $escape => $escape . $escape,
            '%' => $escape . '%',
            '_' => $escape . '_'
        ]);
    }

    /**
     * Convert to hex
     */
    public static function toHex(string $string): string
    {
        return bin2hex($string);
    }

    /**
     * Convert from hex
     */
    public static function fromHex(string $hex): string|false
    {
        return hex2bin($hex);
    }

    /**
     * Convert to binary
     */
    public static function toBinary(string $string): string
    {
        $binary = '';
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $binary .= sprintf('%08b', ord($string[$i]));
        }

        return $binary;
    }

    /**
     * Convert from binary
     */
    public static function fromBinary(string $binary): string
    {
        $string = '';
        $chunks = str_split($binary, 8);

        foreach ($chunks as $chunk) {
            $string .= chr(bindec($chunk));
        }

        return $string;
    }

    /**
     * Convert to ASCII
     */
    public static function toAscii(string $string): string
    {
        return iconv(self::DEFAULT_ENCODING, 'ASCII//TRANSLIT//IGNORE', $string);
    }

    /**
     * JSON encode
     */
    public static function jsonEncode($data, int $flags = 0, int $depth = 512): string|false
    {
        return json_encode($data, $flags, $depth);
    }

    /**
     * JSON decode
     */
    public static function jsonDecode(string $json, bool $associative = true, int $depth = 512, int $flags = 0)
    {
        return json_decode($json, $associative, $depth, $flags);
    }

    // ============================= Hashing & Encryption =============================

    /**
     * Generate MD5 hash
     */
    public static function md5(string $string, bool $binary = false): string
    {
        return md5($string, $binary);
    }

    /**
     * Generate SHA1 hash
     */
    public static function sha1(string $string, bool $binary = false): string
    {
        return sha1($string, $binary);
    }

    /**
     * Generate SHA256 hash
     */
    public static function sha256(string $string, bool $binary = false): string
    {
        return hash('sha256', $string, $binary);
    }

    /**
     * Generate SHA512 hash
     */
    public static function sha512(string $string, bool $binary = false): string
    {
        return hash('sha512', $string, $binary);
    }

    /**
     * Generate hash with algorithm
     */
    public static function hash(string $string, string $algorithm, bool $binary = false): string
    {
        return hash($algorithm, $string, $binary);
    }

    /**
     * Generate HMAC
     */
    public static function hmac(string $string, string $key, string $algorithm = 'sha256', bool $binary = false): string
    {
        return hash_hmac($algorithm, $string, $key, $binary);
    }

    /**
     * Generate CRC32
     */
    public static function crc32(string $string): int
    {
        return crc32($string);
    }

    /**
     * Generate password hash
     */
    public static function passwordHash(string $password, ?string $algorithm = PASSWORD_DEFAULT, array $options = []): string
    {
        return password_hash($password, $algorithm, $options);
    }

    /**
     * Verify password hash
     */
    public static function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // ============================= Validation =============================

    /**
     * Validate email
     */
    public static function isEmail(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     */
    public static function isUrl(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate domain
     */
    public static function isDomain(string $string): bool
    {
        return preg_match(self::PATTERN_DOMAIN, $string) === 1;
    }

    /**
     * Validate IP address
     */
    public static function isIp(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate IPv4 address
     */
    public static function isIpv4(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Validate IPv6 address
     */
    public static function isIpv6(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Validate MAC address
     */
    public static function isMac(string $string): bool
    {
        return preg_match(self::PATTERN_MAC, $string) === 1;
    }

    /**
     * Validate UUID
     */
    public static function isUuid(string $string): bool
    {
        return preg_match(self::PATTERN_UUID, $string) === 1;
    }

    /**
     * Validate phone number
     */
    public static function isPhone(string $string): bool
    {
        return preg_match(self::PATTERN_PHONE, $string) === 1;
    }

    /**
     * Validate credit card
     */
    public static function isCreditCard(string $string): bool
    {
        $string = preg_replace('/\D/', '', $string);

        if (!preg_match(self::PATTERN_CREDIT_CARD, $string)) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int)$string[$length - $i - 1];

            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        return $sum % 10 === 0;
    }

    /**
     * Validate hex color
     */
    public static function isHexColor(string $string): bool
    {
        return preg_match(self::PATTERN_HEX_COLOR, $string) === 1;
    }

    /**
     * Validate RGB color
     */
    public static function isRgbColor(string $string): bool
    {
        return preg_match(self::PATTERN_RGB_COLOR, $string) === 1;
    }

    /**
     * Validate slug
     */
    public static function isSlug(string $string): bool
    {
        return preg_match(self::PATTERN_SLUG, $string) === 1;
    }

    /**
     * Validate username
     */
    public static function isUsername(string $string): bool
    {
        return preg_match(self::PATTERN_USERNAME, $string) === 1;
    }

    /**
     * Validate strong password
     */
    public static function isStrongPassword(string $string): bool
    {
        return preg_match(self::PATTERN_PASSWORD_STRONG, $string) === 1;
    }

    /**
     * Validate date
     */
    public static function isDate(string $string, string $format = 'Y-m-d'): bool
    {
        $date = DateTime::createFromFormat($format, $string);
        return $date && $date->format($format) === $string;
    }

    /**
     * Validate time
     */
    public static function isTime(string $string, string $format = 'H:i:s'): bool
    {
        return self::isDate($string, $format);
    }

    /**
     * Validate datetime
     */
    public static function isDateTime(string $string, string $format = 'Y-m-d H:i:s'): bool
    {
        return self::isDate($string, $format);
    }

// ============================= Sanitization =============================

    /**
     * Sanitize for filename
     */
    public static function sanitizeFilename(string $filename, string $replacement = '_'): string
    {
        $filename = preg_replace('/[^\w\s\-_~,;\[\]().]/u', $replacement, $filename);
        $filename = preg_replace('/\s+/', $replacement, $filename);
        $filename = preg_replace('/' . preg_quote($replacement, '/') . '+/', $replacement, $filename);
        return trim($filename, $replacement . '.');
    }

    /**
     * Sanitize for URL slug
     */
    public static function slug(string $string, string $separator = '-', string $language = 'en'): string
    {
        $string = self::toAscii($string);
        $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', ' ', $string);
        $string = trim($string);
        $string = str_replace(' ', $separator, $string);
        $string = self::lower($string);

        return $string;
    }

    /**
     * Sanitize for username
     */
    public static function sanitizeUsername(string $username, bool $lowercase = true, bool $allowDash = false, bool $allowDot = false, bool $allowUnderscore = true): string
    {
        // Build allowed character pattern based on parameters
        $allowedChars = 'a-zA-Z0-9';
        if ($allowUnderscore) {
            $allowedChars .= '_';
        }
        if ($allowDash) {
            $allowedChars .= '\-';
        }
        if ($allowDot) {
            $allowedChars .= '.';
        }
        
        $username = preg_replace('/[^' . $allowedChars . ']/', '', $username);
        
        // Build trim and replacement patterns based on allowed characters
        $trimChars = '';
        $replacePattern = '[';
        if ($allowUnderscore) {
            $trimChars .= '_';
            $replacePattern .= '_';
        }
        if ($allowDash) {
            $trimChars .= '-';
            $replacePattern .= '\-';
        }
        if ($allowDot) {
            $trimChars .= '.';
            $replacePattern .= '.';
        }
        $replacePattern .= ']{2,}';
        
        if ($trimChars) {
            $username = trim($username, $trimChars);
        }
        if (strlen($replacePattern) > 3) {
            $username = preg_replace('/' . $replacePattern . '/', '_', $username);
        }

        if ($lowercase) {
            $username = self::lower($username);
        }

        return $username;
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize HTML
     */
    public static function sanitizeHtml(string $html, array $allowedTags = []): string
    {
        if (empty($allowedTags)) {
            return strip_tags($html);
        }

        $allowed = '<' . implode('><', $allowedTags) . '>';
        return strip_tags($html, $allowed);
    }

    /**
     * Sanitize for database
     */
    public static function sanitizeForDb(string $string): string
    {
        return addslashes($string);
    }

    /**
     * Remove non-printable characters
     */
    public static function removeNonPrintable(string $string): string
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }

    /**
     * Remove emoji
     */
    public static function removeEmoji(string $string): string
    {
        return preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $string);
    }

    /**
     * Normalize whitespace
     */
    public static function normalizeWhitespace(string $string): string
    {
        return preg_replace('/\s+/', ' ', trim($string));
    }

    /**
     * Normalize line endings
     */
    public static function normalizeLineEndings(string $string, string $lineEnding = "\n"): string
    {
        return str_replace(["\r\n", "\r"], $lineEnding, $string);
    }

    /**
     * Sanitize phone number
     */
    public static function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9+\-()\s]/', '', $phone);
    }

    /**
     * Sanitize for CSS class name
     */
    public static function sanitizeCssClass(string $class): string
    {
        $class = preg_replace('/[^a-zA-Z0-9\-_]/', '', $class);
        $class = preg_replace('/^[0-9\-]+/', '', $class);
        return $class;
    }

    /**
     * Sanitize for CSS ID
     */
    public static function sanitizeCssId(string $id): string
    {
        $id = preg_replace('/[^a-zA-Z0-9\-_]/', '', $id);
        $id = preg_replace('/^[0-9\-]+/', '', $id);
        return $id;
    }

    /**
     * Sanitize JSON string
     */
    public static function sanitizeJson(string $json): string
    {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }
        return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Sanitize for XML
     */
    public static function sanitizeXml(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize credit card number
     */
    public static function sanitizeCreditCard(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    /**
     * Sanitize for JavaScript
     */
    public static function sanitizeJs(string $string): string
    {
        return addcslashes($string, "\0..\37!@\177..\377");
    }

    /**
     * Remove SQL injection patterns
     */
    public static function removeSqlInjection(string $string): string
    {
        $patterns = [
            '/(\s|^)(union)(\s|$)/i',
            '/(\s|^)(select)(\s|$)/i',
            '/(\s|^)(insert)(\s|$)/i',
            '/(\s|^)(update)(\s|$)/i',
            '/(\s|^)(delete)(\s|$)/i',
            '/(\s|^)(drop)(\s|$)/i',
            '/(\s|^)(create)(\s|$)/i',
            '/(\s|^)(alter)(\s|$)/i',
            '/(\s|^)(exec)(\s|$)/i',
            '/(\s|^)(execute)(\s|$)/i',
            '/(\s|^)(script)(\s|$)/i',
            '/(\s|^)(or)(\s|$)/i',
            '/(\s|^)(and)(\s|$)/i',
            '/[\'";]/',
            '/\/\*.*?\*\//',
            '/--.*?$/m'
        ];

        return preg_replace($patterns, '', $string);
    }

    /**
     * Remove XSS patterns
     */
    public static function removeXss(string $string): string
    {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/<applet\b[^<]*(?:(?!<\/applet>)<[^<]*)*<\/applet>/mi',
            '/<form\b[^<]*(?:(?!<\/form>)<[^<]*)*<\/form>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/onmouseover=/i',
            '/onfocus=/i',
            '/onblur=/i',
            '/onchange=/i',
            '/onsubmit=/i'
        ];

        return preg_replace($patterns, '', $string);
    }

    /**
     * Sanitize for CSV
     */
    public static function sanitizeForCsv(string $string): string
    {
        $string = str_replace(['"', "\r", "\n"], ['""', '', ''], $string);
        if (str_contains($string, ',') || str_contains($string, '"')) {
            $string = '"' . $string . '"';
        }
        return $string;
    }

    /**
     * Sanitize password (remove common unsafe characters)
     */
    public static function sanitizePassword(
        string $raw,
        int    $maxBytesUtf8 = 1024,
        bool   $trimAsciiWhitespace = true,
        bool   $mapLocalDigitsToAscii = true,
        bool   $toLowerCase = true
    ): string
    {
        if (strlen($raw) > $maxBytesUtf8) {
            throw new InvalidArgumentException('Password too long');
        }

        if ($trimAsciiWhitespace) {
            $raw = preg_replace('/^[\x09\x0A\x0D\x20]+|[\x09\x0A\x0D\x20]+$/u', '', $raw);
        }
        if ($mapLocalDigitsToAscii) {
            $raw = strtr($raw, [
                '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
                '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
                '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            ]);
        }
        if (!Normalizer::isNormalized($raw, Normalizer::FORM_KC)) {
            $raw = Normalizer::normalize($raw, Normalizer::FORM_KC);
        }
        $raw = preg_replace('/[\p{C}&[^\x{200C}\x{200D}]]/u', '', $raw);
        if ($toLowerCase) {
            $raw = mb_strtolower($raw, 'UTF-8');
        }
        if ($raw === '') {
            throw new InvalidArgumentException('Password became empty after normalisation');
        }
        return $raw;
    }

    /**
     * Sanitize domain name
     */
    public static function sanitizeDomain(string $domain): string
    {
        $domain = self::lower($domain);
        $domain = preg_replace('/[^a-z0-9\-.]/', '', $domain);
        $domain = preg_replace('/\.{2,}/', '.', $domain);
        $domain = trim($domain, '.-');
        return $domain;
    }

    /**
     * Sanitize IP address
     */
    public static function sanitizeIp(string $ip): string
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false ? $ip : '';
    }

    /**
     * Sanitize MAC address
     */
    public static function sanitizeMac(string $mac): string
    {
        return preg_replace('/[^a-fA-F0-9:]/', '', $mac);
    }

    /**
     * Sanitize color hex code
     */
    public static function sanitizeHexColor(string $color): string
    {
        $color = preg_replace('/[^a-fA-F0-9#]/', '', $color);
        if (!preg_match('/^#[a-fA-F0-9]{3}$|^#[a-fA-F0-9]{6}$/', $color)) {
            return '';
        }
        return self::upper($color);
    }

    /**
     * Sanitize for regex pattern
     */
    public static function sanitizeRegex(string $pattern): string
    {
        return preg_quote($pattern, '/');
    }

    /**
     * Sanitize base64 string
     */
    public static function sanitizeBase64(string $string): string
    {
        return preg_replace('/[^a-zA-Z0-9+\/=]/', '', $string);
    }

    /**
     * Sanitize hash string
     */
    public static function sanitizeHash(string $hash): string
    {
        return preg_replace('/[^a-fA-F0-9]/', '', $hash);
    }

    /**
     * Sanitize for file path
     */
    public static function sanitizeFilePath(string $path): string
    {
        $path = str_replace(['../', '..\\', '../', '..\\\\'], '', $path);
        $path = preg_replace('/[^a-zA-Z0-9\-_.\\/\\\\]/', '', $path);
        return $path;
    }

    /**
     * Sanitize currency amount
     */
    public static function sanitizeCurrency(string $amount): string
    {
        return preg_replace('/[^0-9.,\-]/', '', $amount);
    }

    /**
     * Sanitize for search query
     */
    public static function sanitizeSearchQuery(string $query): string
    {
        $query = self::removeXss($query);
        $query = self::removeSqlInjection($query);
        $query = self::normalizeWhitespace($query);
        return trim($query);
    }

    /**
     * Sanitize for attribute value
     */
    public static function sanitizeAttribute(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Clean and validate UTF-8 string
     */
    public static function cleanUtf8(string $string): string
    {
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }

    /**
     * Remove dangerous file extensions
     */
    public static function sanitizeFileExtension(string $filename): string
    {
        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'phtml', 'asp', 'aspx', 'jsp', 'cfm',
            'exe', 'bat', 'cmd', 'scr', 'com', 'pif', 'vbs', 'js', 'jar', 'sh'
        ];

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(self::lower($extension), $dangerousExtensions)) {
            return pathinfo($filename, PATHINFO_FILENAME) . '.txt';
        }

        return $filename;
    }

    // ============================= Transformation =============================

    /**
     * Convert tabs to spaces
     */
    public static function tabsToSpaces(string $string, int $spaces = 4): string
    {
        return str_replace("\t", str_repeat(' ', $spaces), $string);
    }

    /**
     * Convert spaces to tabs
     */
    public static function spacesToTabs(string $string, int $spaces = 4): string
    {
        return str_replace(str_repeat(' ', $spaces), "\t", $string);
    }

    /**
     * Convert to single line
     */
    public static function toSingleLine(string $string): string
    {
        return str_replace(["\r\n", "\r", "\n"], ' ', $string);
    }

    /**
     * Strip accents
     */
    public static function stripAccents(string $string): string
    {
        $transliterator = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;');
        return $transliterator->transliterate($string);
    }

    /**
     * Expand contractions
     */
    public static function expandContractions(string $string): string
    {
        $contractions = [
            "can't" => "cannot",
            "won't" => "will not",
            "n't" => " not",
            "'re" => " are",
            "'ve" => " have",
            "'ll" => " will",
            "'d" => " would",
            "'m" => " am",
            "'s" => " is"
        ];

        return str_ireplace(array_keys($contractions), array_values($contractions), $string);
    }

    // ============================= Number Conversion =============================

    /**
     * Convert all Unicode digits to English
     */
    public static function normalizeDigits(string $string): string
    {
        // Persian & Arabic
        $string = strtr($string, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'
        ]);

        // Devanagari
        $string = strtr($string, ['०' => '0', '१' => '1', '२' => '2', '३' => '3', '४' => '4', '५' => '5', '६' => '6', '७' => '7', '८' => '8', '९' => '9']);

        // Bengali
        $string = strtr($string, ['০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4', '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9']);

        // Thai
        $string = strtr($string, ['๐' => '0', '๑' => '1', '๒' => '2', '๓' => '3', '๔' => '4', '๕' => '5', '๖' => '6', '๗' => '7', '๘' => '8', '๙' => '9']);

        // Chinese
        $string = strtr($string, ['零' => '0', '一' => '1', '二' => '2', '三' => '3', '四' => '4', '五' => '5', '六' => '6', '七' => '7', '八' => '8', '九' => '9']);

        return $string;
    }

    // ============================= Analysis & Statistics =============================

    /**
     * Count words
     */
    public static function wordCount(string $string): int
    {
        return str_word_count($string);
    }

    /**
     * Count characters (excluding spaces)
     */
    public static function charCount(string $string, bool $includeSpaces = true): int
    {
        if (!$includeSpaces) {
            $string = str_replace(' ', '', $string);
        }

        return self::length($string);
    }

    /**
     * Count lines
     */
    public static function lineCount(string $string): int
    {
        return substr_count($string, "\n") + 1;
    }

    /**
     * Get word frequency
     */
    public static function wordFrequency(string $string, bool $caseSensitive = false): array
    {
        if (!$caseSensitive) {
            $string = self::lower($string);
        }

        $words = self::words($string);
        return array_count_values($words);
    }

    /**
     * Get character frequency
     */
    public static function charFrequency(string $string, bool $caseSensitive = false): array
    {
        if (!$caseSensitive) {
            $string = self::lower($string);
        }

        $chars = self::toArray($string);
        return array_count_values($chars);
    }

    /**
     * Calculate reading time (words per minute)
     */
    public static function readingTime(string $string, int $wordsPerMinute = 200): int
    {
        $wordCount = self::wordCount($string);
        return (int)ceil($wordCount / $wordsPerMinute);
    }

    /**
     * Get string entropy
     */
    public static function entropy(string $string): float
    {
        $frequencies = self::charFrequency($string);
        $length = self::length($string);
        $entropy = 0.0;

        foreach ($frequencies as $frequency) {
            $probability = $frequency / $length;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    /**
     * Calculate Levenshtein distance
     */
    public static function levenshteinDistance(string $string1, string $string2): int
    {
        return levenshtein($string1, $string2);
    }

    /**
     * Calculate similarity percentage
     */
    public static function similarity(string $string1, string $string2): float
    {
        similar_text($string1, $string2, $percent);
        return round($percent, 2);
    }

    /**
     * Calculate Jaro-Winkler distance
     */
    public static function jaroWinkler(string $string1, string $string2): float
    {
        $len1 = self::length($string1);
        $len2 = self::length($string2);

        if ($len1 === 0 && $len2 === 0) return 1.0;
        if ($len1 === 0 || $len2 === 0) return 0.0;

        $matchWindow = max($len1, $len2) / 2 - 1;
        $matchWindow = max(0, $matchWindow);

        $matches = 0;
        $transpositions = 0;

        $s1Matches = array_fill(0, $len1, false);
        $s2Matches = array_fill(0, $len2, false);

        for ($i = 0; $i < $len1; $i++) {
            $start = max(0, $i - $matchWindow);
            $end = min($i + $matchWindow + 1, $len2);

            for ($j = $start; $j < $end; $j++) {
                if ($s2Matches[$j] || self::charAt($string1, $i) !== self::charAt($string2, $j)) {
                    continue;
                }

                $s1Matches[$i] = true;
                $s2Matches[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) return 0.0;

        $k = 0;
        for ($i = 0; $i < $len1; $i++) {
            if (!$s1Matches[$i]) continue;

            while (!$s2Matches[$k]) $k++;

            if (self::charAt($string1, $i) !== self::charAt($string2, $k)) {
                $transpositions++;
            }

            $k++;
        }

        $jaro = ($matches / $len1 + $matches / $len2 + ($matches - $transpositions / 2) / $matches) / 3;

        $prefix = 0;
        for ($i = 0; $i < min($len1, $len2); $i++) {
            if (self::charAt($string1, $i) === self::charAt($string2, $i)) {
                $prefix++;
            } else {
                break;
            }
        }

        $prefix = min(4, $prefix);

        return $jaro + $prefix * 0.1 * (1 - $jaro);
    }

    // ============================= Generation =============================

    /**
     * Generate random string
     */
    public static function random(int $length = 16, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generate random alphanumeric string
     */
    public static function randomAlphanumeric(int $length = 16): string
    {
        return self::random($length);
    }

    /**
     * Generate random alphabetic string
     */
    public static function randomAlpha(int $length = 16, bool $uppercase = true): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        if ($uppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        return self::random($length, $characters);
    }

    /**
     * Generate random numeric string
     */
    public static function randomNumeric(int $length = 16): string
    {
        return self::random($length, '0123456789');
    }

    /**
     * Generate random hexadecimal string
     */
    public static function randomHex(int $length = 16): string
    {
        return self::random($length, '0123456789abcdef');
    }

    /**
     * Generate UUID v4
     */
    public static function uuid4(): string
    {
        try {
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        } catch (RandomException $e) {
            throw new InvalidArgumentException('Unable to generate UUID: ' . $e->getMessage());
        }
    }

    /**
     * Generate UUID v5 (namespace-based)
     */
    public static function uuid5(string $namespace, string $name): string
    {
        $hash = sha1($namespace . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * Generate random password
     * @throws RandomException
     */
    public static function generatePassword(int $length = 12, bool $includeSymbols = true): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $characters = $lowercase . $uppercase . $numbers;
        if ($includeSymbols) {
            $characters .= $symbols;
        }

        $password = $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        if ($includeSymbols) {
            $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        }

        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Generate Lorem Ipsum text
     */
    public static function lorem(int $words = 50, bool $startWithLorem = true): string
    {
        $loremWords = [
            'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
            'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
            'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
            'exercitation', 'ullamco', 'laboris', 'nisi', 'aliquip', 'ex', 'ea', 'commodo',
            'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit', 'voluptate',
            'velit', 'esse', 'cillum', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint',
            'occaecat', 'cupidatat', 'non', 'proident', 'sunt', 'culpa', 'qui', 'officia',
            'deserunt', 'mollit', 'anim', 'id', 'est', 'laborum'
        ];

        $result = [];

        if ($startWithLorem) {
            $result[] = 'Lorem';
            $result[] = 'ipsum';
            $words -= 2;
        }

        for ($i = 0; $i < $words; $i++) {
            $result[] = $loremWords[array_rand($loremWords)];
        }

        return implode(' ', $result) . '.';
    }

    // ============================= Formatting =============================

    /**
     * Convert number to ordinal
     */
    public static function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }

        return $number . $suffixes[$number % 10];
    }

    /**
     * Format currency
     */
    public static function currency(float $amount, string $currency = 'USD', int $decimals = 2): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'IRR' => '﷼'
        ];

        $symbol = $symbols[$currency] ?? $currency;
        return $symbol . number_format($amount, $decimals);
    }

    /**
     * Format percentage
     */
    public static function percentage(float $value, int $decimals = 2): string
    {
        return number_format($value * 100, $decimals) . '%';
    }

    /**
     * Format bytes
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Format phone number
     */
    public static function formatPhone(string $phone, string $format = '(###) ###-####'): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = str_split($phone);
        $result = '';
        $phoneIndex = 0;

        for ($i = 0; $i < strlen($format); $i++) {
            if ($format[$i] === '#' && isset($phone[$phoneIndex])) {
                $result .= $phone[$phoneIndex];
                $phoneIndex++;
            } else {
                $result .= $format[$i];
            }
        }

        return $result;
    }

    /**
     * Format credit card number
     */
    public static function formatCreditCard(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        return chunk_split($number, 4, ' ');
    }

    /**
     * Mask string
     */
    public static function mask(string $string, int $start = 0, ?int $length = null, string $mask = '*'): string
    {
        $stringLength = self::length($string);

        if ($length === null) {
            $length = $stringLength - $start;
        }

        $masked = self::substring($string, 0, $start);
        $masked .= str_repeat($mask, $length);
        $masked .= self::substring($string, $start + $length);

        return $masked;
    }

    /**
     * Mask email
     */
    public static function maskEmail(string $email): string
    {
        if (!self::isEmail($email)) {
            return $email;
        }

        [$username, $domain] = explode('@', $email);
        $usernameLength = strlen($username);

        if ($usernameLength <= 2) {
            $maskedUsername = str_repeat('*', $usernameLength);
        } else {
            $visibleChars = max(1, floor($usernameLength / 3));
            $maskedUsername = substr($username, 0, $visibleChars) .
                str_repeat('*', $usernameLength - 2 * $visibleChars) .
                substr($username, -$visibleChars);
        }

        return $maskedUsername . '@' . $domain;
    }

    // ============================= Text Extraction =============================

    /**
     * Extract URLs from text
     */
    public static function extractUrls(string $text): array
    {
        preg_match_all('/https?:\/\/\S+/', $text, $matches);
        return $matches[0];
    }

    /**
     * Extract emails from text
     */
    public static function extractEmails(string $text): array
    {
        preg_match_all(self::PATTERN_EMAIL, $text, $matches);
        return $matches[0];
    }

    /**
     * Extract hashtags from text
     */
    public static function extractHashtags(string $text): array
    {
        preg_match_all(self::PATTERN_HASHTAG, $text, $matches);
        return $matches[0];
    }

    /**
     * Extract mentions from text
     */
    public static function extractMentions(string $text): array
    {
        preg_match_all(self::PATTERN_MENTION, $text, $matches);
        return $matches[0];
    }

    /**
     * Extract phone numbers from text
     */
    public static function extractPhones(string $text): array
    {
        preg_match_all('/(\+?[1-9]\d{1,14})/', $text, $matches);
        return array_filter($matches[0], fn($phone) => self::isPhone($phone));
    }

    /**
     * Extract numbers from text
     */
    public static function extractNumbers(string $text): array
    {
        preg_match_all('/\d+(?:\.\d+)?/', $text, $matches);
        return $matches[0];
    }

    /**
     * Convert URLs to clickable links
     */
    public static function linkify(string $text): string
    {
        return preg_replace(
            '/(https?:\/\/\S+)/',
            '<a href="$1" target="_blank">$1</a>',
            $text
        );
    }

    /**
     * Highlight words in text
     */
    public static function highlight(string $text, string $keyword, string $class = 'highlight'): string
    {
        return preg_replace(
            '/(' . preg_quote($keyword, '/') . ')/i',
            '<span class="' . $class . '">$1</span>',
            $text
        );
    }

    /**
     * Create excerpt with context
     */
    public static function excerpt(string $text, string $keyword, int $radius = 100): string
    {
        $position = self::indexOf(self::lower($text), self::lower($keyword));

        if ($position === false) {
            return self::truncate($text, $radius * 2);
        }

        $start = max(0, $position - $radius);
        $length = min(self::length($text) - $start, ($radius * 2) + self::length($keyword));

        $excerpt = self::substring($text, $start, $length);

        if ($start > 0) {
            $excerpt = '...' . $excerpt;
        }

        if ($start + $length < self::length($text)) {
            $excerpt .= '...';
        }

        return $excerpt;
    }

    // ============================= String Comparison =============================

    /**
     * Compare strings
     */
    public static function compare(string $string1, string $string2, bool $caseSensitive = true): int
    {
        if (!$caseSensitive) {
            $string1 = self::lower($string1);
            $string2 = self::lower($string2);
        }

        return strcmp($string1, $string2);
    }

    /**
     * Natural comparison
     */
    public static function naturalCompare(string $string1, string $string2, bool $caseSensitive = true): int
    {
        return $caseSensitive ? strnatcmp($string1, $string2) : strnatcasecmp($string1, $string2);
    }

    /**
     * Find common prefix
     */
    public static function commonPrefix(string $string1, string $string2): string
    {
        $length = min(self::length($string1), self::length($string2));

        for ($i = 0; $i < $length; $i++) {
            if (self::charAt($string1, $i) !== self::charAt($string2, $i)) {
                break;
            }
        }

        return self::substring($string1, 0, $i);
    }

    /**
     * Find common suffix
     */
    public static function commonSuffix(string $string1, string $string2): string
    {
        $len1 = self::length($string1);
        $len2 = self::length($string2);
        $length = min($len1, $len2);

        for ($i = 0; $i < $length; $i++) {
            if (self::charAt($string1, $len1 - 1 - $i) !== self::charAt($string2, $len2 - 1 - $i)) {
                break;
            }
        }

        return self::substring($string1, $len1 - $i);
    }

    /**
     * Find longest common substring
     */
    public static function longestCommonSubstring(string $string1, string $string2): string
    {
        $len1 = self::length($string1);
        $len2 = self::length($string2);
        $longest = '';

        for ($i = 0; $i < $len1; $i++) {
            for ($j = 0; $j < $len2; $j++) {
                $len = 0;

                while (($i + $len < $len1) && ($j + $len < $len2) &&
                    (self::charAt($string1, $i + $len) === self::charAt($string2, $j + $len))) {
                    $len++;
                }

                if ($len > self::length($longest)) {
                    $longest = self::substring($string1, $i, $len);
                }
            }
        }

        return $longest;
    }

    // ============================= Advanced Features =============================

    /**
     * Parse CSV line
     */
    public static function parseCSV(string $line, string $delimiter = ',', string $enclosure = '"'): array
    {
        return str_getcsv($line, $delimiter, $enclosure);
    }

    /**
     * Convert array to CSV
     */
    public static function toCSV(array $array, string $delimiter = ',', string $enclosure = '"'): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $array, $delimiter, $enclosure);
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return trim($csv);
    }

    /**
     * Parse query string
     */
    public static function parseQueryString(string $query): array
    {
        parse_str($query, $result);
        return $result;
    }

    /**
     * Build query string
     */
    public static function buildQueryString(array $data): string
    {
        return http_build_query($data);
    }

    /**
     * Obfuscate string
     */
    public static function obfuscate(string $string): string
    {
        return base64_encode(gzcompress($string, 9));
    }

    /**
     * Deobfuscate string
     */
    public static function deobfuscate(string $obfuscated): string
    {
        return gzuncompress(base64_decode($obfuscated)) ?: '';
    }

    /**
     * Convert to plural (basic English rules)
     */
    public static function pluralize(string $word): string
    {
        $irregulars = [
            'child' => 'children',
            'man' => 'men',
            'woman' => 'women',
            'tooth' => 'teeth',
            'foot' => 'feet',
            'mouse' => 'mice',
            'goose' => 'geese'
        ];

        $lowerWord = self::lower($word);

        if (isset($irregulars[$lowerWord])) {
            return $irregulars[$lowerWord];
        }

        if (preg_match('/(s|sh|ch|x|z)$/', $lowerWord)) {
            return $word . 'es';
        }

        if (preg_match('/[^aeiou]y$/', $lowerWord)) {
            return substr($word, 0, -1) . 'ies';
        }

        if (preg_match('/[^f]fe?$/', $lowerWord)) {
            return preg_replace('/fe?$/', 'ves', $word);
        }

        return $word . 's';
    }

    /**
     * Convert to singular (basic English rules)
     */
    public static function singularize(string $word): string
    {
        $irregulars = array_flip([
            'child' => 'children',
            'man' => 'men',
            'woman' => 'women',
            'tooth' => 'teeth',
            'foot' => 'feet',
            'mouse' => 'mice',
            'goose' => 'geese'
        ]);

        $lowerWord = self::lower($word);

        if (isset($irregulars[$lowerWord])) {
            return $irregulars[$lowerWord];
        }

        if (str_ends_with($lowerWord, 'ies')) {
            return substr($word, 0, -3) . 'y';
        }

        if (str_ends_with($lowerWord, 'ves')) {
            return substr($word, 0, -3) . 'f';
        }

        if (preg_match('/(s|sh|ch|x|z)es$/', $lowerWord)) {
            return substr($word, 0, -2);
        }

        if (str_ends_with($lowerWord, 's') && !str_ends_with($lowerWord, 'ss')) {
            return substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Humanize string
     */
    public static function humanize(string $string): string
    {
        $string = str_replace(['_', '-'], ' ', $string);
        $string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
        $string = self::lower($string);
        return self::capitalize($string);
    }

    /**
     * Table flip (for fun!)
     */
    public static function tableFlip(string $string): string
    {
        $flips = [
            'a' => 'ɐ', 'b' => 'q', 'c' => 'ɔ', 'd' => 'p', 'e' => 'ǝ',
            'f' => 'ɟ', 'g' => 'ƃ', 'h' => 'ɥ', 'i' => 'ᴉ', 'j' => 'ɾ',
            'k' => 'ʞ', 'l' => 'l', 'm' => 'ɯ', 'n' => 'u', 'o' => 'o',
            'p' => 'd', 'q' => 'b', 'r' => 'ɹ', 's' => 's', 't' => 'ʇ',
            'u' => 'n', 'v' => 'ʌ', 'w' => 'ʍ', 'x' => 'x', 'y' => 'ʎ',
            'z' => 'z', '0' => '0', '1' => 'Ɩ', '2' => 'ᄅ', '3' => 'Ɛ',
            '4' => 'ㄣ', '5' => 'ϛ', '6' => '9', '7' => 'ㄥ', '8' => '8',
            '9' => '6', '.' => '˙', ',' => "'", '?' => '¿', '!' => '¡',
            '"' => '„', "'" => '‛', '(' => ')', ')' => '('
        ];

        $result = '';
        for ($i = self::length($string) - 1; $i >= 0; $i--) {
            $char = self::charAt($string, $i);
            $result .= $flips[self::lower($char)] ?? $char;
        }

        return $result;
    }

    /**
     * Calculate password strength score (0-100)
     */
    public static function passwordStrength(string $password): int
    {
        $score = 0;
        $length = self::length($password);
        
        // Length scoring (max 25 points)
        if ($length >= 12) {
            $score += 25;
        } elseif ($length >= 8) {
            $score += 20;
        } elseif ($length >= 6) {
            $score += 15;
        } elseif ($length >= 4) {
            $score += 10;
        }
        
        // Character variety scoring (max 75 points)
        $patterns = [
            '/[a-z]/' => 15,    // lowercase letters
            '/[A-Z]/' => 15,    // uppercase letters
            '/[0-9]/' => 15,    // numbers
            '/[^a-zA-Z0-9]/' => 15, // special characters
        ];
        
        foreach ($patterns as $pattern => $points) {
            if (preg_match($pattern, $password)) {
                $score += $points;
            }
        }
        
        // Bonus points for length over 12 characters (max 15 points)
        if ($length > 12) {
            $score += min(15, ($length - 12) * 2);
        }
        
        // Penalty for common patterns
        $commonPatterns = [
            '/(.)\1{2,}/',      // repeated characters (aaa, 111)
            '/123|abc|qwe/',    // sequential characters
            '/password|admin|user/', // common words
        ];
        
        foreach ($commonPatterns as $pattern) {
            if (preg_match($pattern, self::lower($password))) {
                $score -= 10;
            }
        }
        
        return max(0, min(100, $score));
    }
}