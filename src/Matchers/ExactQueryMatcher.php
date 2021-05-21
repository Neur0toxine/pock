<?php

/**
 * PHP 7.1
 *
 * @category ExactQueryMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveArrayComparator;
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

        return ComparatorLocator::get(RecursiveArrayComparator::class)->compare($this->query, $query);
    }
}
