<?php

/**
 * PHP 7.2
 *
 * @category UnsupportedRequestException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;

/**
 * Class UnsupportedRequestException
 *
 * @category UnsupportedRequestException
 * @package  Pock\Exception
 */
class UnsupportedRequestException extends Exception
{
    public function __construct()
    {
        parent::__construct('Cannot match request with any available matchers');
    }
}
