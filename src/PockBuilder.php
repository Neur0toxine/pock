<?php

/**
 * PHP 7.2
 *
 * @category PockBuilder
 * @package  Pock
 */

namespace Pock;

use DOMDocument;
use Pock\Exception\PockClientException;
use Pock\Exception\PockNetworkException;
use Pock\Exception\PockRequestException;
use Pock\Factory\CallbackReplyFactory;
use Pock\Factory\ReplyFactoryInterface;
use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\BodyMatcher;
use Pock\Matchers\CallbackRequestMatcher;
use Pock\Matchers\ExactFormDataMatcher;
use Pock\Matchers\ExactHeaderMatcher;
use Pock\Matchers\ExactHeadersMatcher;
use Pock\Matchers\ExactQueryMatcher;
use Pock\Matchers\FormDataMatcher;
use Pock\Matchers\HeaderLineMatcher;
use Pock\Matchers\HeaderLineRegexpMatcher;
use Pock\Matchers\HeaderMatcher;
use Pock\Matchers\HeadersMatcher;
use Pock\Matchers\HostMatcher;
use Pock\Matchers\JsonBodyMatcher;
use Pock\Matchers\MethodMatcher;
use Pock\Matchers\MultipartFormDataMatcher;
use Pock\Matchers\MultipleMatcher;
use Pock\Matchers\PathMatcher;
use Pock\Matchers\QueryMatcher;
use Pock\Matchers\RegExpBodyMatcher;
use Pock\Matchers\RegExpPathMatcher;
use Pock\Matchers\RegExpQueryMatcher;
use Pock\Matchers\RegExpUriMatcher;
use Pock\Matchers\RequestMatcherInterface;
use Pock\Matchers\SchemeMatcher;
use Pock\Matchers\UriMatcher;
use Pock\Matchers\XmlBodyMatcher;
use Pock\Traits\JsonDecoderTrait;
use Pock\Traits\JsonSerializerAwareTrait;
use Pock\Traits\XmlSerializerAwareTrait;
use Psr\Http\Client\ClientInterface;
use RuntimeException;
use Throwable;

