<?php

/**
 * PHP 7.2
 *
 * @category PockBuilder
 * @package  Pock
 */

namespace Pock;

use Diff\ArrayComparer\StrictArrayComparer;
use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\BodyMatcher;
use Pock\Matchers\CallbackRequestMatcher;
use Pock\Matchers\ExactHeaderMatcher;
use Pock\Matchers\ExactHeadersMatcher;
use Pock\Matchers\ExactQueryMatcher;
use Pock\Matchers\HeaderLineMatcher;
use Pock\Matchers\HeaderLineRegexpMatcher;
use Pock\Matchers\HeaderMatcher;
use Pock\Matchers\HeadersMatcher;
use Pock\Matchers\HostMatcher;
use Pock\Matchers\JsonBodyMatcher;
use Pock\Matchers\MethodMatcher;
use Pock\Matchers\MultipleMatcher;
use Pock\Matchers\PathMatcher;
use Pock\Matchers\QueryMatcher;
use Pock\Matchers\RequestMatcherInterface;
use Pock\Matchers\SchemeMatcher;
use Pock\Matchers\UriMatcher;
use Pock\Traits\JsonDecoderTrait;
use Pock\Traits\JsonSerializerAwareTrait;
use Pock\Traits\XmlSerializerAwareTrait;
use Psr\Http\Client\ClientInterface;
use Throwable;

/**
 * Class PockBuilder
 *
 * @category PockBuilder
 * @package  Pock
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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

    /** @var \Throwable|null */
    private $throwable;

    /** @var int */
    private $maxHits;

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
     * Match XML request body.
     *
     * **Note:** this method will use string comparison for now. It'll be improved in future.
     *
     * @todo Don't use simple string comparison. Match the entire body by its DOM.
     *
     * @param mixed $data
     *
     * @return self
     * @throws \Pock\Exception\XmlException
     */
    public function matchXmlBody($data): self
    {
        return $this->matchBody(self::serializeXml($data) ?? '');
    }

    /**
     * Match request using provided callback. Callback should receive RequestInterface and return boolean.
     * If returned value is true then request is matched.
     *
     * @param callable $callback
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
        if ($hits > 0) {
            $this->maxHits = $hits;
        }

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
     * Resets the builder.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->matcher = new MultipleMatcher();
        $this->responseBuilder = null;
        $this->throwable = null;
        $this->maxHits = 1;
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
        if (null !== $this->responseBuilder || null !== $this->throwable) {
            if (0 === count($this->matcher)) {
                $this->matcher->addMatcher(new AnyRequestMatcher());
            }

            $response = null;

            if (null !== $this->responseBuilder) {
                $response = $this->responseBuilder->getResponse();
            }

            $this->mocks[] = new Mock(
                $this->matcher,
                $response,
                $this->throwable,
                $this->maxHits
            );
            $this->matcher = new MultipleMatcher();
            $this->responseBuilder = null;
            $this->throwable = null;
            $this->maxHits = 1;
        }
    }
}
