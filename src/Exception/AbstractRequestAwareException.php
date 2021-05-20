<?php

/**
 * PHP 7.3
 *
 * @category AbstractRequestAwareException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;
use Psr\Http\Message\RequestInterface;

/**
 * Class AbstractRequestAwareException
 *
 * @category AbstractRequestAwareException
 * @package  Pock\Exception
 */
class AbstractRequestAwareException extends Exception
{
    /** @var RequestInterface */
    private $request;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return self
     */
    public function setRequest(RequestInterface $request): self
    {
        $instance = new static($this->message, $this->code, $this->getPrevious()); // @phpstan-ignore-line
        $instance->request = $request;
        return $instance;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
