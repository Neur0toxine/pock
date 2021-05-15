<?php

/**
 * PHP 7.2
 *
 * @category JsonSerializerFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\Creator\JmsJsonSerializerCreator;
use Pock\Creator\SymfonyJsonSerializerCreator;
use Pock\Serializer\SerializerInterface;

/**
 * Class JsonSerializerFactory
 *
 * @category JsonSerializerFactory
 * @package  Pock\Factory
 */
class JsonSerializerFactory extends AbstractSerializerFactory
{
    /** @var \Pock\Serializer\SerializerInterface|null */
    private static $mainSerializer;

    /**
     * @inheritDoc
     */
    protected static function getCreators(): array
    {
        return [
            JmsJsonSerializerCreator::class,
            SymfonyJsonSerializerCreator::class
        ];
    }

    /**
     * @param \Pock\Serializer\SerializerInterface|null $serializer
     */
    public static function setSerializer(?SerializerInterface $serializer): void
    {
        static::$mainSerializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    protected static function getMainSerializer(): ?SerializerInterface
    {
        return static::$mainSerializer;
    }
}
