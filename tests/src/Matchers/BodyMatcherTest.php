<?php

/**
 * PHP 7.1
 *
 * @category BodyMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Enum\RequestMethod;
use Pock\Matchers\BodyMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class BodyMatcherTest
 *
 * @category BodyMatcherTest
 * @package  Pock\Tests\Matchers
 */
class BodyMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream('test1'));
        $matcher = new BodyMatcher('test');

        self::assertFalse($matcher->matches($request));
    }

    public function testMatchesString(): void
    {
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream('test'));
        $matcher = new BodyMatcher('test');

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchesStream(): void
    {
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream('test'));
        $matcher = new BodyMatcher(self::getPsr17Factory()->createStream('test'));

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchesResource(): void
    {
        $resource = fopen(__FILE__, 'rb');
        $request = self::getTestRequest(RequestMethod::POST)->withBody(self::getPsr17Factory()->createStream(
            stream_get_contents($resource)
        ));
        $matcher = new BodyMatcher($resource);

        self::assertTrue($matcher->matches($request));
    }
}
