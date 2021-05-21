<?php

/**
 * PHP 7.3
 *
 * @category RecursiveArrayComparator
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

/**
 * Class RecursiveArrayComparator
 *
 * @category RecursiveArrayComparator
 * @package  Pock\Comparator
 */
class RecursiveArrayComparator implements ComparatorInterface
{
    /**
     * @inheritDoc
     */
    public function compare($first, $second): bool
    {
        if (!is_array($first) || !is_array($second)) {
            return false;
        }

        return static::recursiveCompareArrays($first, $second);
    }

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
}
