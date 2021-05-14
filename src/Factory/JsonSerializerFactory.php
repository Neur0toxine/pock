<?php

/**
 * PHP 7.2
 *
 * @category JsonSerializerFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\Creator\JmsJsonSerializerCreator;

/**
 * Class JsonSerializerFactory
 *
 * @category JsonSerializerFactory
 * @package  Pock\Factory
 */
class JsonSerializerFactory extends AbstractSerializerFactory
{
    /**
     * @inheritDoc
     */
    protected static function getCreators(): array
    {
        return [
            JmsJsonSerializerCreator::class,
        ];
    }
}
