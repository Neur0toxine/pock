<?php

/**
 * PHP 7.1
 *
 * @category ExactHeaderMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\ExactHeaderMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class ExactHeaderMatcherTest
 *
 * @category ExactHeaderMatcherTest
 * @package  Pock\Tests\Matchers
 */
class ExactHeaderMatcherTest extends PockTestCase
{
    public function testNoMatch(): void
    {
        $matcher = new ExactHeaderMatcher('x-test-header', 'test value');
        self::assertFalse($matcher->matches(self::getTestRequest()));
    }

    public function testMatchStringValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new ExactHeaderMatcher('x-test-header', 'test value');

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new ExactHeaderMatcher('x-test-header', ['test value']);

        self::assertTrue($matcher->matches($request));
    }

    public function testNotMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value1', 'test value2']);
        $matcher = new ExactHeaderMatcher('x-test-header', ['test value1']);

        self::assertFalse($matcher->matches($request));
    }

    public function testMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value1', 'test value2']);
        $matcher = new ExactHeaderMatcher('x-test-header', ['test value2', 'test value1']);

        self::assertTrue($matcher->matches($request));
    }
}
