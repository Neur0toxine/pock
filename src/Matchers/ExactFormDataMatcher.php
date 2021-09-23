<?php

/**
 * PHP version 7.3
 *
 * @category ExactFormDataMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveArrayComparator;
use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;

/**
 * Class ExactFormDataMatcher
 *
 * @category ExactFormDataMatcher
 * @package  Pock\Matchers
 */
class ExactFormDataMatcher extends QueryMatcher
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

        return ComparatorLocator::get(RecursiveArrayComparator::class)->compare($this->query, $query);
    }
}
