<?php

/**
 * PHP 7.3
 *
 * @category EmptyJsonSerializerDecorator
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use Pock\Serializer\SerializerInterface;

/**
 * Class EmptyJsonSerializerDecorator
 *
 * @category EmptyJsonSerializerDecorator
 * @package  Pock\TestUtils
 */
class EmptyJsonSerializerDecorator implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize($data): string
    {
        return '{}';
    }
}
