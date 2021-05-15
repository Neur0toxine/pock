<?php

/**
 * PHP 7.1
 *
 * @category HeaderLineRegexpMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class HeaderLineRegexpMatcher
 *
 * @category HeaderLineRegexpMatcher
 * @package  Pock\Matchers
 */
class HeaderLineRegexpMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $header;

    /** @var string */
    private $pattern;

    /**
     * HeaderLineRegexpMatcher constructor.
     *
     * @param string $header
     * @param string $pattern
     */
    public function __construct(string $header, string $pattern)
    {
        $this->header = $header;
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if (!$request->hasHeader($this->header)) {
            return false;
        }

        return 1 === preg_match($this->pattern, $request->getHeaderLine($this->header));
    }
}
