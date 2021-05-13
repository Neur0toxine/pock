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
    public function host(string $host): PockBuilder
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
    }

    public function getClient(): Client
    {
        return new Client($this->mocks);
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
