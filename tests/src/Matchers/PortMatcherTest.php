<?php

/**
 * PHP version 7.3
 *
 * @category PortMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Nyholm\Psr7\Uri;
use Pock\Enum\RequestScheme;
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

    public function testMatchesWithoutProto(): void
    {
        self::assertTrue((new PortMatcher(80))->matches(static::getTestRequest()->withUri(new class extends Uri {
            public function getScheme(): string
            {
                return RequestScheme::HTTP;
            }

            public function getPort(): ?int
            {
                return null;
            }
        })));
        self::assertTrue((new PortMatcher(443))->matches(static::getTestRequest()->withUri(new class extends Uri {
            public function getScheme(): string
            {
                return RequestScheme::HTTPS;
            }

            public function getPort(): ?int
            {
                return null;
            }
        })));
    }
}
