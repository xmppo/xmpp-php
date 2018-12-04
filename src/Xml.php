<?php

namespace Norgul\Xmpp;

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



    const AUTH = <<<AUTH
<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="{mechanism}">{encoded}</auth>
AUTH;


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


}