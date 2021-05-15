<?php

/**
 * PHP 7.3
 *
 * @category PockBuilderTest
 * @package  Pock\Tests
 */

namespace Pock\Tests;

use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Exception\UnsupportedRequestException;
use Pock\PockBuilder;
use Pock\TestUtils\PockTestCase;

/**
 * Class PockBuilderTest
 *
 * @category PockBuilderTest
 * @package  Pock\Tests
 */
class PockBuilderTest extends PockTestCase
{
    public function testNoHit(): void
    {
        $this->expectException(UnsupportedRequestException::class);
        (new PockBuilder())->getClient()->sendRequest(self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com'));
    }

    public function testTextResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost('example.com')
            ->reply(403)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Forbidden');

        $response = $builder->getClient()->sendRequest(self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com'));

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Forbidden', $response->getBody()->getContents());
    }

    public function testJsonResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost('example.com')
            ->reply(403)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com'));

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['application/json']], $response->getHeaders());
        self::assertEquals(['error' => 'Forbidden'], json_decode($response->getBody()->getContents(), true));
    }

    public function testXmlResponse(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;


        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost('example.com')
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com'));

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }
}
