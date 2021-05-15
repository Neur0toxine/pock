<?php

/**
 * PHP 7.1
 *
 * @category SymfonyJsonSerializerCreatorTest
 * @package  Pock\Tests\Creator
 */

namespace Pock\Tests\Creator;

use PHPUnit\Framework\TestCase;
use Pock\Creator\SymfonyJsonSerializerCreator;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class SymfonyJsonSerializerCreatorTest
 *
 * @category SymfonyJsonSerializerCreatorTest
 * @package  Pock\Tests\Creator
 */
class SymfonyJsonSerializerCreatorTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = SymfonyJsonSerializerCreator::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::JSON, $serializer->serialize(new SimpleObject()));
    }
}
