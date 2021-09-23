<?php

/**
 * PHP version 7.1
 *
 * @category MultipartFormDataMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use InvalidArgumentException;
use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;
use Riverline\MultiPartParser\StreamedPart;
use Riverline\MultiPartParser\Converters\PSR7;
use RuntimeException;

/**
 * Class MultipartFormDataMatcher
 *
 * @category MultipartFormDataMatcher
 * @package  Pock\Matchers
 */
class MultipartFormDataMatcher implements RequestMatcherInterface
{
    use SeekableStreamDataExtractor;

    /** @var callable */
    private $callback;

    /**
     * MultipartFormDataMatcher constructor.
     *
     * @param callable $callback Accepts Riverline\MultiPartParser\StreamedPart as an argument, returns true if matched.
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function matches(RequestInterface $request): bool
    {
        try {
            $part = PSR7::convert($request);
            $request->getBody()->rewind();
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        return call_user_func($this->callback, $part);
    }
}
