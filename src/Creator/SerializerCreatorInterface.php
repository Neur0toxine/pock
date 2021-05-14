<?php

/**
 * PHP 7.2
 *
 * @category SerializerCreatorInterface
 * @package  Pock\Creator
 */

namespace Pock\Creator;

use Pock\Serializer\SerializerInterface;

/**
 * Interface SerializerCreatorInterface
 *
 * @category SerializerCreatorInterface
 * @package  Pock\Creator
 */
interface SerializerCreatorInterface
{
    /**
     * Instantiates serializer and returns it. Returns null if serializer cannot be located.
     *
     * @return \Pock\Serializer\SerializerInterface|null
     */
    public static function create(): ?SerializerInterface;
}
