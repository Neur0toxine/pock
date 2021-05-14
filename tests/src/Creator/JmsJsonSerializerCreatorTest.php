<?php

/**
 * PHP 7.3
 *
 * @category JmsJsonSerializerCreatorTest
 * @package  src\Component\Creator
 */

namespace Pock\Tests\Component\Creator;

use PHPUnit\Framework\TestCase;
use Pock\Creator\JmsJsonSerializerCreator;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class JmsJsonSerializerCreatorTest
 *
 * @category JmsJsonSerializerCreatorTest
 * @package  src\Component\Creator
 */
class JmsJsonSerializerCreatorTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = JmsJsonSerializerCreator::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::JSON, $serializer->serialize(new SimpleObject()));
    }
}
