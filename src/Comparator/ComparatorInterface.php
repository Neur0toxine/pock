<?php

/**
 * PHP 7.3
 *
 * @category ComparatorInterface
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

/**
 * Interface ComparatorInterface
 *
 * @category ComparatorInterface
 * @package  Pock\Comparator
 */
interface ComparatorInterface
{
    /**
     * Compare two values.
     *
     * @param mixed $first
     * @param mixed $second
     *
     * @return bool
     */
    public function compare($first, $second): bool;
}
