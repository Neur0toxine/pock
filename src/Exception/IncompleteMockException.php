<?php

/**
 * PHP 7.2
 *
 * @category IncompleteMockException
 * @package  Pock\Exception
 */

namespace Pock\Exception;

use Exception;
use Pock\MockInterface;

/**
 * Class IncompleteMockException
 *
 * @category IncompleteMockException
 * @package  Pock\Exception
 */
class IncompleteMockException extends Exception
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
