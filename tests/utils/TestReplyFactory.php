<?php

/**
 * PHP 7.3
 *
 * @category TestReplyFactory
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use Pock\Factory\ReplyFactoryInterface;
use Pock\PockResponseBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TestReplyFactory
 *
 * @category TestReplyFactory
 * @package  Pock\TestUtils
 */
class TestReplyFactory implements ReplyFactoryInterface
{
    /** @var int */
    private $requestNumber = 0;

    /**
     * @inheritDoc
     */
    public function createReply(RequestInterface $request, PockResponseBuilder $responseBuilder): ResponseInterface
    {
        return $responseBuilder->withStatusCode(200)
            ->withBody('Request #' . ++$this->requestNumber)
            ->getResponse();
    }
}
