<?php

/**
 * PHP 7.1
 *
 * @category SimpleObjectJsonSerializable
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use JsonSerializable;

/**
 * Class SimpleObjectJsonSerializable
 *
 * @category SimpleObjectJsonSerializable
 * @package  Pock\TestUtils
 */
class SimpleObjectJsonSerializable extends SimpleObject implements JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return ['field' => $this->field];
    }
}
