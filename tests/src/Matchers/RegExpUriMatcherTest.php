<?php

/**
 * PHP version 7.3
 *
 * @category RegExpUriMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\RegExpUriMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class RegExpUriMatcherTest
 *
 * @category RegExpUriMatcher
 * @package  Pock\Tests\Matchers
 */
class RegExpUriMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $matcher = new RegExpUriMatcher('/https\:\/\/\w+\.com\/\d+-\d+\?param=\d+-\d+/m');
        $request = static::getTestRequest();

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $matcher = new RegExpUriMatcher('/https\:\/\/\w+\.com\/\d+-\d+\?param=\d+-\d+/m', PREG_UNMATCHED_AS_NULL);
        $request = static::getTestRequest()->withUri(
            static::getPsr17Factory()->createUri('https://example.com/23-900')
                ->withQuery('param=23-900')
        );

        self::assertTrue($matcher->matches($request));
    }
}
