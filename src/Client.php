<?php

/**
 * PHP 7.3
 *
 * @category Client
 * @package  Pock
 */

namespace Pock;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Client\Promise\HttpFulfilledPromise;
use Http\Promise\Promise;
use Pock\Exception\BrokenMockException;
use Pock\Exception\UnsupportedRequestException;
use Pock\Promise\HttpRejectedPromise;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * @category Client
 * @package  Pock
 */
class Client implements ClientInterface, HttpClient, HttpAsyncClient
{
    /** @var \Pock\MockInterface[] */
    private $mocks;

    /**
     * Client constructor.
     *
     * @param \Pock\MockInterface[] $mocks
     */
    public function __construct(array $mocks)
    {
        $this->mocks = $mocks;
    }

    /**
     * @throws \Throwable
     */
    public function doSendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->sendAsyncRequest($request)->wait();
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->doSendRequest($request);
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function sendAsyncRequest(RequestInterface $request): Promise
    {
        foreach ($this->mocks as $mock) {
            if ($mock->isFired()) {
                continue;
            }

            if ($mock->getMatcher()->matches($request)) {
                if (null !== $mock->getResponse()) {
                    $mock->markAsFired();

                    return new HttpFulfilledPromise($mock->getResponse());
                }

                if (null !== $mock->getThrowable()) {
                    $mock->markAsFired();

                    return new HttpRejectedPromise($mock->getThrowable());
                }

                throw new BrokenMockException($mock);
            }
        }

        throw new UnsupportedRequestException();
    }
}