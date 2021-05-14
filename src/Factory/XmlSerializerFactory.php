<?php

/**
 * PHP 7.3
 *
 * @category XmlSerializerFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\Creator\JmsXmlSerializerCreator;

/**
 * Class XmlSerializerFactory
 *
 * @category XmlSerializerFactory
 * @package  Pock\Factory
 */
class XmlSerializerFactory extends AbstractSerializerFactory
{
    /**
     * @inheritDoc
     */
    protected static function getCreators(): array
    {
        return [
            JmsXmlSerializerCreator::class
        ];
    }
}
