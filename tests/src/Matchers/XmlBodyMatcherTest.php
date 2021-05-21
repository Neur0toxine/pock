<?php

/**
 * PHP 7.3
 *
 * @category XmlBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */

namespace Pock\Tests\Matchers;

use Pock\Matchers\XmlBodyMatcher;
use Pock\TestUtils\PockTestCase;

/**
 * Class XmlBodyMatcherTest
 *
 * @category XmlBodyMatcherTest
 * @package  Pock\Tests\Matchers
 */
class XmlBodyMatcherTest extends PockTestCase
{
    public function testEmptyXml(): void
    {
        $this->expectExceptionMessage('XML must not be empty.');
        new XmlBodyMatcher('');
    }

    public function testInvalidXml(): void
    {
        $brokenXml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]></field>
</result>
EOF;

        $this->expectExceptionMessage('DOMDocument::loadXML(): CData section not finished');
        new XmlBodyMatcher($brokenXml);
    }

    public function testMatchXml(): void
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field key="2" id="1"><![CDATA[test]]></field>
</result>
EOF;
        $actual = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>

<result>
  <field id="1" key="2">
    <![CDATA[test]]>
  </field>
</result>
EOF;

        self::assertTrue((new XmlBodyMatcher($expected))->matches(static::getRequestWithBody($actual)));
    }
}
