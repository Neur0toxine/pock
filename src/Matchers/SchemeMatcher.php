<?php

/**
 * PHP 7.2
 *
 * @category SchemeMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Enum\RequestScheme;
use Psr\Http\Message\RequestInterface;

/**
 * Class SchemeMatcher
 *
 * @category SchemeMatcher
 * @package  Pock\Matchers
 */
class SchemeMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $scheme;

    /**
     * SchemeMatcher constructor.
     *
     * @param string $scheme
     */
    public function __construct(string $scheme = RequestScheme::HTTP)
    {
        $this->scheme = strtolower($scheme);
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return strtolower($request->getUri()->getScheme()) === $this->scheme;
    }
}
