<?php

/**
 * PHP 7.1
 *
 * @category ExactHeaderMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\ScalarFlatArrayComparator;
use Psr\Http\Message\RequestInterface;

/**
 * Class ExactHeaderMatcher
 *
 * @category ExactHeaderMatcher
 * @package  Pock\Matchers
 */
class ExactHeaderMatcher extends HeaderMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if (!$request->hasHeader($this->header)) {
            return false;
        }

        return ComparatorLocator::get(ScalarFlatArrayComparator::class)
            ->compare($request->getHeader($this->header), $this->value);
    }
}
