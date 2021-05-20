<?php

/**
 * PHP 7.3
 *
 * @category PockRequestException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Psr\Http\Client\RequestExceptionInterface;

/**
 * Class PockRequestException
 *
 * @category PockRequestException
 * @package  Pock\Exception
 */
class PockRequestException extends AbstractRequestAwareException implements RequestExceptionInterface
{
}
