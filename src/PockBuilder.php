<?php

/**
 * PHP 7.3
 *
 * @category PockBuilder
 * @package  Pock
 */

namespace Pock;

use Pock\Matchers\AnyRequestMatcher;
use Pock\Matchers\HostMatcher;
use Pock\Matchers\MultipleMatcher;
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
     * Matches request by hostname.
     *
     * @param string $host
     *
     * @return $this
     */
    public function matchHost(string $host): PockBuilder
    {
        $this->closePrevious();
        $this->matcher->addMatcher(new HostMatcher($host));

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
