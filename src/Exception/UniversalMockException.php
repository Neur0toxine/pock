<?php

/**
 * PHP 7.3
 *
 * @category UniversalMockException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class UniversalMockException
 *
 * @category UniversalMockException
 * @package  Pock\Exception
 */
class UniversalMockException extends Exception implements
    ClientExceptionInterface,
    NetworkExceptionInterface,
    RequestExceptionInterface
{
    private $request;

    /**
     * UniversalMockException constructor.
     *
     * @param $request
     */
    public function __construct($request)
    {
        parent::__construct('Default mock exception');

        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
