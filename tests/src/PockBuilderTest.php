<?php

/**
 * PHP 7.1
 *
 * @category PockBuilderTest
 * @package  Pock\Tests
 */

namespace Pock\Tests;

use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Exception\UniversalMockException;
use Pock\Exception\UnsupportedRequestException;
use Pock\PockBuilder;
use Pock\TestUtils\PockTestCase;
use Pock\TestUtils\SimpleObject;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

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
            ->createRequest(RequestMethod::GET, self::TEST_URI));
    }

    public function testThrowException(): void
    {
        $this->expectException(ClientExceptionInterface::class);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->throwException(new UniversalMockException('Boom!'));

        $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );
    }

    public function testMatchHeader(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchHeader('Authorization', 'Token token')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token')
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Successful', $response->getBody()->getContents());
    }

    public function testMatchHeaders(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchHeaders(['Authorization' => 'Token token'])
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token')
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Successful', $response->getBody()->getContents());
    }

    public function testMatchExactHeader(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchExactHeader('Authorization', ['Token token', 'Token second_token'])
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', ['Token token', 'Token second_token'])
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Successful', $response->getBody()->getContents());
    }

    public function testMatchHeaderLine(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchHeaderLine('Authorization', 'Token token, Token second_token')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', ['Token token', 'Token second_token'])
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Successful', $response->getBody()->getContents());
    }

    public function testMatchHeaderLineRegexp(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchHeaderLineRegexp('Authorization', '/^Token [a-z_]+$/')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token')
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Successful', $response->getBody()->getContents());
    }

    public function testMatchPathResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/test')
            ->reply(403)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Forbidden');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'test'))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Forbidden', $response->getBody()->getContents());
    }

    public function testMatchCallback(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/test')
            ->matchCallback(static function (RequestInterface $request) {
                return '' === $request->getUri()->getQuery();
            })
            ->reply(403)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Forbidden');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'test'))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Forbidden', $response->getBody()->getContents());
    }

    public function testTextResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->reply(403)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Forbidden');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('Forbidden', $response->getBody()->getContents());
    }

    public function testJsonResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->reply(403)
            ->withHeader('Content-Type', 'application/json')
            ->withJson(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['application/json']], $response->getHeaders());
        self::assertEquals(['error' => 'Forbidden'], json_decode($response->getBody()->getContents(), true));
    }

    public function testJsonObjectArrayResponse(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->reply(403)
            ->withHeader('Content-Type', 'application/json')
            ->withJson([
                new SimpleObject(),
                new SimpleObject()
            ]);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['application/json']], $response->getHeaders());
        self::assertEquals([
            ['field' => 'test'],
            ['field' => 'test']
        ], json_decode($response->getBody()->getContents(), true));
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
            ->matchHost(self::TEST_HOST)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    public function testFirstExampleApiMock(): void
    {
        $data = [
            [
                'name' => 'John Doe',
                'username' => 'john',
                'email' => 'john@example.com'
            ],
            [
                'name' => 'Jane Doe',
                'username' => 'jane',
                'email' => 'jane@example.com'
            ],
        ];
        $builder = new PockBuilder();

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost('example.com')
            ->matchPath('/api/v1/users')
            ->matchHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic YWxhZGRpbjpvcGVuc2VzYW1l'
            ])
            ->reply(200)
            ->withHeader('Content-Type', 'application/json')
            ->withJson($data);

        $request = self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com/api/v1/users')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', 'Basic YWxhZGRpbjpvcGVuc2VzYW1l');
        $response = $builder->getClient()->sendRequest($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals(json_encode($data), $response->getBody()->getContents());
    }

    public function testSecondExampleApiMock(): void
    {
        $data = [
            [
                'name' => 'John Doe',
                'username' => 'john',
                'email' => 'john@example.com'
            ],
            [
                'name' => 'Jane Doe',
                'username' => 'jane',
                'email' => 'jane@example.com'
            ],
        ];
        $builder = new PockBuilder();

        $builder->matchMethod(RequestMethod::GET)
            ->matchUri('https://example.com/api/v1/users')
            ->matchHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic YWxhZGRpbjpvcGVuc2VzYW1l'
            ])
            ->reply(200)
            ->withHeader('Content-Type', 'application/json')
            ->withJson($data);

        $request = self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, 'https://example.com/api/v1/users')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', 'Basic YWxhZGRpbjpvcGVuc2VzYW1l');
        $response = $builder->getClient()->sendRequest($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals(json_encode($data), $response->getBody()->getContents());
    }

    public function testSeveralMocks(): void
    {
        $builder = new PockBuilder();

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchHeader('Authorization', 'Token token_1')
            ->repeat(2)
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('First token');

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchHeader('Authorization', 'Token token_2')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Second token');

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchExactQuery(['param1' => 'value'])
            ->matchHeader('Authorization', 'Token token_2')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Second token (exact query params)');

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchQuery(['param1' => 'value'])
            ->matchHeader('Authorization', 'Token token_2')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Second token (query params)');

        $builder->matchMethod(RequestMethod::POST)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchHeaders([
                'Authorization' => 'Token token_2',
                'Content-Type' => 'application/json'
            ])
            ->matchJsonBody(['field' => 'value'])
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Second token (post json)');

        $builder->matchMethod(RequestMethod::POST)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchPath('/ping')
            ->matchHeader('Authorization', 'Token token_2')
            ->matchBody('test data')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Second token (post)');

        $client = $builder->getClient();

        for ($i = 0; $i < 2; $i++) {
            $response = $client->sendRequest(
                self::getPsr17Factory()
                    ->createRequest(RequestMethod::GET, self::TEST_URI)
                    ->withHeader('Authorization', 'Token token_1')
                    ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
            );
            self::assertEquals(
                'First token',
                $response->getBody()->getContents(),
                'Attempt #' . ($i + 1) . ' for repeatable mock'
            );
        }

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
        );
        self::assertEquals('Second token', $response->getBody()->getContents());

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withUri(
                    self::getPsr17Factory()
                        ->createUri(self::TEST_URI . 'ping')
                        ->withQuery('param1=value')
                )
        );
        self::assertEquals('Second token (exact query params)', $response->getBody()->getContents());

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withUri(
                    self::getPsr17Factory()
                        ->createUri(self::TEST_URI . 'ping')
                        ->withQuery('param1=value&param2=value')
                )
        );
        self::assertEquals('Second token (query params)', $response->getBody()->getContents());

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::POST, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withHeader('Content-Type', 'application/json')
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
                ->withBody(self::getPsr17Factory()->createStream('{"field": "value"}'))
        );
        self::assertEquals('Second token (post json)', $response->getBody()->getContents());

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::POST, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
                ->withBody(self::getPsr17Factory()->createStream('test data'))
        );
        self::assertEquals('Second token (post)', $response->getBody()->getContents());
    }
}
