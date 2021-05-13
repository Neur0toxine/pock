<?php

/**
 * PHP 7.3
 *
 * @category HttpRejectedPromise
 * @package  Pock\Promise
 */

namespace Pock\Promise;

use Http\Client\Exception;
use Http\Client\Promise\HttpFulfilledPromise;
use Http\Promise\Promise;
use Throwable;

/**
 * Class HttpRejectedPromise
 *
 * @category HttpRejectedPromise
 * @package  Pock\Promise
 */
class HttpRejectedPromise implements Promise
{
    /** @var \Throwable */
    private $throwable;

    /**
     * HttpRejectedPromise constructor.
     *
     * @param \Throwable $throwable
     */
    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * @inheritDoc
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null === $onRejected) {
            return $this;
        }

        try {
            return new HttpFulfilledPromise($onRejected($this->throwable));
        } catch (Exception $e) {
            return new self($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getState(): string
    {
        return Promise::REJECTED;
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function wait($unwrap = true): void
    {
        if ($unwrap) {
            throw $this->throwable;
        }
    }
}
