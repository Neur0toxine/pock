<?php

/**
 * PHP version 7.3
 *
 * @category RegExpUriMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Psr\Http\Message\RequestInterface;

/**
 * Class RegExpUriMatcher
 *
 * @category RegExpUriMatcher
 * @package  Pock\Matchers
 */
class RegExpUriMatcher extends AbstractRegExpMatcher
{
    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return $this->matchRegExp((string) $request->getUri());
    }
}
