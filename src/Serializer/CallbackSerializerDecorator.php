<?php

/**
 * PHP 7.3
 *
 * @category CallbackSerializerDecorator
 * @package  Pock\Serializer
 */

namespace Pock\Serializer;

use RuntimeException;

/**
 * Class CallbackSerializerDecorator
 *
 * @category CallbackSerializerDecorator
 * @package  Pock\Serializer
 */
class CallbackSerializerDecorator implements SerializerInterface
{
    /** @var callable */
    private $callback;

    /**
     * CallbackSerializerDecorator constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function serialize($data): string
    {
        $result = call_user_func($this->callback, $data);

        if (is_string($result)) {
            return $result;
        }

        throw new RuntimeException(sprintf(
            'Invalid data from serialization callback: expected string, %s given',
            gettype($result)
        ));
    }
}
