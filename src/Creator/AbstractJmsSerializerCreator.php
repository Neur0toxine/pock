<?php

/**
 * PHP 7.2
 *
 * @category AbstractJmsSerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

use Throwable;
use Pock\Serializer\JmsSerializerDecorator;
use Pock\Serializer\SerializerInterface;

/**
 * Class AbstractJmsSerializerCreator
 *
 * @category AbstractJmsSerializerCreator
 * @package  Pock\Creator
 */
abstract class AbstractJmsSerializerCreator implements SerializerCreatorInterface
{
    private const BUILDER_CLASS = '\JMS\Serializer\SerializerBuilder';

    /**
     * @inheritDoc
     */
    public static function create(): ?SerializerInterface
    {
        if (
            class_exists(self::BUILDER_CLASS) &&
            method_exists(self::BUILDER_CLASS, 'create')
        ) {
            try {
                $builder = call_user_func([self::BUILDER_CLASS, 'create']);

                if (null !== $builder && method_exists($builder, 'build')) {
                    return new JmsSerializerDecorator($builder->build(), static::getFormat()); // @phpstan-ignore-line
                }
            } catch (Throwable $throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Returns format for the serializer;
     *
     * @return string
     */
    abstract protected static function getFormat(): string;
}
