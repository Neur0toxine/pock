<?php

/**
 * PHP 7.3
 *
 * @category PockResponseBuilder
 * @package  Pock
 */

namespace Pock;

use InvalidArgumentException;
use JsonSerializable;
use Nyholm\Psr7\Factory\Psr17Factory;
use Pock\Exception\JsonException;
use Pock\Factory\JsonSerializerFactory;
use Pock\Factory\XmlSerializerFactory;
use Pock\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PockResponseBuilder
 *
 * @category PockResponseBuilder
 * @package  Pock
 */
class PockResponseBuilder
{
    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    /** @var Psr17Factory */
    private $factory;

    /** @var SerializerInterface|null */
    private static $jsonSerializer;

    /** @var SerializerInterface|null */
    private static $xmlSerializer;

    /**
     * PockResponseBuilder constructor.
     *
     * @param int $statusCode
     */
    public function __construct(int $statusCode = 200)
    {
        $this->factory = new Psr17Factory();
        $this->response = $this->factory->createResponse($statusCode);
    }

    /**
     * Reply with specified status code.
     *
     * @param int $statusCode
     *
     * @return \Pock\PockResponseBuilder
     */
    public function withStatusCode(int $statusCode = 200): PockResponseBuilder
    {
        $this->response = $this->response->withStatus($statusCode);

        return $this;
    }

    /**
     * Reply with specified body. It can be:
     *  - PSR-7 StreamInterface - it will be used without any changes.
     *  - string - it will be used as stream contents.
     *  - resource - it's data will be used as stream contents.
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $stream
     *
     * @return $this
     */
    public function withBody($stream): PockResponseBuilder
    {
        if (is_string($stream)) {
            $stream = $this->factory->createStream($stream);
        }

        if (is_resource($stream)) {
            $stream = $this->factory->createStreamFromResource($stream);
        }

        $this->response = $this->response->withBody($stream);

        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     * @throws \Pock\Exception\JsonException
     */
    public function withJson($data): PockResponseBuilder
    {
        if (is_string($data) || is_numeric($data)) {
            return $this->withBody((string) $data);
        }

        if (is_array($data)) {
            return $this->withBody(static::jsonEncode($data));
        }

        if (is_object($data)) {
            if ($data instanceof JsonSerializable) {
                return $this->withBody(static::jsonEncode($data));
            }

            return $this->withBody(static::jsonSerializer()->serialize($data));
        }

        throw new InvalidArgumentException('Cannot serialize data with type ' . gettype($data));
    }

    /**
     * @param mixed $data
     *
     * @return $this
     * @throws \Pock\Exception\JsonException
     */
    public function withXml($data): PockResponseBuilder
    {
        if (is_string($data)) {
            return $this->withBody($data);
        }

        if (is_array($data) || is_object($data)) {
            return $this->withBody(static::xmlSerializer()->serialize($data));
        }

        throw new InvalidArgumentException('Cannot serialize data with type ' . gettype($data));
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Encode JSON, throw an exception on error.
     *
     * @param mixed $data
     *
     * @return string
     * @throws \Pock\Exception\JsonException
     */
    private static function jsonEncode($data): string
    {
        $data = json_encode($data);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return (string) $data;
    }

    /**
     * @return \Pock\Serializer\SerializerInterface
     * @throws \Pock\Exception\JsonException
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function jsonSerializer(): SerializerInterface
    {
        if (null !== static::$jsonSerializer) {
            return static::$jsonSerializer;
        }

        $serializer = JsonSerializerFactory::create();

        if (null === $serializer) {
            throw new JsonException('No JSON serializer available');
        }

        static::$jsonSerializer = $serializer;

        return $serializer;
    }

    /**
     * @return \Pock\Serializer\SerializerInterface
     * @throws \Pock\Exception\JsonException
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private static function xmlSerializer(): SerializerInterface
    {
        if (null !== static::$xmlSerializer) {
            return static::$xmlSerializer;
        }

        $serializer = XmlSerializerFactory::create();

        if (null === $serializer) {
            throw new JsonException('No XML serializer available');
        }

        static::$xmlSerializer = $serializer;

        return $serializer;
    }
}
