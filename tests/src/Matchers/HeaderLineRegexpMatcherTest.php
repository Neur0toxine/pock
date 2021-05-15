<?php

/**
 * PHP 7.1
 *
 * @category HeaderLineRegexpMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HeaderLineRegexpMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class HeaderLineRegexpMatcherTest
 *
 * @category HeaderLineRegexpMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HeaderLineRegexpMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['first', 'second']);
        $matcher = new HeaderLineRegexpMatcher('x-test-header', '/first$/');

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['first', 'second']);
        $matcher = new HeaderLineRegexpMatcher('x-test-header', '/^first/');

        self::assertTrue($matcher->matches($request));
    }
}
