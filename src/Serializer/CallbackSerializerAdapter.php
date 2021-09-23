<?php

/**
 * PHP 7.1
 *
 * @category CallbackSerializerAdapter
 * @package  Pock\Serializer
 */

namespace Pock\Serializer;

use RuntimeException;

/**
 * Class CallbackSerializerAdapter
 *
 * @category CallbackSerializerAdapter
 * @package  Pock\Serializer
 */
class CallbackSerializerAdapter implements SerializerInterface
{
    /** @var callable */
    private $callback;

    /**
     * CallbackSerializerAdapter constructor.
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
