<?php

/**
 * PHP 7.2
 *
 * @category XmlSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */

namespace Pock\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class XmlSerializerFactoryTest
 *
 * @category XmlSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */
class XmlSerializerFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = XmlSerializerFactory::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::JMS_XML, $serializer->serialize(new SimpleObject()));
    }
}
