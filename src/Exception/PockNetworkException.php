<?php

/**
 * PHP 7.3
 *
 * @category PockNetworkException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Psr\Http\Client\NetworkExceptionInterface;

/**
 * Class PockNetworkException
 *
 * @category PockNetworkException
 * @package  Pock\Exception
 */
class PockNetworkException extends AbstractRequestAwareException implements NetworkExceptionInterface
{
}
