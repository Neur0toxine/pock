<?php

/**
 * PHP 7.2
 *
 * @category MultipleMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Enum\RequestMethod;
use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\HostMatcher;
use Pock\Matchers\MultipleMatcher;

/**
 * Class MultipleMatcherTest
 *
 * @category MultipleMatcherTest
 * @package  Pock\Tests\Matchers
 */
class MultipleMatcherTest extends AbstractRequestMatcherTest
{
    public function testMatches(): void
    {
        $matcher = new MultipleMatcher([new HostMatcher(self::TEST_HOST)]);
        $matcher->addMatcher(new AnyRequestMatcher());

        self::assertTrue($matcher->matches(static::getTestRequest()));
        self::assertFalse($matcher->matches(static::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://test.com')));
    }
}
