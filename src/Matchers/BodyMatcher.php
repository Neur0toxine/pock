<?php

/**
 * PHP 7.1
 *
 * @category BodyMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class BodyMatcher
 *
 * @category BodyMatcher
 * @package  Pock\Matchers
 */
class BodyMatcher implements RequestMatcherInterface
{
    use SeekableStreamDataExtractor;

    /** @var string */
    protected $contents = '';

    /**
     * BodyMatcher constructor.
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $contents
     */
    public function __construct($contents)
    {
        if (is_string($contents)) {
            $this->contents = $contents;
        }

        if ($contents instanceof StreamInterface) {
            $this->contents = static::getStreamData($contents);
        }

        if (is_resource($contents)) {
            $this->contents = static::readAllResource($contents);
        }
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if (0 === $request->getBody()->getSize()) {
            return '' === $this->contents;
        }

        return static::getStreamData($request->getBody()) === $this->contents;
    }

    /**
     * Reads entire resource data.
     *
     * @param resource $resource
     *
     * @return string
     */
    protected static function readAllResource($resource): string
    {
        fseek($resource, 0);
        return (string) stream_get_contents($resource);
    }
}
