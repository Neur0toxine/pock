<?php

/**
 * PHP 7.3
 *
 * @category RecursiveLtrArrayComparator
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

/**
 * Class RecursiveLtrArrayComparator
 *
 * @category RecursiveLtrArrayComparator
 * @package  Pock\Comparator
 */
class RecursiveLtrArrayComparator extends RecursiveArrayComparator
{
    /**
     * @inheritDoc
     */
    public function compare($first, $second): bool
    {
        if (!is_array($first) || !is_array($second)) {
            return false;
        }

        return static::recursiveNeedlePresentInHaystack($first, $second);
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
