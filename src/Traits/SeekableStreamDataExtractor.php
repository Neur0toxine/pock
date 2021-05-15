<?php

/**
 * PHP 7.1
 *
 * @category SeekableStreamDataExtractor
 * @package  Pock\Traits
 */

namespace Pock\Traits;

use Psr\Http\Message\StreamInterface;

/**
 * Trait SeekableStreamDataExtractor
 *
 * @category SeekableStreamDataExtractor
 * @package  Pock\Traits
 */
trait SeekableStreamDataExtractor
{
    /**
     * Returns contents contents without honoring a contents pointer.
     *
     * @param \Psr\Http\Message\StreamInterface $stream
     *
     * @return string
     */
    protected static function getStreamData(StreamInterface $stream): string
    {
        return $stream->isSeekable() ? $stream->__toString() : $stream->getContents();
    }
}
