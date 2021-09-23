<?php

/**
 * PHP version 7.3
 *
 * @category RegExpPathMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\RegExpPathMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class RegExpPathMatcherTest
 *
 * @category RegExpPathMatcherTest
 * @package  Pock\Tests\Matchers
 */
class RegExpPathMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $matcher = new RegExpPathMatcher('/\/?\d+-\d+/m');
        $request = static::getTestRequest()->withUri(static::getPsr17Factory()->createUri('https://test.com/test'));

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $matcher = new RegExpPathMatcher('/\d+-\d+/m', PREG_UNMATCHED_AS_NULL);
        $request = static::getTestRequest()->withUri(static::getPsr17Factory()->createUri('https://test.com/23-900'));

        self::assertTrue($matcher->matches($request));
    }
}
