<?php

/**
 * PHP 7.1
 *
 * @category AbstractArrayPoweredComponent
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

/**
 * Class AbstractArrayPoweredComponent
 *
 * @category AbstractArrayPoweredComponent
 * @package  Pock\Matchers
 */
abstract class AbstractArrayPoweredComponent
{
    /**
     * Returns true if both arrays are equal recursively.
     *
     * @phpstan-ignore-next-line
     * @param array $first
     * @phpstan-ignore-next-line
     * @param array $second
     *
     * @return bool
     */
    protected static function recursiveCompareArrays(array $first, array $second): bool
    {
        if (count($first) !== count($second)) {
            return false;
        }

        if (!empty(array_diff(array_keys($first), array_keys($second)))) {
            return false;
        }

        foreach ($first as $key => $value) {
            if (is_array($value) && !self::recursiveCompareArrays($value, $second[$key])) {
                return false;
            }

            if ($value !== $second[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if two one-dimensional string arrays are equal.
     *
     * @phpstan-ignore-next-line
     * @param array $first
     * @phpstan-ignore-next-line
     * @param array $second
     *
     * @return bool
     */
    protected static function compareStringArrays(array $first, array $second): bool
    {
        return count($first) === count($second) &&
            array_diff($first, $second) === array_diff($second, $first);
    }

    /**
     * Returns true if all needle values is present in haystack.
     * Doesn't work for multidimensional arrays.
     *
     * @phpstan-ignore-next-line
     * @param array $needle
     * @phpstan-ignore-next-line
     * @param array $haystack
     *
     * @return bool
     */
    protected static function isNeedlePresentInHaystack(array $needle, array $haystack): bool
    {
        foreach ($needle as $value) {
            if (!in_array($value, $haystack, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if all needle values is present in haystack.
     * Works for multidimensional arrays. Internal arrays will be treated as values (e.g. will be compared recursively).
     *
     * @phpstan-ignore-next-line
     * @param array $needle
     * @phpstan-ignore-next-line
     * @param array $haystack
     *
     * @return bool
     */
    protected static function recursiveNeedlePresentInHaystack(array $needle, array $haystack): bool
    {
        if (!empty(array_diff(array_keys($needle), array_keys($haystack)))) {
            return false;
        }

        foreach ($needle as $key => $value) {
            if (is_array($value) && !self::recursiveCompareArrays($value, $haystack[$key])) {
                return false;
            }

            if ($value !== $haystack[$key]) {
                return false;
            }
        }

        return true;
    }
}
