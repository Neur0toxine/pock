<?php

/**
 * PHP 7.1
 *
 * @category HeaderLineMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class HeaderLineMatcher
 *
 * @category HeaderLineMatcher
 * @package  Pock\Matchers
 */
class HeaderLineMatcher implements RequestMatcherInterface
{
    /** @var string */
    private $header;

    /** @var string */
    private $value;

    /**
     * HeaderLineMatcher constructor.
     *
     * @param string $header
     * @param string $value
     */
    public function __construct(string $header, string $value)
    {
        $this->header = $header;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if (!$request->hasHeader($this->header)) {
            return false;
        }

        return $request->getHeaderLine($this->header) === $this->value;
    }
}
