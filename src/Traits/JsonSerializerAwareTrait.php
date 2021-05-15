<?php

/**
 * PHP 7.1
 *
 * @category JsonSerializerAwareTrait
 * @package  Pock\Traits
 */

namespace Pock\Traits;

use JsonSerializable;
use Pock\Exception\JsonException;
use Pock\Factory\JsonSerializerFactory;
use Pock\Serializer\SerializerInterface;

/**
 * Trait JsonSerializerAwareTrait
 *
 * @category JsonSerializerAwareTrait
 * @package  Pock\Traits
 */
trait JsonSerializerAwareTrait
{
    use JsonEncoderTrait;

    /** @var SerializerInterface|null */
    protected static $jsonSerializer;

    /**
     * Returns JSON string if serialization was successful. Returns null otherwise.
     * String input value will be treated as JSON and will not be processed in any way.
     *
     * @param mixed $data
     *
     * @return string|null
     * @throws \Pock\Exception\JsonException
     */
    protected static function serializeJson($data): ?string
    {
        if (is_string($data) || is_numeric($data)) {
            return (string) $data;
        }

        if (is_array($data)) {
            return static::jsonEncode($data);
        }

        if (is_object($data)) {
            if ($data instanceof JsonSerializable) {
                return static::jsonEncode($data);
            }

            return static::jsonSerializer()->serialize($data);
        }

        return null;
    }

    /**
     * @return \Pock\Serializer\SerializerInterface
     * @throws \Pock\Exception\JsonException
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function jsonSerializer(): SerializerInterface
    {
        if (null !== static::$jsonSerializer) {
            return static::$jsonSerializer;
        }

        $serializer = JsonSerializerFactory::create();

        if (null === $serializer) {
            throw new JsonException('No JSON serializer available');
        }

        static::$jsonSerializer = $serializer;

        return $serializer;
    }
}
