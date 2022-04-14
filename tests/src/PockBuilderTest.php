<?php

/**
 * PHP 7.1
 *
 * @category PockBuilderTest
 * @package  Pock\Tests
 */

namespace Pock\Tests;

use DOMDocument;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Exception\UnsupportedRequestException;
use Pock\Matchers\XmlBodyMatcher;
use Pock\PockBuilder;
use Pock\PockResponseBuilder;
use Pock\TestUtils\PockTestCase;
use Pock\TestUtils\SimpleObject;
use Pock\TestUtils\TestReplyFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Riverline\MultiPartParser\StreamedPart;
use RuntimeException;

/**
 * Class PockBuilderTest
 *
 * @category PockBuilderTest
 * @package  Pock\Tests
 */
class PockBuilderTest extends PockTestCase
{
    protected function setUp(): void
    {
        XmlBodyMatcher::$forceTextComparison = false;
    }

    public function testNoHit(): void
    {
        $this->expectException(UnsupportedRequestException::class);
        (new PockBuilder())->getClient()->sendRequest(self::getPsr17Factory()
            ->createRequest(RequestMethod::GET, self::TEST_URI));
    }

    public function testThrowClientException(): void
    {
        $this->expectException(ClientExceptionInterface::class);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->throwClientException();

        $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );
    }

    public function testThrowNetworkException(): void
    {
        $this->expectException(NetworkExceptionInterface::class);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->throwNetworkException();

        $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );
    }

    public function testThrowRequestException(): void
    {
        $this->expectException(RequestExceptionInterface::class);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->throwRequestException();

        $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );
    }

    public function testThrowRequestExceptionGetRequest(): void
    {
        $builder = new PockBuilder();
        $request = self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI);

        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->throwRequestException();

        try {
            $builder->getClient()->sendRequest($request);
        } catch (RequestExceptionInterface $exception) {
            self::assertEquals($request, $exception->getRequest());
        }
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

    public function testMatchOriginFailure(): void
    {
        $incorrectOrigin = RequestScheme::HTTPS . ':///' . self::TEST_HOST;

        $this->expectExceptionMessage($incorrectOrigin);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchOrigin($incorrectOrigin)
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, 'https://another-example.com')
        );
    }

    public function testMatchOrigin(): void
    {
        $origin = RequestScheme::HTTPS . '://' . self::TEST_HOST . ':443';
        $builder = new PockBuilder();

        $builder->matchMethod(RequestMethod::GET)
            ->matchOrigin($origin)
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, $origin)
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

    public function testMatchXmlString(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;
        $simpleObject = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>
EOF;

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchXmlBody($simpleObject)
            ->repeat(2)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream($simpleObject))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    public function testMatchXmlStream(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;
        $simpleObject = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>
EOF;

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchXmlBody(self::getPsr17Factory()->createStream($simpleObject))
            ->repeat(2)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream($simpleObject))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    public function testMatchXmlDOMDocument(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;
        $simpleObject = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>
EOF;

        $document = new DOMDocument();
        $document->loadXML($simpleObject);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchXmlBody($document)
            ->repeat(2)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream($simpleObject))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    /**
     * @dataProvider matchXmlNoXslProvider
     */
    public function testMatchXmlNoXsl(string $simpleObject, bool $expectException): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;

        if ($expectException) {
            $this->expectException(UnsupportedRequestException::class);
        }

        $document = new DOMDocument();
        $document->loadXML($simpleObject);

        XmlBodyMatcher::$forceTextComparison = true;

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchXmlBody($document)
            ->repeat(2)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream($simpleObject))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    public function testSerializedXmlResponse(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry><![CDATA[Forbidden]]></entry>
</result>

EOF;
        $simpleObjectFreeFormXml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>

<result>

  <field>
    <![CDATA[test]]>
    
    </field>
  
</result>
EOF;

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchSerializedXmlBody(new SimpleObject())
            ->repeat(2)
            ->reply(403)
            ->withHeader('Content-Type', 'text/xml')
            ->withXml(['error' => 'Forbidden']);

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream(
                    PHP_EOL . self::getXmlSerializer()->serialize(new SimpleObject()) . PHP_EOL
                ))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::GET, self::TEST_URI)
                ->withBody(self::getPsr17Factory()->createStream($simpleObjectFreeFormXml))
        );

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/xml']], $response->getHeaders());
        self::assertEquals($xml, $response->getBody()->getContents());
    }

    public function testMultipartFormDataMock(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::POST)
            ->matchScheme(RequestScheme::HTTPS)
            ->matchHost(self::TEST_HOST)
            ->matchMultipartFormData(function (StreamedPart $part) {
                return $part->isMultiPart() &&
                    1 === count($part->getPartsByName('param1')) &&
                    1 === count($part->getPartsByName('param2')) &&
                    'value1' === $part->getPartsByName('param1')[0]->getBody() &&
                    'value2' === $part->getPartsByName('param2')[0]->getBody() &&
                    'text/plain' === $part->getPartsByName('param1')[0]->getHeader('Content-Type');
            })->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('ok');

        $streamBuilder = (new MultipartStreamBuilder(self::getPsr17Factory()))
            ->addResource('param1', 'value1', ['headers' => ['Content-Type' => 'text/plain']])
            ->addResource('param2', 'value2');
        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::POST, self::TEST_URI)
                ->withHeader('Content-Type', 'multipart/form-data; boundary="' . $streamBuilder->getBoundary() .  '"')
                ->withBody($streamBuilder->build())
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
        self::assertEquals('ok', $response->getBody()->getContents());
    }

    public function testMatchBodyRegExp(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->matchBodyRegExp('/\d+-\d+/')
            ->reply(200);

        $response = $builder->getClient()->sendRequest(static::getRequestWithBody('test matchable 23-900'));

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMatchPathRegExp(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchOrigin(self::TEST_HOST)
            ->matchPathRegExp('/^\/?test$/')
            ->reply(200);

        $response = $builder->getClient()->sendRequest(
            static::getTestRequest()->withUri(static::getPsr17Factory()->createUri('https://test.com/test'))
        );

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMatchQueryRegExp(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchOrigin(self::TEST_HOST)
            ->matchQueryRegExp('/\d+-\d+/')
            ->reply(200);

        $response = $builder->getClient()->sendRequest(
            static::getTestRequest()->withUri(
                static::getPsr17Factory()->createUri(self::TEST_URI)
                    ->withQuery('param=23-900')
            )
        );

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMatchUriRegExp(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUriRegExp('/https\:\/\/\w+\.com\/\d+-\d+\?param=\d+-\d+/')
            ->reply(200);

        $response = $builder->getClient()->sendRequest(
            static::getTestRequest()->withUri(
                static::getPsr17Factory()->createUri('https://example.com/23-900')
                    ->withQuery('param=23-900')
            )
        );

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMatchFormData(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->matchFormData(['field1' => 'value1', 'field2' => 'value2'])
            ->reply(200);

        $response = $builder->getClient()->sendRequest(self::getRequestWithBody('field1=value1&field2=value2'));

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testMatchExactFormData(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->matchExactFormData(['field2' => 'value2'])
            ->reply(200);

        $response = $builder->getClient()->sendRequest(self::getRequestWithBody('field2=value2'));

        self::assertEquals(200, $response->getStatusCode());
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
            ->matchHeaders([
                'Authorization' => 'Token token_3',
                'Content-Type' => 'application/json'
            ])
            ->matchSerializedJsonBody('{"field": "value3"}')
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Third token (post json with match against serialized data)');

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
                ->withHeader('Authorization', 'Token token_3')
                ->withHeader('Content-Type', 'application/json')
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
                ->withBody(self::getPsr17Factory()->createStream('{"field": "value3"}'))
        );
        self::assertEquals(
            'Third token (post json with match against serialized data)',
            $response->getBody()->getContents()
        );

        $response = $client->sendRequest(
            self::getPsr17Factory()
                ->createRequest(RequestMethod::POST, self::TEST_URI)
                ->withHeader('Authorization', 'Token token_2')
                ->withUri(self::getPsr17Factory()->createUri(self::TEST_URI . 'ping'))
                ->withBody(self::getPsr17Factory()->createStream('test data'))
        );
        self::assertEquals('Second token (post)', $response->getBody()->getContents());
    }

    public function testAlways(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->always()
            ->reply(200)
            ->withHeader('Content-Type', 'text/plain')
            ->withBody('Successful');

        for ($i = 0; $i < 10; $i++) {
            $response = $builder->getClient()->sendRequest(
                self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
            );

            self::assertEquals(200, $response->getStatusCode());
            self::assertEquals(['Content-Type' => ['text/plain']], $response->getHeaders());
            self::assertEquals('Successful', $response->getBody()->getContents());
        }
    }

    public function testAt(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->at(2)
            ->reply(200);

        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->at(4)
            ->reply(201);

        $builder->always()->reply(400);
        $builder->getClient();

        for ($i = 0; $i < 5; $i++) {
            $response = $builder->getClient()->sendRequest(
                self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
            );

            self::assertEquals(1 === $i ? 200 : (4 === $i ? 201 : 400), $response->getStatusCode());
        }
    }

    public function testReplyWithFactory(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->always()
            ->replyWithFactory(new TestReplyFactory());

        for ($i = 0; $i < 5; $i++) {
            $response = $builder->getClient()->sendRequest(
                self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
            );

            self::assertEquals(200, $response->getStatusCode());
            self::assertEquals('Request #' . ($i + 1), $response->getBody()->getContents());
        }
    }

    public function testReplyWithCallback(): void
    {
        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->always()
            ->replyWithCallback(static function (RequestInterface $request, PockResponseBuilder $responseBuilder) {
                return $responseBuilder->withStatusCode(200)
                    ->withBody(self::TEST_URI)
                    ->getResponse();
            });

        $response = $builder->getClient()->sendRequest(
            self::getPsr17Factory()->createRequest(RequestMethod::GET, self::TEST_URI)
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(self::TEST_URI, $response->getBody()->getContents());
    }

    public function testReplyWithCallbackException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Exception from the callback');

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->always()
            ->replyWithCallback(static function (RequestInterface $request, PockResponseBuilder $responseBuilder) {
                throw new RuntimeException('Exception from the callback');
            });

        $builder->getClient()->sendRequest(self::getPsr17Factory()->createRequest(
            RequestMethod::GET,
            self::TEST_URI
        ));
    }

    public function testReplyWithClient(): void
    {
        $inlined = new PockBuilder();
        $inlined->reply(429);

        $builder = new PockBuilder();
        $builder->matchMethod(RequestMethod::GET)
            ->matchUri(self::TEST_URI)
            ->always()
            ->replyWithClient($inlined->getClient());

        $response = $builder->getClient()->sendRequest(self::getPsr17Factory()->createRequest(
            RequestMethod::GET,
            self::TEST_URI
        ));

        self::assertEquals(429, $response->getStatusCode());
    }

    public function matchXmlNoXslProvider(): array
    {
        $simpleObject = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>
EOF;

        return [
            [$simpleObject, true],
            [$simpleObject . "\n", false]
        ];
    }
}
