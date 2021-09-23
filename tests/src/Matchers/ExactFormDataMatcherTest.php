<?php

/**
 * PHP version 7.3
 *
 * @category ExactFormDataMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\ExactFormDataMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class ExactFormDataMatcherTest
 *
 * @category ExactFormDataMatcherTest
 * @package  Pock\Tests\Matchers
 */
class ExactFormDataMatcherTest extends PockTestCase
{
    public function testInvalidData(): void
    {
        $matcher = new ExactFormDataMatcher(['field3' => 'value3']);
        $request = self::getRequestWithBody('doesn\'t look like form-data at all');

        self::assertFalse($matcher->matches($request));
    }

    public function testNoMatches(): void
    {
        $matcher = new ExactFormDataMatcher(['field3' => 'value3']);
        $request = self::getRequestWithBody('field1=value1&field2=value2');

        self::assertFalse($matcher->matches($request));
    }

    public function testNoMatchesByValue(): void
    {
        $matcher = new ExactFormDataMatcher(['field1' => 'value2']);
        $request = self::getRequestWithBody('field1=value1&field2=value2');

        self::assertFalse($matcher->matches($request));
    }

    public function testNoMatchesRedundantParam(): void
    {
        $matcher = new ExactFormDataMatcher(['field2' => 'value2']);
        $request = self::getRequestWithBody('field1=value1&field2=value2');

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $matcher = new ExactFormDataMatcher(['field1' => 'value1', 'field2' => 'value2']);
        $request = self::getRequestWithBody('field1=value1&field2=value2');

        self::assertTrue($matcher->matches($request));
    }
}
