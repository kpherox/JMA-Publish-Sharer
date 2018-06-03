<?php

namespace App\Services;

class SimpleXML
{
    private $rawXml;
    private $isNamespaced;
    private $prefixNamespace;

    /**
     * Create a new instance.
     *
     * @return void
    **/
    public function __construct(String $xml, Bool $isNamespaced = false, Bool $prefixNamespace = false)
    {
        $this->rawXml = $xml;
        $this->isNamespaced = $isNamespaced;
        $this->prefixNamespace = $prefixNamespace;
    }

    public function toSimpleXMLElement(Bool $isExpandAttributes = false) : \SimpleXMLElement
    {
        $xml = $this->isNamespaced ? $this->removeNamespace($this->rawXml) : $this->rawXml;
        $simpleXml = simplexml_load_string($xml);

        if ($isExpandAttributes) {
            self::expandAttributes($simpleXml);
        }

        return $simpleXml;
    }

    public function toJson(Bool $isExpandAttributes = false) : String
    {
        return json_encode($this->toSimpleXMLElement($isExpandAttributes));
    }

    public function toArray(Bool $isExpandAttributes = false, Bool $isAssoc = false) : Array
    {
        return json_decode($this->toJson($isExpandAttributes), $isAssoc);
    }

    /**
     * Remove namespace from xml.
     *
     * @referenced https://laracasts.com/discuss/channels/general-discussion/converting-xml-to-jsonarray
    **/
    private function removeNamespace(String $xml) : String
    {
        $toRemove = collect(simplexml_load_string($xml)->getNamespaces(true))->keys();
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        foreach( $toRemove as $remove ) {
            $replaced = $this->prefixNamespace ? $remove . '_' : '';
            $xml = str_replace('<' . $remove . ':', '<' . $replaced, $xml);
            $xml = str_replace('</' . $remove . ':', '</' . $replaced, $xml);
            $xml = str_replace($remove . ':commentText', $replaced . 'commentText', $xml);
            $pattern = "/xmlns:{$remove}{$nameSpaceDefRegEx}/";
            $xml = preg_replace($pattern, '', $xml, 1);
        }
        return $xml;
    }

    public static function expandAttributes(\SimpleXMLElement $node)
    {
        foreach ($node->children() as $child) {
            if ($child->count() > 0) {
                $child = self::expandAttributes($child);
                continue;
            }

            $attrs = $child->attributes();
            if (empty($attrs)) {
                continue;
            }

            $childAttributes = $node->addChild($child->getName() . "@attributes");

            foreach ($attrs as $key => $val) {
                $childAttributes->addChild($key, $val);
            }
        }
    }
}
