<?php

/**
 * PHP 7.2
 *
 * @category SchemeMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Enum\RequestScheme;
use Pock\Matchers\SchemeMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class SchemeMatcherTest
 *
 * @category SchemeMatcherTest
 * @package  Pock\Tests\Matchers
 */
class SchemeMatcherTest extends PockTestCase
{
    public function testMatches(): void
    {
        self::assertTrue((new SchemeMatcher(RequestScheme::HTTPS))->matches(static::getTestRequest()));
    }
}
