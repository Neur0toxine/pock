<?php

/**
 * PHP 7.3
 *
 * @category AbstractSerializerFactoryTest
 * @package  Pock\Tests\Factory
 */

namespace Pock\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Pock\Factory\JsonSerializerFactory;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\CallbackSerializerDecorator;
use Pock\Serializer\SerializerInterface;

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
        $jsonSerializer = new CallbackSerializerDecorator(function ($data) {
            return 'jsonSerializer';
        });
        $xmlSerializer = new CallbackSerializerDecorator(function ($data) {
            return 'xmlSerializer';
        });

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
