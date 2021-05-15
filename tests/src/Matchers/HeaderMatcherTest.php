<?php

/**
 * PHP 7.1
 *
 * @category HeaderMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HeaderMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class HeaderMatcherTest
 *
 * @category HeaderMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HeaderMatcherTest extends PockTestCase
{
    public function testNoMatch(): void
    {
        $matcher = new HeaderMatcher('x-test-header', 'test value');
        self::assertFalse($matcher->matches(self::getTestRequest()));
    }

    public function testMatchStringValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new HeaderMatcher('x-test-header', 'test value');

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new HeaderMatcher('x-test-header', ['test value']);

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value1', 'test value2']);
        $matcher = new HeaderMatcher('x-test-header', ['test value1']);

        self::assertTrue($matcher->matches($request));
    }
}
