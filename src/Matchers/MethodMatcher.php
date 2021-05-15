<?php

/**
 * PHP 7.1
 *
 * @category MethodMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Enum\RequestMethod;
use Psr\Http\Message\RequestInterface;

/**
 * Class MethodMatcher
 *
 * @category MethodMatcher
 * @package  Pock\Matchers
 */
class MethodMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $method;

    /**
     * MethodMatcher constructor.
     *
     * @param string $method
     */
    public function __construct(string $method = RequestMethod::GET)
    {
        $this->method = strtoupper($method);
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return strtoupper($request->getMethod()) === $this->method;
    }
}
