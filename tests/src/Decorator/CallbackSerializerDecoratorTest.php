<?php

/**
 * PHP 7.3
 *
 * @category CallbackSerializerDecoratorTest
 * @package  Pock\Tests\Decorator
 */

namespace Pock\Tests\Decorator;

use PHPUnit\Framework\TestCase;
use Pock\Serializer\CallbackSerializerDecorator;

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
        self::assertEquals('{}', (new CallbackSerializerDecorator(function ($data) {
            return $data;
        }))->serialize('{}'));
    }
}
