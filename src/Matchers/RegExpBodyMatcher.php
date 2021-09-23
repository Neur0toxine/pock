<?php

/**
 * PHP version 7.3
 *
 * @category RegExpBodyMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;

/**
 * Class RegExpBodyMatcher
 *
 * @category RegExpBodyMatcher
 * @package  Pock\Matchers
 */
class RegExpBodyMatcher extends AbstractRegExpMatcher
{
    use SeekableStreamDataExtractor;

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        return $this->matchRegExp(static::getStreamData($request->getBody()));
    }
}
