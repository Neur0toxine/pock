<?php

/**
 * PHP 7.1
 *
 * @category AbstractSymfonySerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

use Pock\Serializer\SerializerInterface;
use Pock\Serializer\SymfonySerializerDecorator;

/**
 * Class AbstractSymfonySerializerCreator
 *
 * @category AbstractSymfonySerializerCreator
 * @package  Pock\Creator
 */
abstract class AbstractSymfonySerializerCreator implements SerializerCreatorInterface
{
    private const OBJECT_NORMALIZER_CLASS = '\Symfony\Component\Serializer\Normalizer\ObjectNormalizer';
    private const SERIALIZER_CLASS = '\Symfony\Component\Serializer\Serializer';

    /**
     * @inheritDoc
     */
    public static function create(): ?SerializerInterface
    {
        if (self::isAvailable()) {
            $serializer = self::SERIALIZER_CLASS;
            $normalizer = self::OBJECT_NORMALIZER_CLASS;
            $encoder = static::getEncoderClass();

            return new SymfonySerializerDecorator(
                new $serializer([new $normalizer()], [new $encoder()]),
                static::getFormat()
            );
        }

        return null;
    }

    /**
     * Returns true if serializer can be instantiated.
     *
     * @return bool
     */
    private static function isAvailable(): bool
    {
        return class_exists(self::SERIALIZER_CLASS) &&
            class_exists(self::OBJECT_NORMALIZER_CLASS) &&
            class_exists(static::getEncoderClass());
    }

    /**
     * Returns format for the serializer;
     *
     * @return string
     */
    abstract protected static function getFormat(): string;

    /**
     * Returns format for the serializer;
     *
     * @return string
     */
    abstract protected static function getEncoderClass(): string;
}
