<?php

/**
 * PHP 7.3
 *
 * @category AnyRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\AnyRequestMatcher;

/**
 * Class AnyRequestMatcherTest
 *
 * @category AnyRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */
class AnyRequestMatcherTest extends AbstractRequestMatcherTest
{
    public function testMatches(): void
    {
        self::assertTrue((new AnyRequestMatcher())->matches(static::getTestRequest()));
    }
}
