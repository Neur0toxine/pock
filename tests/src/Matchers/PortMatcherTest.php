<?php

/**
 * PHP version 7.3
 *
 * @category PortMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\HostMatcher;
use Pock\Matchers\PortMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class PortMatcherTest
 *
 * @category PortMatcherTest
 * @package  Pock\Tests\Matchers
 */
class PortMatcherTest extends PockTestCase
{
    public function testNotMatches(): void
    {
        self::assertFalse((new PortMatcher(80))->matches(static::getTestRequest()));
    }

    public function testMatches(): void
    {
        self::assertTrue((new PortMatcher(443))->matches(static::getTestRequest()));
    }
}
