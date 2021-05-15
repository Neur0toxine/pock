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
    public const XML = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <field><![CDATA[test]]></field>
</result>

EOF;

    /**
     * @var string
     *
     * @JMS\Type("string")
     * @JMS\SerializedName("field")
     */
    protected $field = 'test';
}
