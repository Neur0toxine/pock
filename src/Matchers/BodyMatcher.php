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
        $this->contents = static::getEntryItemData($contents);
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

    /**
     * @param StreamInterface|resource|string $contents
     *
     * @return string
     */
    protected static function getEntryItemData($contents): string
    {
        if (is_string($contents)) {
            return $contents;
        }

        if ($contents instanceof StreamInterface) {
            return static::getStreamData($contents);
        }

        if (is_resource($contents)) {
            return static::readAllResource($contents);
        }

        return '';
    }
}
