<?php

/**
 * PHP 7.1
 *
 * @category JsonDecoderTrait
 * @package  Pock\Traits
 */

namespace Pock\Traits;

use Pock\Exception\JsonException;

/**
 * Trait JsonDecoderTrait
 *
 * @category JsonDecoderTrait
 * @package  Pock\Traits
 */
trait JsonDecoderTrait
{
    /**
     * json_decode which throws exception on error.
     *
     * @param string $json
     * @param bool   $associative
     * @param int    $depth
     *
     * @param int    $flags
     *
     * @return mixed
     * @throws \Pock\Exception\JsonException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function jsonDecode(
        string $json,
        bool $associative = false,
        int $depth = 512,
        int $flags = 0
    ) {
        $result = json_decode($json, $associative, $depth, $flags);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return $result;
    }
}
