<?php

/**
 * PHP 7.3
 *
 * @category JmsXmlSerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

/**
 * Class JmsXmlSerializerCreator
 *
 * @category JmsXmlSerializerCreator
 * @package  Pock\Creator
 */
class JmsXmlSerializerCreator extends AbstractJmsSerializerCreator
{
    /**
     * @inheritDoc
     */
    protected static function getFormat(): string
    {
        return 'xml';
    }
}
