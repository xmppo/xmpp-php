<?php

namespace Norgul\Xmpp\Xml;

class Xml
{
    /**
     * Opening tag for starting a XMPP stream exchange. One session equals to
     * one XML document so this constant is purposely not properly closed as
     * all communication happens in between open-close tags
     */
    const OPEN_TAG = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<stream:stream to="%s" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client" version="1.0">
XML;

    /**
     * Closing tag for one XMPP stream session
     */
    const CLOSE_TAG = '</stream:stream>';

    /**
     * Quote XML input.
     *
     * @param $input
     * @return string
     */
    public static function quote($input)
    {
        return htmlspecialchars($input, ENT_XML1, 'utf-8');
    }

    /**
     * @param $rawResponse
     * @param string $tag
     * @return array
     */
    public static function parseTag($rawResponse, string $tag): array
    {
        $response = [];

        if (preg_match_all("#(<$tag.*?>.*?<\/$tag>)#si", $rawResponse, $matched) && count($matched) > 1) {
            foreach ($matched[1] as $match) {
                $response[] = @simplexml_load_string($match);
            }
        }

        return $response;
    }

    private static function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node)
            $out[$index] = (is_object($node)) ? self::xml2array($node) : $node;

        return $out;
    }
}