<?php

/**
 * PHP version 7.3
 *
 * @category RegExpPathMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class RegExpPathMatcher
 *
 * @category RegExpPathMatcher
 * @package  Pock\Matchers
 */
class RegExpPathMatcher extends AbstractRegExpMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return $this->matchRegExp($request->getUri()->getPath());
    }
}
