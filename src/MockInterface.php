<?php

/**
 * PHP 7.2
 *
 * @category MockInterface
 * @package  Pock
 */

namespace Pock;

use Pock\Matchers\RequestMatcherInterface;
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
     * Returns matcher for the request.
     *
     * @return \Pock\Matchers\RequestMatcherInterface
     */
    public function getMatcher(): RequestMatcherInterface;

    /**
     * Returns response which should be used as mock data.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;

    /**
     * Returns the throwable which will be thrown as mock data.
     *
     * @return \Throwable|null
     */
    public function getThrowable(): ?Throwable;
}
