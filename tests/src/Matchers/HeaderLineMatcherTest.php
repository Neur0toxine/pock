<?php

/**
 * PHP 7.1
 *
 * @category HeaderLineMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HeaderLineMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class HeaderLineMatcherTest
 *
 * @category HeaderLineMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HeaderLineMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['first', 'second']);
        $matcher = new HeaderLineMatcher('x-test-header', 'second, first');

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['first', 'second']);
        $matcher = new HeaderLineMatcher('x-test-header', 'first, second');

        self::assertTrue($matcher->matches($request));
    }
}
