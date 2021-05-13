<?php

/**
 * PHP 7.3
 *
 * @category Mock
 * @package  Pock
 */

namespace Pock;

use Pock\Matchers\RequestMatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class Mock
 *
 * @category Mock
 * @package  Pock
 */
class Mock implements MockInterface
{
    /** @var \Pock\Matchers\RequestMatcherInterface */
    private $matcher;

    /** @var \Psr\Http\Message\ResponseInterface|null */
    private $response;

    /** @var \Throwable|null */
    private $throwable;

    /** @var bool */
    private $fired = false;

    /**
     * Mock constructor.
     *
     * @param \Pock\Matchers\RequestMatcherInterface   $matcher
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param \Throwable|null                          $throwable
     */
    public function __construct(RequestMatcherInterface $matcher, ?ResponseInterface $response, ?Throwable $throwable)
    {
        $this->matcher = $matcher;
        $this->response = $response;
        $this->throwable = $throwable;
    }

    public function markAsFired(): MockInterface
    {
        $this->fired = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFired(): bool
    {
        return $this->fired;
    }

    /**
     * @return \Pock\Matchers\RequestMatcherInterface
     */
    public function getMatcher(): RequestMatcherInterface
    {
        return $this->matcher;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return \Throwable|null
     */
    public function getThrowable(): ?Throwable
    {
        return $this->throwable;
    }
}
