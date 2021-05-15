<?php

/**
 * PHP 7.1
 *
 * @category JsonBodyMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Exception\JsonException;
use Pock\Traits\JsonDecoderTrait;

/**
 * Class JsonBodyMatcher
 *
 * @category JsonBodyMatcher
 * @package  Pock\Matchers
 */
class JsonBodyMatcher extends AbstractSerializedBodyMatcher
{
    use JsonDecoderTrait;

    /**
     * @phpstan-ignore-next-line
     * @inheritDoc
     */
    protected function deserialize(string $data): ?array
    {
        try {
            return self::jsonDecode($data, true);
        } catch (JsonException $exception) {
            return null;
        }
    }
}
