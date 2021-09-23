<?php

/**
 * PHP version 7.3
 *
 * @category RegExpQueryMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class RegExpQueryMatcher
 *
 * @category RegExpQueryMatcher
 * @package  Pock\Matchers
 */
class RegExpQueryMatcher extends AbstractRegExpMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return $this->matchRegExp($request->getUri()->getQuery());
    }
}
