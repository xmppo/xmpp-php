<?php

namespace Norgul\Xmpp\Xml;

trait Xml
{
    /**
     * Opening tag for starting a XMPP stream exchange. One session equals to
     * one XML document so this constant is purposely not properly closed as
     * all communication happens in between open-close tags
     * @param $host
     * @return string
     */
    public static function openXmlStream($host)
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <stream:stream to=\"{$host}\" 
                xmlns:stream=\"http://etherx.jabber.org/streams\" 
                xmlns=\"jabber:client\" version=\"1.0\">";
    }

    /**
     * Closing tag for one XMPP stream session
     */
    public static function closeXmlStream()
    {
        return '</stream:stream>';
    }

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
        $matchByTag = preg_match_all("#(<$tag.*?>.*?<\/$tag>)#si", $rawResponse, $matched);

        if (!$matchByTag || count($matched) <= 1)
            return [];

        $response = [];

        foreach ($matched[1] as $match) {
            $response[] = @simplexml_load_string($match);
        }

        return $response;
    }
}