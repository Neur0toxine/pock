<?php

/**
 * PHP version 7.3
 *
 * @category RegExpBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\RegExpBodyMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class RegExpBodyMatcherTest
 *
 * @category RegExpBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */
class RegExpBodyMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $matcher = new RegExpBodyMatcher('/\d+-\d+/m');
        $request = static::getRequestWithBody('test unmatchable request');

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $matcher = new RegExpBodyMatcher('/\d+-\d+/m', PREG_UNMATCHED_AS_NULL);
        $request = static::getRequestWithBody('23-900');

        self::assertTrue($matcher->matches($request));
    }
}
