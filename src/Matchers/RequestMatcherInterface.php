<?php

/**
 * PHP 7.3
 *
 * @category RequestMatcherInterface
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Interface RequestMatcherInterface
 *
 * @category RequestMatcherInterface
 * @package  Pock\Matchers
 */
interface RequestMatcherInterface
{
    /**
     * Returns true if request is matched by this matcher.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    public function matches(RequestInterface $request): bool;
}
