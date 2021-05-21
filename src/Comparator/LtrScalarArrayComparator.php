<?php

/**
 * PHP 7.3
 *
 * @category LtrScalarArrayComparator
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

/**
 * Class LtrScalarArrayComparator
 *
 * @category LtrScalarArrayComparator
 * @package  Pock\Comparator
 */
class LtrScalarArrayComparator implements ComparatorInterface
{
    /**
     * @inheritDoc
     */
    public function compare($first, $second): bool
    {
        if (!is_array($first) || !is_array($second)) {
            return false;
        }

        return static::isNeedlePresentInHaystack($first, $second);
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
}
