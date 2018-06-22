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

        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string($xml);

        if (!$simpleXml) {
            $message = 'XML Parse error';

            foreach(libxml_get_errors() as $error) {
                $message .= "\n" . trim($error->message);
            }
            libxml_clear_errors();

            throw new \Exception($message);
        }

        if ($isExpandAttributes) {
            self::expandAttributes($simpleXml);
        }

        return $simpleXml;
    }

    public function toJson(Bool $isExpandAttributes = false) : String
    {
        try {
            return json_encode($this->toSimpleXMLElement($isExpandAttributes));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return Array|Object
    **/
    public function toArray(Bool $isExpandAttributes = false, Bool $isAssoc = false)
    {
        try {
            return json_decode($this->toJson($isExpandAttributes), $isAssoc);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove namespace from xml.
     *
     * @referenced https://laracasts.com/discuss/channels/general-discussion/converting-xml-to-jsonarray
    **/
    private function removeNamespace(String $xml) : String
    {
        $namespaces = collect(simplexml_load_string($xml)->getNamespaces(true))->keys();
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        $namespaces->each(function ($namespace) use (&$xml, $nameSpaceDefRegEx) {
            $remove = $namespace . ':';
            $replaced = $this->prefixNamespace ? $namespace . '_' : '';
            $xml = str_replace('<' . $remove, '<' . $replaced, $xml);
            $xml = str_replace('</' . $remove, '</' . $replaced, $xml);
            $xml = str_replace($remove . 'commentText', $replaced . 'commentText', $xml);
            $pattern = "/xmlns:{$namespace}{$nameSpaceDefRegEx}/";
            $xml = preg_replace($pattern, '', $xml, 1);
        });
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
