<?php

/**
 * PHP 7.3
 *
 * @category JmsJsonSerializerCreator
 * @package  Pock\Creator
 */

namespace Pock\Creator;

/**
 * Class JmsJsonSerializerCreator
 *
 * @category JmsJsonSerializerCreator
 * @package  Pock\Creator
 */
class JmsJsonSerializerCreator extends AbstractJmsSerializerCreator
{
    /**
     * @inheritDoc
     */
    protected static function getFormat(): string
    {
        return 'json';
    }
}
