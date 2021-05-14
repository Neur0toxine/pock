<?php

/**
 * PHP 7.3
 *
 * @category JsonSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */

namespace Pock\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Pock\Factory\JsonSerializerFactory;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class JsonSerializerFactoryTest
 *
 * @category JsonSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */
class JsonSerializerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = JsonSerializerFactory::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::JSON, $serializer->serialize(new SimpleObject()));
    }
}