/**
 * Class PockBuilder
 *
 * @category PockBuilder
 * @package  Pock
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PockBuilder
{
    use JsonDecoderTrait;
    use JsonSerializerAwareTrait;
    use XmlSerializerAwareTrait;

    /** @var \Pock\Matchers\MultipleMatcher */
    private $matcher;

    /** @var \Pock\PockResponseBuilder|null */
    private $responseBuilder;

    /** @var ReplyFactoryInterface|null */
    private $replyFactory;

    /** @var \Throwable|null */
    private $throwable;

    /** @var int */
    private $maxHits;

    /** @var int */
    private $matchAt;

    /** @var \Pock\MockInterface[] */
    private $mocks;

    /** @var \Psr\Http\Client\ClientInterface|null */
    private $fallbackClient;

    /**
     * PockBuilder constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Match request by its method.
     *
     * @param string $method
     *
     * @return self
     */
    public function matchMethod(string $method): self
    {
        return $this->addMatcher(new MethodMatcher($method));
    }

    /**
     * Match request by its scheme.
     *
     * @param string $scheme
     *
     * @return self
     */
    public function matchScheme(string $scheme): self
    {
        return $this->addMatcher(new SchemeMatcher($scheme));
    }

    /**
     * Matches request by hostname.
     *
     * @param string $host
     *
     * @return self
     */
    public function matchHost(string $host): self
    {
        return $this->addMatcher(new HostMatcher($host));
    }

    /**
     * Matches request by origin.
     *
     * @param string $origin
     *
     * @return self
     * @throws \RuntimeException
     */
    public function matchOrigin(string $origin): self
    {
        $parsed = parse_url($origin);

        if (!is_array($parsed)) {
            throw new RuntimeException('Malformed origin: ' . $origin);
        }

        if (array_key_exists('scheme', $parsed) && !empty($parsed['scheme'])) {
            $this->matchScheme($parsed['scheme']);
        }

        if (array_key_exists('host', $parsed) && !empty($parsed['host'])) {
            $this->matchHost($parsed['host']);
        }

        return $this;
    }

    /**
     * Matches request by the whole URI.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     *
     * @return self
     */
    public function matchUri($uri): self
    {
        return $this->addMatcher(new UriMatcher($uri));
    }

    /**
     * Matches request by the whole URI using regular expression.
     *
     * @param string $expression
     * @param int    $flags
     *
     * @return self
     */
    public function matchUriRegExp(string $expression, int $flags = 0): self
    {
        return $this->addMatcher(new RegExpUriMatcher($expression, $flags));
    }

    /**
     * Matches request by header value or several values. Header can have other values which are not specified here.
     * @see PockBuilder::matchExactHeader() if you want to match exact header values.
     *
     * @param string          $header
     * @param string|string[] $value
     *
     * @return self
     */
    public function matchHeader(string $header, $value): self
    {
        return $this->addMatcher(new HeaderMatcher($header, $value));
    }

    /**
     * Matches request by headers values or several values. Headers can have other values which are not specified here.
     * @see PockBuilder::matchExactHeaders() if you want to match exact headers collection.
     *
     * @param array<string, string|string[]> $headers
     *
     * @return self
     */
    public function matchHeaders(array $headers): self
    {
        return $this->addMatcher(new HeadersMatcher($headers));
    }

    /**
     * Matches request by the exact header pattern or values.
     *
     * @param string          $header
     * @param string|string[] $value
     *
     * @return self
     */
    public function matchExactHeader(string $header, $value): self
    {
        return $this->addMatcher(new ExactHeaderMatcher($header, $value));
    }

    /**
     * Matches request by headers values or several values.
     * Note: only host header will be dropped. Any other headers will not be excluded and can result in the problems
     * with the exact matching.
     *
     * @param array<string, string|string[]> $headers
     *
     * @return self
     */
    public function matchExactHeaders(array $headers): self
    {
        return $this->addMatcher(new ExactHeadersMatcher($headers));
    }

    /**
     * Matches request by the unparsed header line.
     *
     * @param string $header
     * @param string $value
     *
     * @return self
     */
    public function matchHeaderLine(string $header, string $value): self
    {
        return $this->addMatcher(new HeaderLineMatcher($header, $value));
    }

    /**
     * Matches request by the unparsed header line using provided regular expression.
     *
     * @param string $header
     * @param string $pattern
     *
     * @return self
     */
    public function matchHeaderLineRegexp(string $header, string $pattern): self
    {
        return $this->addMatcher(new HeaderLineRegexpMatcher($header, $pattern));
    }

    /**
     * Match request by its path. Path with and without slash at the start will be treated as the same path.
     * It's not the same for the path with slash at the end of it.
     *
     * @param string $path
     *
     * @return self
     */
    public function matchPath(string $path): self
    {
        return $this->addMatcher(new PathMatcher($path));
    }

    /**
     * Match request by its path using regular expression. This matcher doesn't care about prefix slash
     * since it's pretty easy to do it using regular expression.
     *
     * @param string $expression
     * @param int    $flags
     *
     * @return self
     */
    public function matchPathRegExp(string $expression, int $flags = 0): self
    {
        return $this->addMatcher(new RegExpPathMatcher($expression, $flags));
    }

    /**
     * Match request by its query. Request can contain other query variables.
     * @see PockBuilder::matchExactQuery() if you want to match an entire query string.
     *
     * @param array<string, mixed> $query
     *
     * @return self
     */
    public function matchQuery(array $query): self
    {
        return $this->addMatcher(new QueryMatcher($query));
    }

    /**
     * Match request by its query using regular expression.
     *
     * @param string $expression
     * @param int    $flags
     *
     * @return self
     */
    public function matchQueryRegExp(string $expression, int $flags = 0): self
    {
        return $this->addMatcher(new RegExpQueryMatcher($expression, $flags));
    }

    /**
     * Match request by its query. Additional query parameters aren't allowed.
     *
     * @param array<string, mixed> $query
     *
     * @return self
     */
    public function matchExactQuery(array $query): self
    {
        return $this->addMatcher(new ExactQueryMatcher($query));
    }

    /**
     * Match request with form-data.
     *
     * @param array<string, mixed> $formFields
     *
     * @return self
     */
    public function matchFormData(array $formFields): self
    {
        return $this->addMatcher(new FormDataMatcher($formFields));
    }

    /**
     * Match request with form-data. Additional fields aren't allowed.
     *
     * @param array<string, mixed> $formFields
     *
     * @return self
     */
    public function matchExactFormData(array $formFields): self
    {
        return $this->addMatcher(new ExactFormDataMatcher($formFields));
    }

    /**
     * Match request multipart form data. Will not match the request if body is not multipart.
     * Uses third-party library to parse the data.
     *
     * @param callable $callback Accepts Riverline\MultiPartParser\StreamedPart as an argument, returns true if matched.
     *
     * @return self
     * @see https://github.com/Riverline/multipart-parser#usage
     */
    public function matchMultipartFormData(callable $callback): self
    {
        return $this->addMatcher(new MultipartFormDataMatcher($callback));
    }

    /**
     * Match entire request body.
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $data
     *
     * @return self
     */
    public function matchBody($data): self
    {
        return $this->addMatcher(new BodyMatcher($data));
    }

    /**
     * Match entire request body using provided regular expression.
     *
     * @param string $expression
     * @param int    $flags
     *
     * @return self
     */
    public function matchBodyRegExp(string $expression, int $flags = 0): self
    {
        return $this->addMatcher(new RegExpBodyMatcher($expression, $flags));
    }

    /**
     * Match JSON request body.
     *
     * @param mixed $data
     *
     * @return self
     * @throws \Pock\Exception\JsonException
     */
    public function matchJsonBody($data): self
    {
        return $this->addMatcher(new JsonBodyMatcher(
            self::jsonDecode(
                self::serializeJson($data) ?? '',
                true
            )
        ));
    }

    /**
     * Match JSON request body against JSON string or array with data.
     *
     * @param array<int|string, mixed>|string $data
     *
     * @return self
     * @throws \Pock\Exception\JsonException
     */
    public function matchSerializedJsonBody($data): self
    {
        if (is_string($data)) {
            $data = self::jsonDecode($data, true);
        }

        return $this->addMatcher(new JsonBodyMatcher($data));
    }

    /**
     * Match XML request body using raw XML data.
     *
     * **Note:** this method will fallback to the string comparison if ext-xsl is not available.
     * It also doesn't serializer values with available XML serializer.
     * Use PockBuilder::matchSerializedXmlBody if you want to execute available serializer.
     *
     * @param DOMDocument|\Psr\Http\Message\StreamInterface|resource|string $data
     *
     * @return self
     * @throws \Pock\Exception\XmlException
     * @see \Pock\PockBuilder::matchSerializedXmlBody()
     *
     */
    public function matchXmlBody($data): self
    {
        return $this->addMatcher(new XmlBodyMatcher($data));
    }

    /**
     * Match XML request body.
     *
     * This method will try to use available XML serializer before matching.
     *
     * @phpstan-ignore-next-line
     * @param string|array|object $data
     *
     * @return self
     * @throws \Pock\Exception\XmlException
     */
    public function matchSerializedXmlBody($data): self
    {
        return $this->matchXmlBody(self::serializeXml($data) ?? '');
    }

    /**
     * Match request using provided callback. Callback should receive RequestInterface and return boolean.
     * If returned value is true then request is matched.
     *
     * @param callable $callback Callable that accepts PSR-7 RequestInterface as it's first argument.
     *
     * @return self
     */
    public function matchCallback(callable $callback): self
    {
        return $this->addMatcher(new CallbackRequestMatcher($callback));
    }

    /**
     * Add custom matcher to the mock.
     *
     * @param \Pock\Matchers\RequestMatcherInterface $matcher
     *
     * @return self
     */
    public function addMatcher(RequestMatcherInterface $matcher): self
    {
        $this->closePrevious();
        $this->matcher->addMatcher($matcher);

        return $this;
    }

    /**
     * Repeat this mock provided amount of times.
     * For example, if you pass 2 as an argument mock will be able to handle two identical requests.
     *
     * @param int $hits
     *
     * @return self
     */
    public function repeat(int $hits): self
    {
        $this->closePrevious();

        if ($hits > 0) {
            $this->maxHits = $hits;
        }

        return $this;
    }

    /**
     * Always execute this mock if matched. Mock with this call will not be expired ever.
     *
     * @return self
     */
    public function always(): self
    {
        $this->closePrevious();
        $this->maxHits = -1;

        return $this;
    }

    /**
     * Match request only at Nth hit. Previous matches will not be executed.
     *
     * **Note:** There IS a catch if you use this with the equal mocks. The test Client will not register hit
     * for the second mock and the second mock will be executed at N+1 time.
     *
     * For example, if you try to send 5 requests with this mocks and log response codes:
     * ```php
     * $builder = new PockBuilder();
     *
     * $builder->matchHost('example.com')->at(2)->reply(200);
     * $builder->matchHost('example.com')->at(4)->reply(201);
     * $builder->always()->reply(400);
     * ```
     *
     * You will get this: 400, 400, 200, 400, 400, 201
     * Instead of this: 400, 400, 200, 400, 201, 400
     *
     * @param int $hit
     *
     * @return self
     */
    public function at(int $hit): self
    {
        $this->closePrevious();
        $this->matchAt = $hit - 1;

        return $this;
    }

    /**
     * Throw an exception when request is being sent.
     *
     * @param \Throwable $throwable
     *
     * @return self
     */
    public function throwException(Throwable $throwable): self
    {
        $this->throwable = $throwable;

        return $this;
    }

    /**
     * Throw an ClientExceptionInterface instance with specified message
     *
     * @param string $message
     *
     * @return self
     */
    public function throwClientException(string $message = 'Pock ClientException'): self
    {
        return $this->throwException(new PockClientException($message));
    }

    /**
     * Throw an NetworkExceptionInterface instance with specified message
     *
     * @param string $message
     *
     * @return self
     */
    public function throwNetworkException(string $message = 'Pock NetworkException'): self
    {
        return $this->throwException(new PockNetworkException($message));
    }

    /**
     * Throw an RequestExceptionInterface instance with specified message
     *
     * @param string $message
     *
     * @return self
     */
    public function throwRequestException(string $message = 'Pock RequestException'): self
    {
        return $this->throwException(new PockRequestException($message));
    }

    /**
     * @param int $statusCode
     *
     * @return \Pock\PockResponseBuilder
     */
    public function reply(int $statusCode = 200): PockResponseBuilder
    {
        if (null === $this->responseBuilder) {
            $this->responseBuilder = new PockResponseBuilder($statusCode);

            return $this->responseBuilder;
        }

        return $this->responseBuilder->withStatusCode($statusCode);
    }

    /**
     * Construct the response during request execution using provided ReplytFactoryInterface implementation.
     *
     * @param \Pock\Factory\ReplyFactoryInterface $factory
     * @see ReplyFactoryInterface
     */
    public function replyWithFactory(ReplyFactoryInterface $factory): void
    {
        $this->replyFactory = $factory;
    }

    /**
     * Construct the response during request execution using provided callback.
     *
     * Callback should receive the same parameters as in the `ReplyFactoryInterface::createReply` method.
     *
     * @see ReplyFactoryInterface::createReply()
     *
     * @param callable $callback
     */
    public function replyWithCallback(callable $callback): void
    {
        $this->replyWithFactory(new CallbackReplyFactory($callback));
    }

    /**
     * Resets the builder.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->matcher = new MultipleMatcher();
        $this->replyFactory = null;
        $this->responseBuilder = null;
        $this->throwable = null;
        $this->maxHits = 1;
        $this->matchAt = -1;
        $this->mocks = [];

        return $this;
    }

    /**
     * Sets fallback Client. It will be used if no request can be matched.
     *
     * @param \Psr\Http\Client\ClientInterface|null $fallbackClient
     *
     * @return self
     */
    public function setFallbackClient(?ClientInterface $fallbackClient = null): self
    {
        $this->fallbackClient = $fallbackClient;
        return $this;
    }

    /**
     * @return \Pock\Client
     */
    public function getClient(): Client
    {
        $this->closePrevious();
        return new Client($this->mocks, $this->fallbackClient);
    }

    private function closePrevious(): void
    {
        if (null !== $this->responseBuilder || null !== $this->replyFactory || null !== $this->throwable) {
            if (0 === count($this->matcher)) {
                $this->matcher->addMatcher(new AnyRequestMatcher());
            }

            $response = null;

            if (null !== $this->responseBuilder) {
                $response = $this->responseBuilder->getResponse();
            }

            $this->mocks[] = new Mock(
                $this->matcher,
                $this->replyFactory,
                $response,
                $this->throwable,
                $this->maxHits,
                $this->matchAt
            );
            $this->matcher = new MultipleMatcher();
            $this->replyFactory = null;
            $this->responseBuilder = null;
            $this->throwable = null;
            $this->maxHits = 1;
            $this->matchAt = -1;
        }
    }
}
