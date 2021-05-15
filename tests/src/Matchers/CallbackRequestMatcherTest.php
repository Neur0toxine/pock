<?php

/**
 * PHP 7.1
 *
 * @category CallbackRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\CallbackRequestMatcher;
use Pock\TestUtils\PockTestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class CallbackRequestMatcherTest
 *
 * @category CallbackRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */
class CallbackRequestMatcherTest extends PockTestCase
{
    public function testMatches(): void
    {
        $matcher = new CallbackRequestMatcher(function (RequestInterface $request) {
            return '' !== $request->getUri()->getQuery();
        });

        self::assertFalse($matcher->matches(self::getTestRequest()));
        self::assertTrue($matcher->matches(
            self::getTestRequest()
                ->withUri(
                    self::getTestRequest()
                        ->getUri()
                        ->withQuery('param=value')
                )
        ));
    }
}
