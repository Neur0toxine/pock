<?php

/**
 * PHP 7.1
 *
 * @category JsonBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Enum\RequestMethod;
use Pock\Matchers\JsonBodyMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class JsonBodyMatcherTest
 *
 * @category JsonBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */
class JsonBodyMatcherTest extends PockTestCase
{
    public function testInvalidJson(): void
    {
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream('test1'));
        $matcher = new JsonBodyMatcher(['field' => 'value']);

        self::assertFalse($matcher->matches($request));
    }

    public function testNoMatches(): void
    {
        $data = [
            'field' => [
                'items' => [
                    'another' => 'value'
                ]
            ]
        ];
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream(json_encode($data)));
        $data['field']['items']['another2'] = 'value2';
        $matcher = new JsonBodyMatcher($data);

        self::assertFalse($matcher->matches($request));
    }

    public function testMatches(): void
    {
        $data = [
            'field' => [
                'items' => [
                    'another' => 'value'
                ]
            ]
        ];
        $request = self::getTestRequest(RequestMethod::POST)
            ->withBody(self::getPsr17Factory()->createStream(json_encode($data)));
        $matcher = new JsonBodyMatcher($data);

        self::assertTrue($matcher->matches($request));
    }
}
