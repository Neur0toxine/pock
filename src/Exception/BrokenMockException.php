<?php

/**
 * PHP 7.3
 *
 * @category BrokenMockException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;
use Pock\MockInterface;

/**
 * Class BrokenMockException
 *
 * @category BrokenMockException
 * @package  Pock\Exception
 */
class BrokenMockException extends Exception
{
    /** @var \Pock\MockInterface */
    private $mock;

    /**
     * AbstractMockException constructor.
     *
     * @param \Pock\MockInterface $mock
     */
    public function __construct(MockInterface $mock)
    {
        parent::__construct('Neither response nor the throwable is set in the mock.');

        $this->mock = $mock;
    }

    /**
     * @return \Pock\MockInterface
     */
    public function getMock(): MockInterface
    {
        return $this->mock;
    }
}
