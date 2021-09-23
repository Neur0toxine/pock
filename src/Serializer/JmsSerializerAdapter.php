<?php

/**
 * PHP 7.2
 *
 * @category JmsSerializerAdapter
 * @package  Pock\Serializer
 */

namespace Pock\Serializer;

/**
 * Class JmsSerializerAdapter
 *
 * @category JmsSerializerAdapter
 * @package  Pock\Serializer
 */
class JmsSerializerAdapter implements SerializerInterface
{
    /** @var object */
    private $serializer;

    /** @var string */
    private $format;

    /**
     * JmsSerializerAdapter constructor.
     *
     * @param object $serializer
     */
    public function __construct($serializer, string $format = 'json')
    {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     */
    public function serialize($data): string
    {
        if (method_exists($this->serializer, 'serialize')) {
            return $this->serializer->serialize($data, $this->format);
        }

        return '';
    }
}
