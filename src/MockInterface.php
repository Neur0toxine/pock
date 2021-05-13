<?php

/**
 * PHP 7.3
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
     * Marks mock as already used.
     *
     * @return \Pock\MockInterface
     */
    public function markAsFired(): MockInterface;

    /**
     * Returns true if mock was not used yet.
     *
     * @return bool
     */
    public function isFired(): bool;

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
