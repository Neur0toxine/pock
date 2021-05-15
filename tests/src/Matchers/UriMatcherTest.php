<?php

/**
 * PHP 7.2
 *
 * @category UriMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\UriMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class UriMatcherTest
 *
 * @category UriMatcherTest
 * @package  Pock\Tests\Matchers
 */
class UriMatcherTest extends PockTestCase
{
    public function testMatches(): void
    {
        self::assertFalse((new UriMatcher('https://test.com'))->matches(static::getTestRequest()));
        self::assertTrue((new UriMatcher(self::TEST_URI))->matches(static::getTestRequest()));
        self::assertTrue((new UriMatcher(static::getPsr17Factory()->createUri(self::TEST_URI)))
            ->matches(static::getTestRequest()));
    }
}
