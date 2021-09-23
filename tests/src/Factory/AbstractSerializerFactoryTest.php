<?php

/**
 * PHP 7.1
 *
 * @category AbstractSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */

namespace Pock\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Pock\Factory\JsonSerializerFactory;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\CallbackSerializerAdapter;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\EmptyJsonSerializerDecorator;
use Pock\TestUtils\EmptyXmlSerializerDecorator;

/**
 * Class AbstractSerializerFactoryTest
 *
 * @category AbstractSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */
class AbstractSerializerFactoryTest extends TestCase
{
    public function testSetSerializer(): void
    {
        $jsonSerializer = new EmptyJsonSerializerDecorator();
        $xmlSerializer = new EmptyXmlSerializerDecorator();

        self::assertNotEquals($jsonSerializer, $xmlSerializer);

        JsonSerializerFactory::setSerializer($jsonSerializer);
        XmlSerializerFactory::setSerializer($xmlSerializer);

        $resultJsonSerializer = JsonSerializerFactory::create();
        $resultXmlSerializer = XmlSerializerFactory::create();

        JsonSerializerFactory::setSerializer(null);
        XmlSerializerFactory::setSerializer(null);

        self::assertInstanceOf(SerializerInterface::class, $resultJsonSerializer);
        self::assertInstanceOf(SerializerInterface::class, $resultXmlSerializer);
        self::assertEquals($jsonSerializer, $resultJsonSerializer);
        self::assertEquals($xmlSerializer, $resultXmlSerializer);
        self::assertNotEquals($jsonSerializer, $xmlSerializer);
    }
}
