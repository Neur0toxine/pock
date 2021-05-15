<?php

/**
 * PHP 7.1
 *
 * @category HeadersMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HeadersMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class HeadersMatcherTest
 *
 * @category HeadersMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HeadersMatcherTest extends PockTestCase
{
    public function testNoMatch(): void
    {
        $matcher = new HeadersMatcher(['x-test-header' => 'test value']);
        self::assertFalse($matcher->matches(self::getTestRequest()));
    }

    public function testMatchStringValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new HeadersMatcher(['x-test-header' => 'test value']);

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new HeadersMatcher(['x-test-header' => ['test value']]);

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value1', 'test value2']);
        $matcher = new HeadersMatcher(['x-test-header' => ['test value1']]);

        self::assertTrue($matcher->matches($request));
    }

    public function testNoMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value2']);
        $matcher = new HeadersMatcher(['x-test-header' => ['test value1']]);

        self::assertFalse($matcher->matches($request));
    }
}
