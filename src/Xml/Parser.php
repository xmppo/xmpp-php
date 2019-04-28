<?php

namespace Norgul\Xmpp\Xml;

trait Parser
{
    public static function matchTag($xml, $tag)
    {
        preg_match("#<$tag.*?>(.*)<\/$tag>#", $xml, $match);

        if (count($match) < 1) {
            return "";
        }

        return $match[1];
    }
    
    public static function getFeatures($xml)
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
}

