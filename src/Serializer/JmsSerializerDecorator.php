<?php

/**
 * PHP 7.2
 *
 * @category JmsSerializerDecorator
 * @package  Pock\Serializer
 */

namespace Pock\Serializer;

/**
 * Class JmsSerializerDecorator
 *
 * @category JmsSerializerDecorator
 * @package  Pock\Serializer
 */
class JmsSerializerDecorator implements SerializerInterface
{
    /** @var object */
    private $serializer;

    /** @var string */
    private $format;

    /**
     * JmsSerializerDecorator constructor.
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
