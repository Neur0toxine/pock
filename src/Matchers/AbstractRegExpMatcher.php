<?php

/**
 * PHP version 7.3
 *
 * @category AbstractRegExpMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

/**
 * Class AbstractRegExpMatcher
 *
 * @category AbstractRegExpMatcher
 * @package  Pock\Matchers
 */
abstract class AbstractRegExpMatcher implements RequestMatcherInterface
{
    /** @var string */
    protected $expression;

    /** @var int */
    protected $flags = 0;

    /**
     * @param string $expression
     * @param int    $flags
     */
    public function __construct(string $expression, int $flags = 0)
    {
        $this->expression = $expression;
        $this->flags = $flags;
    }

    protected function matchRegExp(string $content): bool
    {
        $matches = [];
        return 1 === preg_match($this->expression, $content, $matches, $this->flags);
    }
}
