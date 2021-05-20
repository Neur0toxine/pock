<?php

/**
 * PHP 7.2
 *
 * @category MockInterface
 * @package  Pock
 */

namespace Pock;

use Pock\Matchers\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Interface MockInterface
 *
 * @category MockInterface
 * @package  Pock
 */
interface MockInterface
{
    /**
     * Registers a hit to the mock.
     *
     * @return \Pock\MockInterface
     */
    public function registerHit(): MockInterface;

    /**
     * Returns true if mock is still can be used.
     *
     * @return bool
     */
    public function available(): bool;

    /**
     * Returns true if underlying matcher has matched provided request.
     * It also returns false if matcher has matched request but hits condition is not met yet.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    public function matches(RequestInterface $request): bool;

    /**
     * Returns response which should be used as mock data.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;

    /**
     * Returns the throwable which will be thrown as mock data.
     *
     * @param \Psr\Http\Message\RequestInterface $request This request may be set into exception if possible
     *
     * @return \Throwable|null
     */
    public function getThrowable(RequestInterface $request): ?Throwable;
}
