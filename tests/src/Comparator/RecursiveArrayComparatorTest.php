<?php

/**
 * PHP version 7.3
 *
 * @category RecursiveArrayComparatorTest
 * @package  Pock\Tests\Comparator
 */

namespace Pock\Tests\Comparator;

use PHPUnit\Framework\TestCase;
use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveArrayComparator;

/**
 * Class RecursiveArrayComparatorTest
 *
 * @category RecursiveArrayComparatorTest
 * @package  Pock\Tests\Comparator
 */
class RecursiveArrayComparatorTest extends TestCase
{
    public function testMatches(): void
    {
        $needle = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ],
            'test' => ''
        ];
        $haystack = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ],
            'test' => ''
        ];

        self::assertTrue(ComparatorLocator::get(RecursiveArrayComparator::class)->compare($needle, $haystack));
    }

    public function testNotMatches(): void
    {
        $needle = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ],
            'test2' => [1]
        ];
        $haystack = [
            'filter' => [
                'createdAtFrom' => '2020-01-01 00:00:00',
                'createdAtTo' => '2021-08-01 00:00:00',
            ],
            'test2' => 1
        ];

        self::assertFalse(ComparatorLocator::get(RecursiveArrayComparator::class)->compare($needle, $haystack));
    }

    public function testNotMatchesKeyPositions(): void
    {
        $needle = [
            'source' => json_decode('{"medium":"tiktok","source":"Test Ad","campaign":"Test Campaign"}', true)
        ];
        $haystack = [
            'source' => json_decode('{"source":"Test Ad","medium":"tiktok","campaign":"Test Campaign"}', true)
        ];

        self::assertTrue(ComparatorLocator::get(RecursiveArrayComparator::class)->compare($needle, $haystack));
    }
}
