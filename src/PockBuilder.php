<?php

/**
 * PHP 7.3
 *
 * @category PockBuilder
 * @package  Pock
 */

namespace Pock;

use Pock\Enum\RequestScheme;
use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\HostMatcher;
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

    /** @var \Psr\Http\Message\ResponseInterface|null */
    private $response;

    /** @var \Throwable|null */
    private $throwable;

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
     * Match request by its scheme.
     *
     * @param string $scheme
     *
     * @return $this
     */
    public function matchScheme(string $scheme = RequestScheme::HTTP): PockBuilder
    {
        return $this->addMatcher(new SchemeMatcher($scheme));
    }

    /**
     * Matches request by hostname.
     *
     * @param string $host
     *
     * @return $this
     */
    public function matchHost(string $host): PockBuilder
    {
        return $this->addMatcher(new HostMatcher($host));
    }

    /**
     * Matches request by the whole URI.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     *
     * @return \Pock\PockBuilder
     */
    public function matchUri($uri): PockBuilder
    {
        return $this->addMatcher(new UriMatcher($uri));
    }

    /**
     * Add custom matcher to the mock.
     *
     * @param \Pock\Matchers\RequestMatcherInterface $matcher
     *
     * @return \Pock\PockBuilder
     */
    public function addMatcher(RequestMatcherInterface $matcher): PockBuilder
    {
        $this->closePrevious();
        $this->matcher->addMatcher($matcher);

        return $this;
    }

    /**
     * Resets the builder.
     *
     * @return \Pock\PockBuilder
     */
    public function reset(): PockBuilder
    {
        $this->matcher = new MultipleMatcher();
        $this->response = null;
        $this->throwable = null;
        $this->mocks = [];

        return $this;
    }

    /**
     * Sets fallback Client. It will be used if no request can be matched.
     *
     * @param \Psr\Http\Client\ClientInterface|null $fallbackClient
     *
     * @return \Pock\PockBuilder
     */
    public function setFallbackClient(?ClientInterface $fallbackClient = null): PockBuilder
    {
        $this->fallbackClient = $fallbackClient;
        return $this;
    }

    /**
     * @return \Pock\Client
     */
    public function getClient(): Client
    {
        return new Client($this->mocks, $this->fallbackClient);
    }

    private function closePrevious(): void
    {
        if (null !== $this->response || null !== $this->throwable) {
            if (0 === count($this->matcher)) {
                $this->matcher->addMatcher(new AnyRequestMatcher());
            }

            $this->mocks[] = new Mock($this->matcher, $this->response, $this->throwable);
            $this->response = null;
            $this->throwable = null;
        }
    }
}
