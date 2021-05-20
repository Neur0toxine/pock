<?php

/**
 * PHP 7.3
 *
 * @category PockClientException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Class PockClientException
 *
 * @category PockClientException
 * @package  Pock\Exception
 */
class PockClientException extends Exception implements ClientExceptionInterface
{
}
