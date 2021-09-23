<?php

/**
 * PHP 7.1
 *
 * @category CallbackSerializerAdapterTest
 * @package  Pock\Tests\Serializer
 */

namespace Pock\Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Pock\Serializer\CallbackSerializerAdapter;

/**
 * Class CallbackSerializerAdapterTest
 *
 * @category CallbackSerializerAdapterTest
 * @package  Pock\Tests\Serializer
 */
class CallbackSerializerAdapterTest extends TestCase
{
    public function testSerialize(): void
    {
        self::assertEquals('{}', (new CallbackSerializerAdapter(function ($data) {
            return $data;
        }))->serialize('{}'));
    }
}
