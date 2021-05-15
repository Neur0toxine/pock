<?php

/**
 * PHP 7.1
 *
 * @category PathMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\PathMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class PathMatcherTest
 *
 * @category PathMatcherTest
 * @package  Pock\Tests\Matchers
 */
class PathMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $request = self::getTestRequest()->withUri(self::getTestRequest()->getUri()->withPath('/test/path'));
        $matcher = new PathMatcher('/test/path/here');

        self::assertFalse($matcher->matches($request));
    }

    /**
     * @dataProvider matchesProvider
     */
    public function testMatches(string $expected, string $actual): void
    {
        $request = self::getTestRequest()->withUri(self::getTestRequest()->getUri()->withPath($actual));
        $matcher = new PathMatcher($expected);

        self::assertTrue($matcher->matches($request));
    }

    public function matchesProvider(): array
    {
        return [
            ['/test/path', '/test/path'],
            ['/test/path', 'test/path'],
            ['test/path', '/test/path'],
            ['test/path', 'test/path']
        ];
    }
}
