<?php

/**
 * PHP 7.2
 *
 * @category Client
 * @package  Pock
 */

namespace Pock;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Client\Promise\HttpFulfilledPromise;
use Http\Promise\Promise;
use Pock\Exception\IncompleteMockException;
use Pock\Exception\UnsupportedRequestException;
use Pock\Promise\HttpRejectedPromise;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

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

    /** @var \Psr\Http\Client\ClientInterface|null */
    private $fallbackClient;

    /**
     * Client constructor.
     *
     * @param \Pock\MockInterface[] $mocks
     */
    public function __construct(array $mocks, ?ClientInterface $fallbackClient = null)
    {
        $this->mocks = $mocks;
        $this->fallbackClient = $fallbackClient;
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
            if (!$mock->available()) {
                continue;
            }

            if ($mock->getMatcher()->matches($request)) {
                if (null !== $mock->getResponse()) {
                    $mock->registerHit();

                    return new HttpFulfilledPromise($mock->getResponse());
                }

                if (null !== $mock->getThrowable()) {
                    $mock->registerHit();

                    return new HttpRejectedPromise($mock->getThrowable());
                }

                throw new IncompleteMockException($mock);
            }
        }

        if (null !== $this->fallbackClient) {
            try {
                return new HttpFulfilledPromise($this->fallbackClient->sendRequest($request));
            } catch (Throwable $throwable) {
                return new HttpRejectedPromise($throwable);
            }
        }

        throw new UnsupportedRequestException();
    }
}
