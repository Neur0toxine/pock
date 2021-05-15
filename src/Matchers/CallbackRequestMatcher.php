<?php

/**
 * PHP 7.1
 *
 * @category CallbackRequestMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class CallbackRequestMatcher
 *
 * @category CallbackRequestMatcher
 * @package  Pock\Matchers
 */
class CallbackRequestMatcher implements RequestMatcherInterface
{
    /** @var callable */
    private $callback;

    /**
     * CallbackRequestMatcher constructor.
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
    public function matches(RequestInterface $request): bool
    {
        return call_user_func($this->callback, $request);
    }
}
