<?php

/**
 * PHP version 7.3
 *
 * @category PortMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Enum\RequestScheme;
use Psr\Http\Message\RequestInterface;

/**
 * Class PortMatcher
 *
 * @category PortMatcher
 * @package  Pock\Matchers
 */
class PortMatcher implements RequestMatcherInterface
{
    /** @var int */
    protected $port;

    /**
     * PortMatcher constructor.
     *
     * @param int $port
     */
    public function __construct(int $port)
    {
        $this->port = $port;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $port = $request->getUri()->getPort();

        if (null === $port) {
            switch ($request->getUri()->getScheme()) {
                case RequestScheme::HTTP:
                    return 80 === $this->port;
                case RequestScheme::HTTPS:
                    return 443 === $this->port;
                default:
                    return false;
            }
        }

        return $port === $this->port;
    }
}
