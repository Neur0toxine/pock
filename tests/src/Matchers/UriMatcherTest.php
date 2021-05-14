<?php

/**
 * PHP 7.3
 *
 * @category UriMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\UriMatcher;

/**
 * Class UriMatcherTest
 *
 * @category UriMatcherTest
 * @package  Pock\Tests\Matchers
 */
class UriMatcherTest extends AbstractRequestMatcherTest
{
    public function testMatches(): void
    {
        self::assertTrue((new UriMatcher(self::TEST_URI))->matches(static::getTestRequest()));
        self::assertTrue((new UriMatcher(static::getPsr17Factory()->createUri(self::TEST_URI)))
            ->matches(static::getTestRequest()));
    }
}
