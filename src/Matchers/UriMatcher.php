<?php

/**
 * PHP 7.2
 *
 * @category UriMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class UriMatcher
 *
 * @category UriMatcher
 * @package  Pock\Matchers
 */
class UriMatcher implements RequestMatcherInterface
{
    /** @var \Psr\Http\Message\UriInterface|string */
    private $uri;

    /**
     * UriMatcher constructor.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return ((string) $request->getUri()) === ((string) $this->uri);
    }
}
