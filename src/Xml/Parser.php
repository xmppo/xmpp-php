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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$xml = <<<ttt
<?xml version='1.0'?><stream:stream xml:lang='en' xmlns:stream='http://etherx.jabber.org/streams' version='1.0' xmlns='jabber:client' from='616.pub' id='78150744-1f58-4b91-acf7-b46aabeacd80'><stream:features><starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'><required/></starttls></stream:features><proceed xmlns='urn:ietf:params:xml:ns:xmpp-tls'/><mechanisms xmlns='urn:ietf:params:xml:ns:xmpp-sasl'><mechanism>PLAIN</mechanism><mechanism>SCRAM-SHA-1-PLUS</mechanism><mechanism>SCRAM-SHA-1</mechanism></mechanisms>
ttt;

//echo print_r(Parser::supportedAuthMethods($xml));
echo Parser::canProceed($xml) ? 't' : 'f';
