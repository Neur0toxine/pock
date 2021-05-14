<?php

/**
 * PHP 7.2
 *
 * @category HostMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HostMatcher;

/**
 * Class HostMatcherTest
 *
 * @category HostMatcherTest
 * @package  Pock\Tests\Matchers
 */
class HostMatcherTest extends AbstractRequestMatcherTest
{
    public function testMatches(): void
    {
        self::assertTrue((new HostMatcher(self::TEST_HOST))->matches(static::getTestRequest()));
    }
}
