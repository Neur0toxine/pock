<?php

/**
 * PHP 7.2
 *
 * @category XmlSerializerFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\Creator\JmsXmlSerializerCreator;
use Pock\Creator\SymfonyXmlSerializerCreator;
use Pock\Serializer\SerializerInterface;

/**
 * Class XmlSerializerFactory
 *
 * @category XmlSerializerFactory
 * @package  Pock\Factory
 */
class XmlSerializerFactory extends AbstractSerializerFactory
{
    /** @var \Pock\Serializer\SerializerInterface|null */
    private static $mainSerializer;

    /**
     * @inheritDoc
     */
    protected static function getCreators(): array
    {
        return [
            JmsXmlSerializerCreator::class,
            SymfonyXmlSerializerCreator::class
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
