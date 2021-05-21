<?php

/**
 * PHP 7.2
 *
 * @category Mock
 * @package  Pock
 */

namespace Pock;

use Pock\Exception\PockNetworkException;
use Pock\Exception\PockRequestException;
use Pock\Factory\ReplyFactoryInterface;
use Pock\Matchers\RequestMatcherInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class Mock
 *
 * @category Mock
 * @package  Pock
 */
class Mock implements MockInterface
{
    /** @var \Pock\Matchers\RequestMatcherInterface */
    private $matcher;

    /** @var \Pock\Factory\ReplyFactoryInterface|null */
    private $replyFactory;

    /** @var \Psr\Http\Message\ResponseInterface|null */
    private $response;

    /** @var \Throwable|null */
    private $throwable;

    /** @var int */
    private $hits;

    /** @var int */
    private $maxHits;

    /** @var int */
    private $matchAt;

    /**
     * Mock constructor.
     *
     * @param \Pock\Matchers\RequestMatcherInterface   $matcher
     * @param \Pock\Factory\ReplyFactoryInterface|null $replyFactory
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @param \Throwable|null                          $throwable
     * @param int                                      $maxHits
     * @param int                                      $matchAt
     */
    public function __construct(
        RequestMatcherInterface $matcher,
        ?ReplyFactoryInterface $replyFactory,
        ?ResponseInterface $response,
        ?Throwable $throwable,
        int $maxHits,
        int $matchAt
    ) {
        $this->matcher = $matcher;
        $this->replyFactory = $replyFactory;
        $this->response = $response;
        $this->throwable = $throwable;
        $this->matchAt = $matchAt;
        $this->maxHits = $maxHits;
        $this->hits = 0;

        if ($this->maxHits < ($matchAt + 1) && -1 !== $this->maxHits) {
            $this->maxHits = $matchAt + 1;
        }
    }

    /**
     * @inheritDoc
     */
    public function registerHit(): MockInterface
    {
        if (-1 !== $this->maxHits) {
            ++$this->hits;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function available(): bool
    {
        return -1 === $this->maxHits || $this->hits < $this->maxHits;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if ($this->matcher->matches($request)) {
            if ($this->matchAt <= 0) {
                return true;
            }

            if ($this->matchAt === $this->hits) {
                return true;
            }

            $this->registerHit();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ?ResponseInterface
    {
        if (
            null !== $this->response &&
            null !== $this->response->getBody() &&
            $this->response->getBody()->isSeekable()
        ) {
            $this->response->getBody()->seek(0);
        }

        return $this->response;
    }

    /**
     * @inheritDoc
     */
    public function getReplyFactory(): ?ReplyFactoryInterface
    {
        return $this->replyFactory;
    }

    /**
     * @inheritDoc
     */
    public function getThrowable(RequestInterface $request): ?Throwable
    {
        if ($this->throwable instanceof PockRequestException || $this->throwable instanceof PockNetworkException) {
            return $this->throwable->setRequest($request);
        }

        return $this->throwable;
    }
}
