<?php

namespace Norgul\Xmpp;

use Exception;
use Norgul\Xmpp\Authentication\Auth;
use Norgul\Xmpp\Authentication\AuthTypes\Plain;

/**
 * Class Socket
 * @package Norgul\Xmpp
 */
class XmppClient
{
    protected $socket;
    protected $options;

    /**
     * XmppClient constructor. Initializing a new socket
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Open socket to host:port and authenticate with given credentials
     */
    public function connect()
    {
        $this->socket = stream_socket_client($this->options->getFullSocketAddress());

        // Wait max 3 seconds by default before terminating the socket. Can be changed with options
        stream_set_timeout($this->socket, $this->options->getSocketWaitPeriod());

        /**
         * Opening stream to XMPP server
         */
        $this->send(Xml::OPEN_TAG);

        /**
         * Try to assign a resource if it exists. If bare JID is forwarded, this will default to your username
         */
        $username = $this->splitUsernameResource($this->options->getUsername());

        $this->authenticate($username, $this->options->getPassword());
        $this->setResource($this->options->getResource());

        /**
         * Initial presence stanza sent to server to check roster and send
         * presence notification to each person subscribed to you
         */
        $this->presence();
    }

    /**
     * Sending XML stanzas to open socket
     * @param $xml
     * @return bool
     */
    public function send(string $xml)
    {
        try {
            fwrite($this->socket, $xml);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Send message to Jabber user
     * TODO: refactor to use object for type instead of string
     *
     * @param $message
     * @param $to
     * @param $type
     * @return bool
     */
    public function sendMessage(string $message, string $to, string $type = "CHAT")
    {
        $preparedString = str_replace(
            ['{message}', '{to}', '{type}'],
            [Xml::quote($message), Xml::quote($to), Xml::quote($type)],
            Xml::MESSAGE
        );

        $this->send($preparedString);
        return true;
    }

    /**
     * Set resource
     * @param $resource
     * @return bool
     */
    public function setResource(string $resource)
    {
        if (empty($resource) || $resource == '')
            return false;

        $preparedString = str_replace('{resource}', Xml::quote($resource), Xml::RESOURCE);
        $this->send($preparedString);
        return true;
    }

    /**
     * Get roster for currently authenticated user
     * @return bool
     */
    public function getRoster()
    {
        $this->send(Xml::ROSTER);
        return true;
    }

    // TODO: not working? Not getting a server response

    /**
     * Ask the user to accept a presence subscription event
     * Response should be:
     * - subscribed is user accepted
     * - unsubscribed if user declined
     *
     * @param $from
     * @param $to
     * @param string $type
     * @return bool
     */
    public function requestPresence($from, $to, $type = "subscribe")
    {
        $preparedString = str_replace(
            ['{from}', '{to}', '{type}'],
            [Xml::quote($from), Xml::quote($to), Xml::quote($type)],
            Xml::PRESENCE
        );

        $this->send($preparedString);
        return true;
    }

    /**
     * Closed presence stanza, used in initial communication with server
     * @return bool
     */
    private function presence()
    {
        $this->send('<presence/>');
        return true;
    }

    /**
     * Authenticate user with given XMPP server
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password)
    {
        $preparedString = Auth::authenticate(new Plain(), $username, $password);
        $this->send($preparedString);
        return true;
    }

    /**
     * Get response from server if any.
     * @return string
     */
    public function getResponse(): string
    {
        $response = '';
        while ($out = fgets($this->socket)) {
            $response .= $out;
        }

        return $response;
    }

    /**
     * Extracting messages from the response
     * @return array
     */
    public function getMessages(): array
    {
        return $this->getXmlTag("message");
    }

    /**
     * @param string $tag
     * @return array
     */
    public function getXmlTag(string $tag): array
    {
        $rawResponse = $this->getResponse();
        $response = [];

        if (preg_match_all("#(<$tag.*?>.*?<\/$tag>)#si", $rawResponse, $matched) && count($matched) > 1) {
            foreach ($matched[1] as $match) {
                $response[] = @simplexml_load_string($match);
            }
        }

        return $response;
    }

    /**
     * End the XMPP session and close the socket
     */
    public function disconnect()
    {
        $this->send(Xml::CLOSE_TAG);
        fclose($this->socket);
    }

    /**
     * Try to extract resource from JID. If not present, this will default to
     *
     * @param string $username
     * @return string
     */
    protected function splitUsernameResource(string $username): string
    {
        $usernameResource = explode('/', $username);

        if (count($usernameResource) > 1) {
            $this->options->setResource($usernameResource[1]);
            return $usernameResource[0];
        }
        return $username;
    }


}