<?php

/**
 * PHP 7.3
 *
 * @category MultipleMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Countable;
use Psr\Http\Message\RequestInterface;

/**
 * Class MultipleMatcher
 *
 * @category MultipleMatcher
 * @package  Pock\Matchers
 */
class MultipleMatcher implements RequestMatcherInterface, Countable
{
    /** @var \Pock\Matchers\RequestMatcherInterface[] */
    public $matchers;

    /**
     * MultipleMatcher constructor.
     *
     * @param \Pock\Matchers\RequestMatcherInterface[] $matchers
     */
    public function __construct(array $matchers = [])
    {
        $this->matchers = $matchers;
    }

    /**
     * @param \Pock\Matchers\RequestMatcherInterface $matcher
     *
     * @return $this
     */
    public function addMatcher(RequestMatcherInterface $matcher): self
    {
        $this->matchers[] = $matcher;
        return $this;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->matchers);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return bool
     */
    public function matches(RequestInterface $request): bool
    {
        foreach ($this->matchers as $matcher) {
            if (!$matcher->matches($request)) {
                return false;
            }
        }

        return true;
    }
}
