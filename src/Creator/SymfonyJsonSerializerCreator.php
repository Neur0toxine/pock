<?php

/**
 * PHP 7.1
 *
 * @category SymfonyJsonSerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

/**
 * Class SymfonyJsonSerializerCreator
 *
 * @category SymfonyJsonSerializerCreator
 * @package  Pock\Creator
 */
class SymfonyJsonSerializerCreator extends AbstractSymfonySerializerCreator
{
    /**
     * @inheritDoc
     */
    protected static function getFormat(): string
    {
        return 'json';
    }

    /**
     * @inheritDoc
     */
    protected static function getEncoderClass(): string
    {
        return '\Symfony\Component\Serializer\Encoder\JsonEncoder';
    }
}
