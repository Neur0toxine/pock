<?php

/**
 * PHP 7.2
 *
 * @category PockTestCase
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Pock\Factory\JsonSerializerFactory;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\SerializerInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class PockTestCase
 *
 * @category PockTestCase
 * @package  Pock\TestUtils
 */
abstract class PockTestCase extends TestCase
{
    protected const TEST_METHOD = RequestMethod::GET;
    protected const TEST_SCHEME = RequestScheme::HTTPS;
    protected const TEST_HOST = 'example.com';
    protected const TEST_URI = self::TEST_SCHEME . '://' . self::TEST_HOST . '/';

    /** @var \Nyholm\Psr7\Factory\Psr17Factory */
    protected static $psr17Factory;

    /**
     * @param string|null $method
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected static function getTestRequest(?string $method = null): RequestInterface
    {
        return static::getPsr17Factory()->createRequest($method ?? static::TEST_METHOD, static::TEST_URI);
    }

    /**
     * @return \Nyholm\Psr7\Factory\Psr17Factory
     */
    protected static function getPsr17Factory(): Psr17Factory
    {
        if (null === static::$psr17Factory) {
            static::$psr17Factory = new Psr17Factory();
        }

        return static::$psr17Factory;
    }

    protected static function getJsonSerializer(): SerializerInterface
    {
        return JsonSerializerFactory::create();
    }

    protected static function getXmlSerializer(): SerializerInterface
    {
        return XmlSerializerFactory::create();
    }
}
