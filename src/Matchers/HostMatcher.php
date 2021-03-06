<?php

/**
 * PHP 7.2
 *
 * @category HostMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class HostMatcher
 *
 * @category HostMatcher
 * @package  Pock\Matchers
 */
class HostMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $host;

    /**
     * HostMatcher constructor.
     *
     * @param string $host
     */
    public function __construct(string $host)
    {
        $this->host = strtolower($host);
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return strtolower($request->getUri()->getHost()) === $this->host;
    }
}
