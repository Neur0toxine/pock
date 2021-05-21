<?php

/**
 * PHP 7.3
 *
 * @category XmlBodyMatcher
 * @package  Pock\Matchers
 */

namespace Pock\Matchers;

use DOMDocument;
use Pock\Exception\XmlException;
use Pock\Traits\SeekableStreamDataExtractor;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use XSLTProcessor;

/**
 * Class XmlBodyMatcher
 *
 * @category XmlBodyMatcher
 * @package  Pock\Matchers
 */
class XmlBodyMatcher extends BodyMatcher
{
    use SeekableStreamDataExtractor;

    private const TAG_SORT_XSLT = <<<EOT
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>
 <xsl:strip-space elements="*"/>
 <xsl:template match="node()|@*">
  <xsl:copy>
   <xsl:apply-templates select="@*">
    <xsl:sort select="name()"/>
   </xsl:apply-templates>
   <xsl:apply-templates select="node()">
    <xsl:sort select="name()"/>
   </xsl:apply-templates>
  </xsl:copy>
 </xsl:template>
</xsl:stylesheet>
EOT;

    /** @var XSLTProcessor|null */
    private static $sorter;

    /** @var bool */
    private $useFallback;

    /**
     * XmlBodyMatcher constructor.
     *
     * @param DOMDocument|\Psr\Http\Message\StreamInterface|resource|string $referenceXml
     *
     * @throws \Pock\Exception\XmlException
     */
    public function __construct($referenceXml)
    {
        if (!extension_loaded('xsl') || !extension_loaded('dom')) {
            $this->useFallback = true;
        }

        if (!extension_loaded('xsl')) {
            $this->useFallback = true;

            if (extension_loaded('dom') && $referenceXml instanceof DOMDocument) {
                $referenceXml = static::getDOMString($referenceXml);
            }

            parent::__construct($referenceXml); // @phpstan-ignore-line

            return;
        }

        if ($referenceXml instanceof DOMDocument) {
            parent::__construct(static::sortXmlTags($referenceXml));

            return;
        }

        parent::__construct(static::sortXmlTags(
            static::createDOMDocument(static::getEntryItemData($referenceXml))
        ));
    }

    /**
     * @inheritDoc
     */
    public function matches(RequestInterface $request): bool
    {
        if ($this->useFallback) {
            return parent::matches($request);
        }

        if (0 === $request->getBody()->getSize()) {
            return '' === $this->contents;
        }

        return self::sortXmlTags(self::createDOMDocument(self::getStreamData($request->getBody()))) === $this->contents;
    }

    /**
     * Returns new document with tags sorted alphabetically.
     *
     * @param \DOMDocument $document
     *
     * @return string
     * @throws \RuntimeException|\Pock\Exception\XmlException
     */
    private static function sortXmlTags(DOMDocument $document): string
    {
        $xml = static::getSorter()->transformToXml($document);

        if (false === $xml) {
            throw new RuntimeException('Cannot sort XML nodes');
        }

        return $xml;
    }

    /**
     * Returns XSLTProcessor with XSLT which sorts tags alphabetically.
     *
     * @return \XSLTProcessor
     * @throws \Pock\Exception\XmlException
     */
    private static function getSorter(): XSLTProcessor
    {
        if (null === static::$sorter) {
            static::$sorter = new XSLTProcessor();
            static::$sorter->importStylesheet(static::createDOMDocument(static::TAG_SORT_XSLT));
        }

        return static::$sorter;
    }

    /**
     * Create DOMDocument with provided XML string.
     *
     * @param string $xml
     * @param string $version
     * @param string $encoding
     *
     * @return \DOMDocument
     * @throws \Pock\Exception\XmlException
     */
    private static function createDOMDocument(string $xml, string $version = '1.0', string $encoding = ''): DOMDocument
    {
        if ('' === $xml) {
            throw new XmlException('XML must not be empty.');
        }

        $error = null;
        $document = new DOMDocument($version, $encoding);

        try {
            set_error_handler(static function ($code, $message) {
                throw new XmlException($message, $code);
            });
            $document->loadXML(trim($xml));
        } catch (XmlException $exception) {
            $error = $exception;
        } finally {
            restore_error_handler();
        }

        if (null !== $error) {
            throw $error;
        }

        return $document;
    }

    /**
     * @param \DOMDocument $document
     *
     * @return string
     * @throws \Pock\Exception\XmlException
     */
    private static function getDOMString(DOMDocument $document): string
    {
        $result = $document->saveXML();

        if (false === $result) {
            throw new XmlException('Cannot export XML.');
        }

        return $result;
    }
}
