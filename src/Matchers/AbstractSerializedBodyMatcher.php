<?php

/**
 * PHP 7.1
 *
 * @category AbstractSerializedBodyMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use Pock\Comparator\ComparatorLocator;
use Pock\Comparator\RecursiveArrayComparator;
use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;

/**
 * Class AbstractSerializedBodyMatcher
 *
 * @category AbstractSerializedBodyMatcher
 * @package  Pock\Matchers
 */
abstract class AbstractSerializedBodyMatcher implements RequestMatcherInterface
{
    use SeekableStreamDataExtractor;

    /** @var array */
    private $data; // @phpstan-ignore-line

    /**
     * AbstractSerializedBodyMatcher constructor.
     *
     * @phpstan-ignore-next-line
     *
     * @param array                             $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        $body = static::getStreamData($request->getBody());

        if ('' === $body) {
            return false;
        }

        $bodyData = $this->deserialize($body);

        if (null === $bodyData) {
            return false;
        }

        return ComparatorLocator::get(RecursiveArrayComparator::class)->compare($bodyData, $this->data);
    }

    /**
     * Returns an array with deserialized data.
     *
     * @param string $data
     *
     * @phpstan-ignore-next-line
     * @return array|null
     */
    abstract protected function deserialize(string $data): ?array;
}
