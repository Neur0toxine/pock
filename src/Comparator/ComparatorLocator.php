<?php

/**
 * PHP 7.3
 *
 * @category ComparatorLocator
 * @package  Pock\Comparator
 */

namespace Pock\Comparator;

use RuntimeException;

/**
 * Class ComparatorLocator
 *
 * @category ComparatorLocator
 * @package  Pock\Comparator
 */
class ComparatorLocator
{
    /** @var \Pock\Comparator\ComparatorInterface[] */
    private static $comparators = [];

    /**
     * Returns comparator.
     *
     * @param string $fqn
     *
     * @return \Pock\Comparator\ComparatorInterface
     */
    public static function get(string $fqn): ComparatorInterface
    {
        if (!class_exists($fqn)) {
            throw new RuntimeException('Comparator ' . $fqn . ' does not exist.');
        }

        if (!array_key_exists($fqn, static::$comparators)) {
            static::$comparators[$fqn] = new $fqn();
        }

        return static::$comparators[$fqn];
    }
}
