<?php

/**
 * PHP 7.2
 *
 * @category AbstractRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Pock\Enum\RequestMethod;
use Pock\Enum\RequestScheme;
use Psr\Http\Message\RequestInterface;

/**
 * Class AbstractRequestMatcherTest
 *
 * @category AbstractRequestMatcherTest
 * @package  Pock\Tests\Matchers
 */
abstract class AbstractRequestMatcherTest extends TestCase
{
    protected const TEST_METHOD = RequestMethod::GET;
    protected const TEST_SCHEME = RequestScheme::HTTPS;
    protected const TEST_HOST = 'example.com';
    protected const TEST_URI = self::TEST_SCHEME . '://' . self::TEST_HOST . '/';

    /** @var \Nyholm\Psr7\Factory\Psr17Factory */
    private static $psr17Factory;

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    protected static function getTestRequest(): RequestInterface
    {
        return static::getPsr17Factory()->createRequest(static::TEST_METHOD, static::TEST_URI);
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
}
