<?php

/**
 * PHP 7.1
 *
 * @category HeadersMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class HeadersMatcher
 *
 * @category HeadersMatcher
 * @package  Pock\Matchers
 */
class HeadersMatcher extends AbstractArrayPoweredComponent implements RequestMatcherInterface
{
    /** @var array<string, string|string[]> */
    protected $headers;

    /**
     * HeadersMatcher constructor.
     *
     * @param array<string, string|string[]> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        foreach (array_keys($this->headers) as $header) {
            if (!$request->hasHeader($header)) {
                return false;
            }
        }

        foreach ($this->headers as $name => $value) {
            if (is_string($value)) {
                $value = [$value];
            }

            if (!static::isNeedlePresentInHaystack($value, $request->getHeader($name))) {
                return false;
            }
        }

        return true;
    }
}
