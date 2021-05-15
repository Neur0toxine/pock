<?php

/**
 * PHP 7.1
 *
 * @category ExactHeadersMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\ExactHeadersMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class ExactHeadersMatcherTest
 *
 * @category ExactHeadersMatcherTest
 * @package  Pock\Tests\Matchers
 */
class ExactHeadersMatcherTest extends PockTestCase
{
    public function testNoMatch(): void
    {
        $matcher = new ExactHeadersMatcher(['x-test-header' => 'test value']);
        self::assertFalse($matcher->matches(self::getTestRequest()));
    }

    public function testMatchStringValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new ExactHeadersMatcher(['x-test-header' => 'test value']);

        self::assertTrue($matcher->matches($request));
    }

    public function testMatchArrayValue(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', 'test value');
        $matcher = new ExactHeadersMatcher(['x-test-header' => ['test value']]);

        self::assertTrue($matcher->matches($request));
    }

    public function testNotMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader('x-test-header', ['test value1', 'test value2']);
        $matcher = new ExactHeadersMatcher(['x-test-header' => ['test value1']]);

        self::assertFalse($matcher->matches($request));
    }

    public function testNoMatchArrayValues(): void
    {
        $request = self::getTestRequest()->withHeader(
            'x-test-header',
            ['test value1', 'test value2', 'test value 3']
        );
        $matcher = new ExactHeadersMatcher(['x-test-header' => ['test value2', 'test value1']]);

        self::assertFalse($matcher->matches($request));
    }
}
