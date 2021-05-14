<?php

/**
 * PHP 7.3
 *
 * @category AbstractSerializerFactory
 * @package  Pock\Factory
 */

namespace Pock\Factory;

use Pock\Creator\SerializerCreatorInterface;
use Pock\Serializer\SerializerInterface;

/**
 * Class AbstractSerializerFactory
 *
 * @category AbstractSerializerFactory
 * @package  Pock\Factory
 */
abstract class AbstractSerializerFactory implements SerializerCreatorInterface
{
    /**
     * Instantiate first available serializer.
     *
     * @return \Pock\Serializer\SerializerInterface|null
     */
    public static function create(): ?SerializerInterface
    {
        foreach (static::getCreators() as $creator) {
            if (!method_exists($creator, 'create')) {
                continue;
            }

            $serializer = call_user_func([$creator, 'create']); // @phpstan-ignore-line

            if ($serializer instanceof SerializerInterface) {
                return $serializer;
            }
        }

        return null;
    }

    /**
     * Returns list of available creators.
     *
     * @return string[]
     */
    abstract protected static function getCreators(): array;
}
