<?php

/**
 * PHP 7.1
 *
 * @category HeaderMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\LtrScalarArrayComparator;
use Psr\Http\Message\RequestInterface;

/**
 * Class HeaderMatcher
 *
 * @category HeaderMatcher
 * @package  Pock\Matchers
 */
class HeaderMatcher implements RequestMatcherInterface
{
    /** @var string */
    protected $header;

    /** @var string[] */
    protected $value;

    /**
     * HeaderMatcher constructor.
     *
     * @param string          $header
     * @param string|string[] $value
     */
    public function __construct(string $header, $value)
    {
        $this->header = $header;

        if (is_string($value)) {
            $this->value = [$value];
        } elseif (is_array($value)) {
            $this->value = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if (!$request->hasHeader($this->header)) {
            return false;
        }

        return ComparatorLocator::get(LtrScalarArrayComparator::class)
            ->compare($this->value, $request->getHeader($this->header));
    }
}
