<?php

/**
 * PHP 7.2
 *
 * @category SimpleObject
 * @package  Pock\TestUtils
 */

namespace Pock\TestUtils;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SimpleObject
 *
 * @category SimpleObject
 * @package  Pock\TestUtils
 */
class SimpleObject
{
    public const JSON = '{"field":"test"}';
    public const JMS_XML = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>

EOF;
    public const SYMFONY_XML = <<<'EOF'
<?xml version="1.0"?>
<response><field>test</field></response>

EOF;


    /**
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("field")
     */
    public $field = 'test';
}
