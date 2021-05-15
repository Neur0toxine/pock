<?php

/**
 * PHP 7.3
 *
 * @category PockResponseBuilderTest
 * @package  Pock\Tests
 */

namespace Pock\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Pock\PockResponseBuilder;
use Pock\TestUtils\PockTestCase;
use Pock\TestUtils\SimpleObject;
use Pock\TestUtils\SimpleObjectJsonSerializable;

/**
 * Class PockResponseBuilderTest
 *
 * @category PockResponseBuilderTest
 * @package  Pock\Tests
 */
class PockResponseBuilderTest extends PockTestCase
{
    public function testBuildText(): void
    {
        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('test text')
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('test text', (string) $response->getBody());
    }

    public function testBuildResource(): void
    {
        $resource = fopen(__FILE__, 'r');
        self::assertIsResource($resource);

        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody($resource)
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertStringEqualsFile(__FILE__, (string) $response->getBody());

        fclose($resource);
    }

    public function testBuildStream(): void
    {
        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody(self::getPsr17Factory()->createStream('test text'))
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('test text', (string) $response->getBody());
    }

    public function testBuildFile(): void
    {
        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'text/plain')
            ->withFile(__FILE__)
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertStringEqualsFile(__FILE__, (string) $response->getBody());
    }

    /**
     * @dataProvider buildJsonProvider
     *
     * @param mixed  $data
     * @param string $expected
     *
     * @throws \Pock\Exception\JsonException
     */
    public function testBuildJson($data, string $expected): void
    {
        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withAddedHeader('Content-Type', 'application/json')
            ->withJson($data)
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['application/json']], $response->getHeaders());
        self::assertEquals($expected, (string) $response->getBody());
    }

    /**
     * @throws \Pock\Exception\JsonException
     */
    public function testBuildJsonException(): void
    {
        $this->expectExceptionMessage('Cannot serialize data with type NULL');
        (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(null);
    }

    /**
     * @dataProvider buildXmlProvider
     *
     * @param mixed  $data
     * @param string $expected
     *
     * @throws \Pock\Exception\XmlException
     */
    public function testBuildXml($data, string $expected): void
    {
        $response = (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeaders(['Content-Type' => 'text/xml'])
            ->withXml($data)
            ->getResponse();

        self::assertNotNull($response);
        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($expected, (string) $response->getBody());
    }

    /**
     * @throws \Pock\Exception\XmlException
     */
    public function testBuildXmlException(): void
    {
        $this->expectExceptionMessage('Cannot serialize data with type NULL');
        (new PockResponseBuilder(200))
            ->withStatusCode(400)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(null);
    }

    public function buildJsonProvider(): array
    {
        return [
            [1, '1'],
            ['{}', '{}'],
            [['key' => 'value'], '{"key":"value"}'],
            [new SimpleObjectJsonSerializable(), SimpleObjectJsonSerializable::JSON],
            [new SimpleObject(), SimpleObject::JSON]
        ];
    }

    public function buildXmlProvider(): array
    {
        $xmlArray = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry>
    <field><![CDATA[test]]></field>
  </entry>
</result>

EOF;

        return [
            [SimpleObject::XML, SimpleObject::XML],
            [new SimpleObject(), SimpleObject::XML],
            [[new SimpleObject()], $xmlArray]
        ];
    }
}
