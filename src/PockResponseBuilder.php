<?php

/**
 * PHP 7.1
 *
 * @category PockResponseBuilder
 * @package  Pock
 */

namespace Pock;

use InvalidArgumentException;
use JsonSerializable;
use Nyholm\Psr7\Factory\Psr17Factory;
use Pock\Traits\JsonSerializerAwareTrait;
use Pock\Traits\XmlSerializerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class PockResponseBuilder
 *
 * @category PockResponseBuilder
 * @package  Pock
 */
class PockResponseBuilder
{
    use JsonSerializerAwareTrait;
    use XmlSerializerAwareTrait;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var Psr17Factory */
    protected $factory;

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
     * @return self
     */
    public function withStatusCode(int $statusCode = 200): self
    {
        $this->response = $this->response->withStatus($statusCode);

        return $this;
    }

    /**
     * Respond with specified header pattern.
     * @see \Psr\Http\Message\MessageInterface::withHeader()
     *
     * @param string          $name
     * @param string|string[] $value
     *
     * @return self
     */
    public function withHeader(string $name, $value): self
    {
        $this->response = $this->response->withHeader($name, $value);

        return $this;
    }

    /**
     * Respond with specified header pattern appended to existing header.
     * @see \Psr\Http\Message\MessageInterface::withAddedHeader()
     *
     * @param string          $name
     * @param string|string[] $value
     *
     * @return self
     */
    public function withAddedHeader(string $name, $value): self
    {
        $this->response = $this->response->withAddedHeader($name, $value);

        return $this;
    }

    /**
     * Respond with specified headers. Works exactly like calling PockResponseBuilder::withHeader() would work.
     *
     * @param array<string, string|string[]> $headers
     *
     * @return self
     */
    public function withHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->response = $this->response->withHeader($name, $value);
        }

        return $this;
    }

    /**
     * Respond with specified headers. Works exactly like calling PockResponseBuilder::withAddedHeader() would work.
     *
     * @param array<string, string|string[]> $headers
     *
     * @return self
     */
    public function withAddedHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->response = $this->response->withAddedHeader($name, $value);
        }

        return $this;
    }

    /**
     * Reply with specified body. It can be:
     *  - PSR-7 StreamInterface - it will be used without any changes.
     *  - string - it will be used as contents contents.
     *  - resource - it's data will be used as contents contents.
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $stream
     *
     * @return self
     */
    public function withBody($stream): self
    {
        if (is_string($stream)) {
            $stream = $this->factory->createStream($stream);
        }

        if (is_resource($stream)) {
            $stream = $this->factory->createStreamFromResource($stream);
        }

        if ($stream->isSeekable()) {
            $stream->seek(0);
        }

        $this->response = $this->response->withBody($stream);

        return $this;
    }

    /**
     * Reply with data from specified file.
     * For available modes @see \fopen()
     *
     * @param string $path
     * @param string $mode
     *
     * @return self
     * @throws InvalidArgumentException|RuntimeException
     */
    public function withFile(string $path, string $mode = 'r'): self
    {
        $stream = $this->factory->createStreamFromFile($path, $mode);

        if ($stream->isSeekable()) {
            $stream->seek(0);
        }

        $this->response = $this->response->withBody($stream);

        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return self
     * @throws \Pock\Exception\JsonException
     */
    public function withJson($data): self
    {
        $result = static::serializeJson($data);

        if (null !== $result) {
            return $this->withBody($result);
        }

        throw new InvalidArgumentException('Cannot serialize data with type ' . gettype($data));
    }

    /**
     * @param mixed $data
     *
     * @return self
     * @throws \Pock\Exception\XmlException
     */
    public function withXml($data): self
    {
        $result = static::serializeXml($data);

        if (null !== $result) {
            return $this->withBody($result);
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
}
