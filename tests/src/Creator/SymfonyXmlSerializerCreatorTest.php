<?php

/**
 * PHP 7.1
 *
 * @category SymfonyXmlSerializerCreatorTest
 * @package  Pock\Tests\Creator
 */

namespace Pock\Tests\Creator;

use PHPUnit\Framework\TestCase;
use Pock\Creator\SymfonyXmlSerializerCreator;
use Pock\Serializer\SerializerInterface;
use Pock\TestUtils\SimpleObject;

/**
 * Class SymfonyXmlSerializerCreatorTest
 *
 * @category SymfonyXmlSerializerCreatorTest
 * @package  Pock\Tests\Creator
 */
class SymfonyXmlSerializerCreatorTest extends TestCase
{
    public function testCreate(): void
    {
        $serializer = SymfonyXmlSerializerCreator::create();

        self::assertInstanceOf(SerializerInterface::class, $serializer);
        self::assertEquals(SimpleObject::SYMFONY_XML, $serializer->serialize(new SimpleObject()));
    }
}
