<?php

/**
 * PHP 7.1
 *
 * @category EmptyXmlSerializerDecorator
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use Pock\Serializer\SerializerInterface;

/**
 * Class EmptyXmlSerializerDecorator
 *
 * @category EmptyXmlSerializerDecorator
 * @package  Pock\TestUtils
 */
class EmptyXmlSerializerDecorator implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize($data): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>';
    }
}
