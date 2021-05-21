<?php

/**
 * PHP 7.3
 *
 * @category CallbackReplyFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\PockResponseBuilder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CallbackReplyFactory
 *
 * @category CallbackReplyFactory
 * @package  Pock\Factory
 */
class CallbackReplyFactory implements ReplyFactoryInterface
{
    /** @var callable */
    private $callback;

    /**
     * CallbackReplyFactory constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function createReply(RequestInterface $request, PockResponseBuilder $responseBuilder): ResponseInterface
    {
        return call_user_func($this->callback, $request, $responseBuilder);
    }
}
