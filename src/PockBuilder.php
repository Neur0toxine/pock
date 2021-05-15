<?php

/**
 * PHP 7.2
 *
 * @category PockBuilder
 * @package  Pock
 */

namespace Pock;

use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\HostMatcher;
use Pock\Matchers\MethodMatcher;
use Pock\Matchers\MultipleMatcher;
use Pock\Matchers\RequestMatcherInterface;
use Pock\Matchers\SchemeMatcher;
use Pock\Matchers\UriMatcher;
use Psr\Http\Client\ClientInterface;

/**
 * Class PockBuilder
 *
 * @category PockBuilder
 * @package  Pock
 */
class PockBuilder
{
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
     * @return $this
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
     * @return $this
     */
    public function repeat(int $hits): self
    {
        if ($hits > 0) {
            $this->maxHits = $hits;
        }

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
