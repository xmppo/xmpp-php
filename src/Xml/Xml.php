<?php

namespace Norgul\Xmpp\Xml;

use Norgul\Xmpp\Exceptions\StreamError;

trait Xml
{
    /**
     * Opening tag for starting a XMPP stream exchange.
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

    public static function parseTag($rawResponse, string $tag): array
    {
        preg_match_all("#(<$tag.*?>.*?<\/$tag>)#si", $rawResponse, $matched);

        if (count($matched) <= 1) {
            return [];
        }

        return array_map(function ($match) {
            return @simplexml_load_string($match);
        }, $matched[1]);
    }

    public static function parseFeatures($xml)
    {
        return self::matchInsideOfTag($xml, "stream:features");
    }

    public static function isTlsSupported($xml)
    {
        $matchTag = self::matchCompleteTag($xml, "starttls");
        return !empty($matchTag);
    }

    public static function isTlsRequired($xml)
    {
        if (!self::isTlsSupported($xml)) {
            return false;
        }

        $tls = self::matchCompleteTag($xml, "starttls");
        preg_match("#required#", $tls, $match);
        return count($match) > 0;
    }

    public static function matchCompleteTag($xml, $tag)
    {
        $match = self::matchTag($xml, $tag);
        return is_array($match) && count($match) > 0 ? $match[0] : [];
    }

    public static function matchInsideOfTag($xml, $tag)
    {
        $match = self::matchTag($xml, $tag);
        return is_array($match) && count($match) > 1 ? $match[1] : [];
    }

    private static function matchTag($xml, $tag)
    {
        preg_match("#<$tag.*?>(.*)<\/$tag>#", $xml, $match);
        return count($match) < 1 ? '' : $match;
    }

    public static function canProceed($xml)
    {
        preg_match("#<proceed xmlns=[\'|\"]urn:ietf:params:xml:ns:xmpp-tls[\'|\"]\/>#", $xml, $match);
        return count($match) > 0;
    }

    public static function supportedAuthMethods($xml)
    {
        preg_match_all("#<mechanism>(.*?)<\/mechanism>#", $xml, $match);
        return count($match) < 1 ? [] : $match[1];
    }

    public static function roster($xml)
    {
        preg_match_all("#<iq.*?type=[\'|\"]result[\'|\"]>(.*?)<\/iq>#", $xml, $match);
        return count($match) < 1 ? [] : $match[1];
    }

    /**
     * @param string $response
     * @throws StreamError
     */
    public static function checkForUnrecoverableErrors(string $response)
    {
        preg_match_all("#<stream:error>(<(.*?) (.*?)\/>)<\/stream:error>#", $response, $streamErrors);

        if ((!empty($streamErrors[0])) && count($streamErrors[2]) > 0) {
            throw new StreamError($streamErrors[2][0]);
        }
    }
}
