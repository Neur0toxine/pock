<?php

/**
 * PHP 7.2
 *
 * @category SerializerInterface
 * @package  Pock\Serializer
 */

namespace Pock\Serializer;

/**
 * Interface SerializerInterface
 *
 * @category SerializerInterface
 * @package  Pock\Serializer
 */
interface SerializerInterface
{
    /**
     * Serialize item
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data): string;
}
