<?php

/**
 * PHP 7.1
 *
 * @category ExactHeadersMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class ExactHeadersMatcher
 *
 * @category ExactHeadersMatcher
 * @package  Pock\Matchers
 */
class ExactHeadersMatcher extends HeadersMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $requestHeaders = [];

        foreach ($request->getHeaders() as $header => $value) {
            $requestHeaders[strtolower($header)] = $value;
        }

        if (isset($requestHeaders['host']) && !$this->expectHeader('host')) {
            unset($requestHeaders['host']);
        }

        if (!static::headerValuesEqual(array_keys($this->headers), array_keys($requestHeaders))) {
            return false;
        }

        foreach ($requestHeaders as $header => $value) {
            $expectedValue = is_string($this->headers[$header]) ? [$this->headers[$header]] : $this->headers[$header];

            if (!static::headerValuesEqual($value, $expectedValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if provided header is expected by the mock.
     *
     * @param string $name
     *
     * @return bool
     */
    private function expectHeader(string $name): bool
    {
        foreach (array_keys($this->headers) as $header) {
            if (strtolower($header) === strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $first
     * @param string[] $second
     *
     * @return bool
     */
    private static function headerValuesEqual(array $first, array $second): bool
    {
        return count($first) === count($second) &&
            array_diff($first, $second) === array_diff($second, $first);
    }
}
