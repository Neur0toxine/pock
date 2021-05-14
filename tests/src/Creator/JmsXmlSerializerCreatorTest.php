<?php

/**
 * PHP 7.3
 *
 * @category JmsXmlSerializerCreator
 * @package  src\Component\Creator
 */

namespace Pock\Tests\Component\Creator;

use PHPUnit\Framework\TestCase;
use Pock\Creator\JmsXmlSerializerCreator;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class JmsXmlSerializerCreator
 *
 * @category JmsXmlSerializerCreator
 * @package  src\Component\Creator
 */
class JmsXmlSerializerCreatorTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = JmsXmlSerializerCreator::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::XML, $serializer->serialize(new SimpleObject()));
    }
}
