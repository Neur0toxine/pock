<?php

/**
 * PHP 7.1
 *
 * @category XmlSerializerAwareTrait
 * @package  Pock\Traits
 */

namespace Pock\Traits;

use Pock\Exception\XmlException;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\SerializerInterface;

/**
 * Trait XmlSerializerAwareTrait
 *
 * @category XmlSerializerAwareTrait
 * @package  Pock\Traits
 */
trait XmlSerializerAwareTrait
{
    /** @var SerializerInterface|null */
    protected static $xmlSerializer;

    /**
     * Returns XML string if serialization was successful. Returns null otherwise.
     * String input value will be treated as XML and will not be processed in any way.
     *
     * @param mixed $data
     *
     * @return string|null
     * @throws \Pock\Exception\XmlException
     */
    protected static function serializeXml($data): ?string
    {
        if (is_string($data)) {
            return $data;
        }

        if (is_array($data) || is_object($data)) {
            return static::xmlSerializer()->serialize($data);
        }

        return null;
    }

    /**
     * @return \Pock\Serializer\SerializerInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \Pock\Exception\XmlException
     */
    protected static function xmlSerializer(): SerializerInterface
    {
        if (null !== static::$xmlSerializer) {
            return static::$xmlSerializer;
        }

        $serializer = XmlSerializerFactory::create();

        if (null === $serializer) {
            throw new XmlException('No XML serializer available');
        }

        static::$xmlSerializer = $serializer;

        return $serializer;
    }
}
