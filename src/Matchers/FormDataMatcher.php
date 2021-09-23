<?php

/**
 * PHP version 7.3
 *
 * @category FormDataMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveLtrArrayComparator;
use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;

/**
 * Class FormDataMatcher
 *
 * @category FormDataMatcher
 * @package  Pock\Matchers
 */
class FormDataMatcher extends QueryMatcher
{
    use SeekableStreamDataExtractor;

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $query = static::parseQuery(static::getStreamData($request->getBody()));

        if (empty($query)) {
            return false;
        }

        return ComparatorLocator::get(RecursiveLtrArrayComparator::class)->compare($this->query, $query);
    }
}
