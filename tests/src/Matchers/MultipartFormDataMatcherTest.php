<?php

/**
 * PHP version 7.3
 *
 * @category MultipartFormDataMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Http\Message\MultipartStream\MultipartStreamBuilder;
use Pock\Matchers\MultipartFormDataMatcher;
use Pock\TestUtils\PockTestCase;
use Psr\Http\Message\RequestInterface;
use Riverline\MultiPartParser\StreamedPart;

/**
 * Class MultipartFormDataMatcherTest
 *
 * @category MultipartFormDataMatcherTest
 * @package  Pock\Tests\Matchers
 */
class MultipartFormDataMatcherTest extends PockTestCase
{
    public function testNoMatchesNotMultipart(): void
    {
        $matcher = new MultipartFormDataMatcher(function (StreamedPart $part) {
            return $part->isMultiPart();
        });

        self::assertFalse($matcher->matches(self::getTestRequest()));
        self::assertFalse($matcher->matches(self::getRequestWithBody('param=value&param2=value')));
    }

    public function testMatches(): void
    {
        $matcher = new MultipartFormDataMatcher(function (StreamedPart $part) {
            return $part->isMultiPart() &&
                1 === count($part->getPartsByName('param1')) &&
                1 === count($part->getPartsByName('param2')) &&
                'value1' === $part->getPartsByName('param1')[0]->getBody() &&
                'value2' === $part->getPartsByName('param2')[0]->getBody() &&
                'text/plain' === $part->getPartsByName('param1')[0]->getHeader('Content-Type');
        });
        $builder = new MultipartStreamBuilder(self::getPsr17Factory());
        $builder->addResource('param1', 'value1', ['headers' => ['Content-Type' => 'text/plain']])
            ->addResource('param2', 'value2');

        self::assertTrue($matcher->matches(self::getMultipartRequest($builder)));
    }

    private static function getMultipartRequest(MultipartStreamBuilder $builder): RequestInterface
    {
        return self::getPsr17Factory()->createRequest('POST', 'https://example.com')
            ->withHeader('Content-Type', 'multipart/form-data; boundary="' . $builder->getBoundary() .  '"')
            ->withBody($builder->build());
    }
}
