<?php

namespace App\Services;

class SimpleXML
{
    /**
     * Raw xml string.
     *
     * @var string
     */
    private $rawXml;

    /**
     * Has namespace.
     *
     * @var bool
     */
    private $isNamespaced;

    /**
     * Leave prefix namespace.
     *
     * @var bool
     */
    private $prefixNamespace;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(string $xml, bool $isNamespaced = false, bool $prefixNamespace = false)
    {
        $this->rawXml = $xml;
        $this->isNamespaced = $isNamespaced;
        $this->prefixNamespace = $prefixNamespace;
    }

    /**
     * @param  bool $isExpandAttributes default: false
     */
    public function toSimpleXMLElement(bool $isExpandAttributes = false) : \SimpleXMLElement
    {
        try {
            $xml = $this->isNamespaced ? $this->removeNamespace($this->rawXml) : $this->rawXml;
            $simpleXml = $this->simpleXmlElement($xml);
        } catch (\Exception $e) {
            throw $e;
        }

        if ($isExpandAttributes) {
            self::expandAttributes($simpleXml);
        }

        return $simpleXml;
    }

    /**
     * @param  bool $isExpandAttributes default: false
     */
    public function toJson(bool $isExpandAttributes = false) : string
    {
        try {
            return json_encode($this->toSimpleXMLElement($isExpandAttributes));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param  bool $isExpandAttributes default: false
     */
    public function toArray(bool $isExpandAttributes = false) : array
    {
        try {
            return json_decode($this->toJson($isExpandAttributes), true);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param  bool $isExpandAttributes default: false
     */
    public function toObject(bool $isExpandAttributes = false) : \stdClass
    {
        try {
            return json_decode($this->toJson($isExpandAttributes), false);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function simpleXmlElement(string $xml) : \SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $simpleXml = simplexml_load_string($xml);

        if (! $simpleXml) {
            $message = 'XML Parse error';

            foreach (libxml_get_errors() as $error) {
                $message .= "\n".trim($error->message);
            }
            libxml_clear_errors();

            throw new \Exception($message);
        }

        libxml_use_internal_errors(false);

        return $simpleXml;
    }

    /**
     * Remove namespace from xml.
     *
     * @referenced https://laracasts.com/discuss/channels/general-discussion/converting-xml-to-jsonarray
     *
     * @param  string $xml
     */
    private function removeNamespace(string $xml) : string
    {
        try {
            $namespaces = collect($this->simpleXmlElement($xml)->getNamespaces(true))->keys();
        } catch (\Exception $e) {
            throw $e;
        }
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        $namespaces->each(function ($namespace) use (&$xml, $nameSpaceDefRegEx) {
            $remove = $namespace.':';
            $replaced = $this->prefixNamespace ? $namespace.'_' : '';
            $xml = str_replace('<'.$remove, '<'.$replaced, $xml);
            $xml = str_replace('</'.$remove, '</'.$replaced, $xml);
            $xml = str_replace($remove.'commentText', $replaced.'commentText', $xml);
            $pattern = "/xmlns:{$namespace}{$nameSpaceDefRegEx}/";
            $xml = preg_replace($pattern, '', $xml, 1);
        });

        return $xml;
    }

    /**
     * @param  \SimpleXMLElement $node
     */
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

            $childAttributes = $node->addChild($child->getName().'@attributes');

            foreach ($attrs as $key => $val) {
                $childAttributes->addChild($key, $val);
            }
        }
    }
}
