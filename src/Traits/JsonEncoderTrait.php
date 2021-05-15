<?php

/**
 * PHP 7.1
 *
 * @category JsonEncoderTrait
 * @package  Pock\Traits
 */

namespace Pock\Traits;

use Pock\Exception\JsonException;

/**
 * Trait JsonEncoderTrait
 *
 * @category JsonEncoderTrait
 * @package  Pock\Traits
 */
trait JsonEncoderTrait
{
    /**
     * json_encode which throws exception on error.
     *
     * @param mixed $data
     * @param int   $flags
     * @param int   $depth
     *
     * @return string
     * @throws \Pock\Exception\JsonException
     */
    public static function jsonEncode($data, int $flags = 0, int $depth = 512): string
    {
        $result = json_encode($data, $flags, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return (string) $result;
    }
}
