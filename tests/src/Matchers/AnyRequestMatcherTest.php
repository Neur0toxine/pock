<?php

/**
 * PHP 7.2
 *
 * @category AnyRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\AnyRequestMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class AnyRequestMatcherTest
 *
 * @category AnyRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */
class AnyRequestMatcherTest extends PockTestCase
{
    public function testMatches(): void
    {
        self::assertTrue((new AnyRequestMatcher())->matches(static::getTestRequest()));
    }
}
