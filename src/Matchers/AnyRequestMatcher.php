<?php

/**
 * PHP 7.3
 *
 * @category AnyRequestMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class AnyRequestMatcher
 *
 * @category AnyRequestMatcher
 * @package  Pock\Matchers
 */
class AnyRequestMatcher implements RequestMatcherInterface
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return true;
    }
}
