<?php

/**
 * PHP 7.1
 *
 * @category ExactQueryMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\ExactQueryMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class ExactQueryMatcherTest
 *
 * @category ExactQueryMatcherTest
 * @package  Pock\Tests\Matchers
 */
class ExactQueryMatcherTest extends PockTestCase
{
    public function testNoMatches(): void
    {
        $request = self::getTestRequest()->withUri(self::getTestRequest()->getUri());
        $matcher = new ExactQueryMatcher(['var' => 'ok']);

        self::assertFalse($matcher->matches($request));
    }

    /**
     * @dataProvider matchesProvider
     */
    public function testMatches(array $expected, string $actual, bool $result): void
    {
        $request = self::getTestRequest()->withUri(
            self::getTestRequest()
                ->getUri()
                ->withQuery($actual)
        );
        $matcher = new ExactQueryMatcher($expected);

        self::assertEquals($result, $matcher->matches($request));
    }

    public function matchesProvider(): array
    {
        return [
            [
                ['var' => 'ok'],
                'var=ok',
                true
            ],
            [
                ['var' => 'ok'],
                'var=ok&var1=true',
                false
            ],
            [
                [
                    'var' => 'ok',
                    'var1' => 'true',
                ],
                'var=ok&var1=true',
                true
            ],
            [
                [
                    'var' => 'ok',
                    'var1' => 'true',
                ],
                'var=ok',
                false
            ],
            [
                [
                    'var' => 'ok',
                    'x' => [
                        0 => 'alpha',
                        1 => 'beta',
                        'gamma' => ['lambda']
                    ],
                ],
                'var=ok&x[]=alpha&x[]=beta&x[gamma][]=lambda',
                true
            ],
        ];
    }
}
