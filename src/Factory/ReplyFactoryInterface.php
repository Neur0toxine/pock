<?php

/**
 * PHP 7.3
 *
 * @category ReplyFactoryInterface
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\PockResponseBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ReplyFactoryInterface
 *
 * @category ReplyFactoryInterface
 * @package  Pock\Factory
 */
interface ReplyFactoryInterface
{
    /**
     * Reply to the specified request.
     *
     * If this method throws any exception, it will be treated as with the `PockBuilder::throwException call`.
     *
     * @see \Pock\PockBuilder::throwException()
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Pock\PockResponseBuilder          $responseBuilder
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Throwable
     */
    public function createReply(RequestInterface $request, PockResponseBuilder $responseBuilder): ResponseInterface;
}
