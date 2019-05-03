<?php

namespace Norgul\Xmpp\Xml\Stanzas;

class Auth extends Stanza
{
    public function authenticate()
    {
        if (self::isTlsRequired($this->readResponseFile()) && $this->options->usingTls()) {
            $this->startTls();
            $this->socket->autoAnswerSend(self::openXmlStream($this->options->getHost()));
        }

        $mechanism = $this->options->getAuthType()->getName();
        $encodedCredentials = $this->options->getAuthType()->encodedCredentials();
        $nameSpace = "urn:ietf:params:xml:ns:xmpp-sasl";

        $xml = "<auth xmlns='{$nameSpace}' mechanism='{$mechanism}'>{$encodedCredentials}</auth>";

        $this->socket->autoAnswerSend($xml);
        $this->socket->autoAnswerSend(self::openXmlStream($this->options->getHost()));
    }

    protected function startTls()
    {
        $this->socket->autoAnswerSend("<starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'/>");

        if (!self::canProceed($this->readResponseFile())) {
            $this->options->getLogger()->error("TLS authentication failed. 
            Trying to continue but will most likely fail.");
        }

        stream_socket_enable_crypto($this->socket->connection, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
    }
}
