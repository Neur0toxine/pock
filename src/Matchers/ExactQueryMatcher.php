<?php

/**
 * PHP 7.1
 *
 * @category ExactQueryMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class ExactQueryMatcher
 *
 * @category ExactQueryMatcher
 * @package  Pock\Matchers
 */
class ExactQueryMatcher extends QueryMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $query = static::parseQuery($request->getUri()->getQuery());

        if (empty($query)) {
            return false;
        }

        return self::recursiveCompareArrays($this->query, $query);
    }
}
