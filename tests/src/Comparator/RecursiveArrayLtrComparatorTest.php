<?php

/**
 * PHP version 7.3
 *
 * @category RecursiveArrayLtrComparatorTest
 * @package  Pock\Tests\Comparator
 */

namespace Pock\Tests\Comparator;

use PHPUnit\Framework\TestCase;
use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveLtrArrayComparator;

/**
 * Class RecursiveArrayLtrComparatorTest
 *
 * @category RecursiveArrayLtrComparatorTest
 * @package  Pock\Tests\Comparator
 */
class RecursiveArrayLtrComparatorTest extends TestCase
{
    public function testMatches(): void
    {
        $needle = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ]
        ];
        $haystack = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ],
            'test' => ''
        ];

        self::assertTrue(ComparatorLocator::get(RecursiveLtrArrayComparator::class)->compare($needle, $haystack));
    }
}
