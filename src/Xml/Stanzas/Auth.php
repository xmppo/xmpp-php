<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Auth extends Stanza
{
    public function authenticate()
    {
        $response = $this->socket->getResponseBuffer()->read();
        $options = $this->socket->getOptions();

        if (self::isTlsRequired($response) && $options->usingTls()) {
            $this->startTls();
            $this->socket->send(self::openXmlStream($options->getHost()));
        }

        $mechanism = $options->getAuthType()->getName();
        $encodedCredentials = $options->getAuthType()->encodedCredentials();
        $nameSpace = "urn:ietf:params:xml:ns:xmpp-sasl";

        $xml = "<auth xmlns='{$nameSpace}' mechanism='{$mechanism}'>{$encodedCredentials}</auth>";

        $this->socket->send($xml);
    }

    protected function startTls()
    {
        $this->socket->send("<starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'/>");
        $response = $this->socket->getResponseBuffer()->read();

        if (!self::canProceed($response)) {
            $this->socket->getOptions()->getLogger()->error(__METHOD__ . '::' . __LINE__ .
                "TLS authentication failed. Trying to continue but will most likely fail.");
        }

        stream_socket_enable_crypto($this->socket->connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
    }
}
