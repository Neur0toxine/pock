<?php

/**
 * PHP 7.3
 *
 * @category SchemeMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Enum\RequestScheme;
use Pock\Matchers\SchemeMatcher;

/**
 * Class SchemeMatcherTest
 *
 * @category SchemeMatcherTest
 * @package  Pock\Tests\Matchers
 */
class SchemeMatcherTest extends AbstractRequestMatcherTest
{
    public function testMatches(): void
    {
        self::assertTrue((new SchemeMatcher(RequestScheme::HTTPS))->matches(static::getTestRequest()));
    }
}
