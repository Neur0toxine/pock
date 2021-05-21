<?php

/**
 * PHP 7.3
 *
 * @category ScalarFlatArrayComparator
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

/**
 * Class ScalarFlatArrayComparator
 *
 * @category ScalarFlatArrayComparator
 * @package  Pock\Comparator
 */
class ScalarFlatArrayComparator implements ComparatorInterface
{
    /**
     * @inheritDoc
     */
    public function compare($first, $second): bool
    {
        if (!is_array($first) || !is_array($second)) {
            return false;
        }

        return static::compareScalarFlatArrays($first, $second);
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
    protected static function compareScalarFlatArrays(array $first, array $second): bool
    {
        return count($first) === count($second) &&
            array_diff($first, $second) === array_diff($second, $first);
    }
}
