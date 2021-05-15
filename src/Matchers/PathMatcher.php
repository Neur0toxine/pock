<?php

/**
 * PHP 7.1
 *
 * @category PathMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class PathMatcher
 *
 * @category PathMatcher
 * @package  Pock\Matchers
 */
class PathMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $path;

    /**
     * PathMatcher constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        if (('' !== $path) && '/' === $path[0]) {
            $path = substr($path, 1);
        }

        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return $request->getUri()->getPath() === $this->path ||
            $request->getUri()->getPath() === '/' . $this->path;
    }
}
