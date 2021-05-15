<?php

/**
 * PHP 7.1
 *
 * @category SymfonyXmlSerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

/**
 * Class SymfonyXmlSerializerCreator
 *
 * @category SymfonyXmlSerializerCreator
 * @package  Pock\Creator
 */
class SymfonyXmlSerializerCreator extends AbstractSymfonySerializerCreator
{
    /**
     * @inheritDoc
     */
    protected static function getFormat(): string
    {
        return 'xml';
    }

    /**
     * @inheritDoc
     */
    protected static function getEncoderClass(): string
    {
        return '\Symfony\Component\Serializer\Encoder\XmlEncoder';
    }
}
