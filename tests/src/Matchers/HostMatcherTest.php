<?php

/**
 * PHP 7.2
 *
 * @category HostMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HostMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class HostMatcherTest
 *
 * @category HostMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HostMatcherTest extends PockTestCase
{
    public function testNotMatches(): void
    {
        self::assertFalse((new HostMatcher('test.com'))->matches(static::getTestRequest()));
    }

    public function testMatches(): void
    {
        self::assertTrue((new HostMatcher(self::TEST_HOST))->matches(static::getTestRequest()));
    }
}
