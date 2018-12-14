# PHP library for XMPP

[![Latest Stable Version](https://poser.pugx.org/norgul/xmpp-php/v/stable)](https://packagist.org/packages/norgul/xmpp-php)
[![Total Downloads](https://poser.pugx.org/norgul/xmpp-php/downloads)](https://packagist.org/packages/norgul/xmpp-php)
[![Latest Unstable Version](https://poser.pugx.org/norgul/xmpp-php/v/unstable)](https://packagist.org/packages/norgul/xmpp-php)
[![License](https://poser.pugx.org/norgul/xmpp-php/license)](https://packagist.org/packages/norgul/xmpp-php)

**Disclaimer**: even though I called it a version, this is in no way production ready
and current repository state is really volatile due to my testing and all-around changes.

This is low level socket implementation for enabling PHP to 
communicate with XMPP due to lack of such libraries online (at least ones I 
could find). 

XMPP core documentation can be found [here](https://xmpp.org/rfcs/rfc6120.html).

Current version is oriented towards simplicity and XMPP understanding under the
hood. Should the need arise, I will expand the library, and by all means feel
free to contribute to the repository. 

# Installation requirements and example

Project requirements are given in `composer.json` file (this assumes you know what 
[Composer](https://getcomposer.org) is):

```
"require": {
    "ext-sockets": "*",
    "php": ">=7.0"
},
```

You can use this library in your project by running:

```
composer require norgul/xmpp-php
```

You can see usage example in `Example.php` file by changing credentials to 
point to your XMPP server and from project root run `php Example.php`.

# Library usage
## Init
In order to start using the library you first need to instantiate a new `Options` 
class. Everything except setting a port number is required. If omitted, port 
will default to `5222` which is XMPP default. Username can be either bare JID, in which
case you will probably have to run `$client->setResource()` (see below), or it can be
in form `JID/resource` in which case resource will be automatically parsed and sent to 
server.

```
$options = new Options();

$options
    ->setHost($host)            // required
    ->setPort($port)            // not required, defaults to 5222
    ->setUsername($username)    // required
    ->setPassword($password);   // required
```

`Options` object is required for establishing the connection and every other subsequent
request, so once set it should not be changed. 

Once this is set you can instantiate new `XmppClient` object and pass the `Options` object in.

## Connect & auth
```
$client = new XmppClient();
$client->connect($options);
```

`$client->connect()` method does a few things:
1. Connects to the socket which was initialized in `XmppClient` constructor
2. Opens XML stream to exchange with XMPP server
3. Tries to authenticate with the server based on provided credentials

Current version supports only `PLAIN` auth method. 

## Setting resource

`$client->setResource()` method sets the resource (duh!). If you have provided username in 
`JID/resource` form, this will be set automatically for you.

## Sending messages

`$client->sendMessage()` takes 3 parameters of which the last one is optional. First parameter
is the actual message you'd like to send, second one is recipient of the message and third
one is type of message to be sent. This is currently set to default to `CHAT`, but will probably
be extended in future releases

## Receiving messages and other responses

Mostly all methods look as if they do nothing unless you get some output back. For this you can 
run one of two public methods:
 
 1. `$client->getRawResponse()` method will fetch the XML from server back. This
is raw unfiltered data. These responses are suitable for any server return. 

2. `$client->getParsedResponse()` ~~method will try to parse the XML response. Even though
it looks intuitive that every response can be parsed, this is not true for example for
first response you will get from server opening the XML session with `<stream:stream>` tag. 
This tag is closed only at the end of the session, thus making this initial XML invalid. 
If response fails to parse, it will fall back to raw response.~~ TODO: finding alternative,
currently does the same as getting raw response.

## Roster

`$client->getRoster()` takes no arguments and fetches current authenticated user roster. 

## More options (not required)

`Options` object can take more options which may be chained but are not required. These are explained
and commented in the code directly in the `Options` class:

```
$options
    ->setSocketWaitPeriod($wait)    // defaults to 1s
    ->setProtocol($protocol)        // defaults to TCP
```
