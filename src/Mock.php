<?php

/**
 * PHP 7.2
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

    /** @var int */
    private $hits;

    /** @var int */
    private $maxHits;

    /**
     * Mock constructor.
     *
     * @param \Pock\Matchers\RequestMatcherInterface   $matcher
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param \Throwable|null                          $throwable
     * @param int                                      $maxHits
     */
    public function __construct(
        RequestMatcherInterface $matcher,
        ?ResponseInterface $response,
        ?Throwable $throwable,
        int $maxHits
    ) {
        $this->matcher = $matcher;
        $this->response = $response;
        $this->throwable = $throwable;
        $this->maxHits = $maxHits;
        $this->hits = 0;
    }

    /**
     * @inheritDoc
     */
    public function registerHit(): MockInterface
    {
        ++$this->hits;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function available(): bool
    {
        return $this->hits < $this->maxHits;
    }

    /**
     * @inheritDoc
     */
    public function getMatcher(): RequestMatcherInterface
    {
        return $this->matcher;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function getThrowable(): ?Throwable
    {
        return $this->throwable;
    }
}
