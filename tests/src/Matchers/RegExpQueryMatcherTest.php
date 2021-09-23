<?php

/**
 * PHP version 7.3
 *
 * @category RegExpQueryMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\RegExpQueryMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class RegExpQueryMatcherTest
 *
 * @category RegExpQueryMatcherTest
 * @package  Pock\Tests\Matchers
 */
class RegExpQueryMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $matcher = new RegExpQueryMatcher('/\d+-\d+/m');
        $request = static::getTestRequest()->withUri(
            static::getPsr17Factory()->createUri('https://test.com')
                ->withQuery('param=value')
        );

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $matcher = new RegExpQueryMatcher('/\d+-\d+/m', PREG_UNMATCHED_AS_NULL);
        $request = static::getTestRequest()->withUri(
            static::getPsr17Factory()->createUri('https://test.com')
                ->withQuery('param=23-900')
        );

        self::assertTrue($matcher->matches($request));
    }
}
