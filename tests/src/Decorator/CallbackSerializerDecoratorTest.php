<?php

/**
 * PHP 7.1
 *
 * @category CallbackSerializerDecoratorTest
 * @package  Pock\Tests\Decorator
 */

namespace Pock\Tests\Decorator;

use PHPUnit\Framework\TestCase;
use Pock\Serializer\CallbackSerializerAdapter;

/**
 * Class CallbackSerializerDecoratorTest
 *
 * @category CallbackSerializerDecoratorTest
 * @package  Pock\Tests\Decorator
 */
class CallbackSerializerDecoratorTest extends TestCase
{
    public function testSerialize(): void
    {
        self::assertEquals('{}', (new CallbackSerializerAdapter(function ($data) {
            return $data;
        }))->serialize('{}'));
    }
}
