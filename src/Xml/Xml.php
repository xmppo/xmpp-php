<?php

namespace Norgul\Xmpp\Xml;

use Norgul\Xmpp\Exceptions\StreamError;

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
        $xmlOpen = "<?xml version='1.0' encoding='UTF-8'?>";
        $to = "to='{$host}'";
        $stream = "xmlns:stream='http://etherx.jabber.org/streams'";
        $client = "xmlns='jabber:client'";
        $version = "version='1.0'";

        return "{$xmlOpen}<stream:stream $to $stream $client $version>";
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

        if (!$matchByTag || count($matched) <= 1) {
            return [];
        }

        $response = [];

        foreach ($matched[1] as $match) {
            $response[] = @simplexml_load_string($match);
        }

        return $response;
    }

    public static function matchTag($xml, $tag)
    {
        preg_match("#<$tag.*?>(.*)<\/$tag>#", $xml, $match);

        if (count($match) < 1) {
            return "";
        }

        return $match[1];
    }

    public static function parseFeatures($xml)
    {
        return self::matchTag($xml, "stream:features");
    }

    public static function isTlsSupported($xml)
    {
        return !empty(self::matchTag($xml, "starttls"));
    }

    public static function isTlsRequired($xml)
    {
        if (!self::isTlsSupported($xml)) {
            return false;
        }

        $tls = self::matchTag($xml, "starttls");
        preg_match("#required#", $tls, $match);
        return count($match) > 0;
    }

    public static function canProceed($xml)
    {
        preg_match("#<proceed xmlns='urn:ietf:params:xml:ns:xmpp-tls'/>#", $xml, $match);
        return count($match) > 0;
    }

    public static function supportedAuthMethods($xml)
    {
        preg_match_all("#<mechanism>(.*?)<\/mechanism>#", $xml, $match);

        if (count($match) < 1) {
            return [];
        }

        return $match[1];
    }

    public static function roster($xml)
    {
        preg_match_all("#<iq.*?type='result'>(.*?)<\/iq>#", $xml, $match);

        if (count($match) < 1) {
            return [];
        }

        return $match[1];
    }

    public static function hasUnrecoverableErrors(string $response)
    {
        preg_match_all("#<stream:error>(<(.*?) (.*?)\/>)<\/stream:error>#", $response, $streamErrors);

        if ((!empty($streamErrors[0])) && count($streamErrors[2]) > 0) {
            throw new StreamError($streamErrors[2][0]);
        }
    }
}
