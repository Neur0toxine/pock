<?php

/**
 * PHP 7.3
 *
 * @category ComparatorLocatorTest
 * @package  Pock\Tests\Comparator
 */

namespace Pock\Tests\Comparator;

use PHPUnit\Framework\TestCase;
use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\LtrScalarArrayComparator;
use Pock\Comparator\RecursiveArrayComparator;
use Pock\Comparator\RecursiveLtrArrayComparator;
use Pock\Comparator\ScalarFlatArrayComparator;
use RuntimeException;

/**
 * Class ComparatorLocatorTest
 *
 * @category ComparatorLocatorTest
 * @package  Pock\Tests\Comparator
 */
class ComparatorLocatorTest extends TestCase
{
    public function testGetException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Comparator random does not exist.');

        ComparatorLocator::get('random');
    }

    public function testGet(): void
    {
        $comparator = ComparatorLocator::get(ScalarFlatArrayComparator::class);

        self::assertInstanceOf(ScalarFlatArrayComparator::class, $comparator);
        self::assertTrue($comparator->compare(['1'], ['1']));
        self::assertFalse($comparator->compare(['1'], ['2']));
        self::assertFalse($comparator->compare(null, null));
        self::assertFalse(ComparatorLocator::get(LtrScalarArrayComparator::class)->compare(null, null));
        self::assertFalse(ComparatorLocator::get(RecursiveArrayComparator::class)->compare(null, null));
        self::assertFalse(ComparatorLocator::get(RecursiveLtrArrayComparator::class)->compare(null, null));
    }
}
