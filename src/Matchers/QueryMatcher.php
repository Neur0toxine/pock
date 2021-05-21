<?php

/**
 * PHP 7.1
 *
 * @category QueryMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveLtrArrayComparator;
use Psr\Http\Message\RequestInterface;

/**
 * Class QueryMatcher
 *
 * @category QueryMatcher
 * @package  Pock\Matchers
 */
class QueryMatcher implements RequestMatcherInterface
{
    /** @var array<string, mixed> */
    protected $query;

    /**
     * QueryMatcher constructor.
     *
     * @param array<string, mixed> $query
     */
    public function __construct(array $query)
    {
        $this->query = $query;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $query = static::parseQuery($request->getUri()->getQuery());

        if (empty($query)) {
            return false;
        }

        return ComparatorLocator::get(RecursiveLtrArrayComparator::class)->compare($this->query, $query);
    }

    /**
     * Parses query, returns result.
     *
     * @param string $queryString
     *
     * @return array<string, mixed>
     */
    protected static function parseQuery(string $queryString): array
    {
        $query = [];

        if ('' !== $queryString) {
            parse_str($queryString, $query);
        }

        return $query;
    }
}
