# PHP client library for XMPP (Jabber) protocol

[![Latest Stable Version](https://poser.pugx.org/norgul/xmpp-php/v/stable)](https://packagist.org/packages/norgul/xmpp-php)
[![Total Downloads](https://poser.pugx.org/norgul/xmpp-php/downloads)](https://packagist.org/packages/norgul/xmpp-php)
[![Latest Unstable Version](https://poser.pugx.org/norgul/xmpp-php/v/unstable)](https://packagist.org/packages/norgul/xmpp-php)
[![Build Status](https://travis-ci.org/Norgul/xmpp-php.svg?branch=master)](https://travis-ci.org/Norgul/xmpp-php)
[![License](https://poser.pugx.org/norgul/xmpp-php/license)](https://packagist.org/packages/norgul/xmpp-php)

This is low level socket implementation for enabling PHP to 
communicate with XMPP due to lack of such libraries online (at least ones I 
could find that had decent documentation). 

XMPP core documentation can be found [here](https://xmpp.org/rfcs/rfc6120.html).

# Installation requirements and example

Project requirements are given in `composer.json` (
[Composer website](https://getcomposer.org)):

You can use this library in your project by running:

```
composer require norgul/xmpp-php
```

You can see usage example in `Example.php` file by changing credentials to 
point to your XMPP server and from project root run `php Example.php`.

# Library usage
## Initialization
In order to start using the library you first need to instantiate a new `Options` 
class. Host, username and password are mandatory fields, while port number, if omitted,
will default to `5222` which is XMPP default. 

Username can be either bare `JID` or in `JID/resource` form. If you are using a bare `JID`
the resource will be added automatically. You can override this by explicitly setting a 
resource with `$client->iq->setResource()`. In the second case the username will be automatically
parsed to `username` and `resource` variables. In case of `JID/resource/xyz` format, everything
after second slash will be ignored. If both `JID/resource` is present as well as using the
`$client->iq->setResource()` method, which ever was defined last will take precedence. 

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

Once this is set you can instantiate a new `XmppClient` object and pass the `Options` object in.

## XMPP client class explanation
Since XMPP is all about 3 major stanzas, (**IQ, Message and Presence**), I've 
created separate classes which are dependant on socket implementation so that you 
can directly send XML by calling a stanza method. 

This means that 3 stanzas have been made available from `XmppClient` class constructor
to be used like a chained method on client's concrete class. 

Current logic thus is `$client->STANZA->METHOD()`. For example:

```
$client->iq->getRoster();
$client->message->send();
$client->presence->subscribe();
```

## Connecting to the server
Beside being a stanza wrapper, `XmppClient` class offers a few public methods.

`$client->connect()` method does a few things:
1. Connects to the socket which was initialized in `XmppClient` constructor
2. Opens an XML stream to exchange with the XMPP server
3. Tries to authenticate with the server based on provided credentials
4. Starts the initial communication with the server, bare minimum to get you started

Current version supports `PLAIN` and `DIGEST-MD5` auth methods. 

TLS is supported by default. If server has support for TLS, library will 
automatically try to connect with TLS and make the connection secure. 

If you'd like to explicitly disable this functionality, you can use `setUseTls(false)` 
function on the `Options` instance so that TLS communication is disabled. 

## Sending raw data
`send()` message is exposed as being public in `XmppClient` class, and its intention
is to send raw XML data to the server. For it to work correctly, XML which you send
has to be valid XML. 

## Getting raw response
Server responses (or server side continuous XML session to be exact) can be retrieved with 
`$client->getResponse()`. This should be used in an infinite loop or for more sustainable
solution in some WebSocket solution like [Ratchet](http://socketo.me/) if you'd like to 
see continuous stream of everything coming from the server.

If you would like to see the output of the received response in the console you can call the
`$client->prettyPrint($response)` method.

## Receiving messages and other responses

In case you are not interested in complete response which comes from server, you may also use 
`$client->message->receive()` (`$client->getMessages()` was removed because it was just a shorthand
method for this one) which will match message tags with regex and return array of matched messages. 
In case you'd like to see the response in the terminal, you can do something like this:

```
do {
    $response = $client->message->receive();
    if($response)
        echo print_r($response);
} while (true);
```

## Disconnect
Disconnect method sends closing XML to the server to end the currently open session and
closes the open socket. 

# Stanza method breakdown
Remember from [here](#xmpp-client-class-explanation) -> `$client->STANZA->METHOD()`

## Message
`send()` - sending a message to someone. Takes 3 parameters of which the last one is 
optional. First parameter is the actual message you'd like to send (body), second 
one is recipient of the message and third one is type of message to be sent. This 
defaults to `chat`.

You can find possible types in [this RFC document](https://xmpp.org/rfcs/rfc3921.html#stanzas)

`receive()` - covered in [this section](#receiving-messages-and-other-responses)

## IQ
`getRoster()` - takes no arguments and fetches current authenticated user roster. 

`setGroup()` - puts a given user in group you provide. Method takes two arguments: 
first one being the group name which you will attach to given user, and other 
being JID of that user. 

## Presence

`setPriority()` - sets priority for given resource. First argument is an integer 
`-128 <> 127`. If no second argument is given, priority will be set for currently used resource.
Other resource can be provided as a second argument whereas the priority will be set for that
specific resource. 

`subscribe()` - takes JID as an argument and asks that user for presence.

`acceptSubscription()` - takes JID as an argument and accepts presence from that user.

`declineSubscription()` - takes JID as an argument and declines presence from that user.

## Sessions

Sessions are currently being used only to differentiate logs if multiple connections
are being made. 

`XmppClient` class takes in second optional parameter `$sessionId` to which you can 
forward session ID from your system, or it will be assigned automatically. 

You can disable sessions through `Options` object (`$options->setSessionManager(false)`), 
as they can cause collision with already established sessions if being used inside 
frameworks or similar. Needless to say if this is disabled, forwarding a second parameter
to `XmppClient` will not establish a new session. 

# More options (not required)

`Options` object can take more options which may be chained but are not required. These are explained
and commented in the code directly in the `Options` class:

```
$options
    ->setProtocol($protocol)  // defaults to TCP
    ->setResource($resource)  // defaults to 'norgul_machine' string + timestamp
    ->setLogger($logger)      // logger instance (logging explained below)
    ->setAuthType($authType)  // Takes on classes which implement Authenticable
```

## Socket options
Most of the socket options are set by default so there is no need to temper
with this class, however you can additionally change the timeout for the period 
the socket will be alive when doing a `socket_read()`, and you can do that with
`$socket->setTimeout()`.

## Logging

Upon new established session the library is creating a `xmpp.log` log file in `logs/` folder:

You can manually set logger when instantiating `Options` with `setLogger($logger)`. The method accepts
any object which implements `Loggable` interface so you can create your own implementation. 
  
# Other

`Example.php` has a `sendRawXML()` method which can be helpful with debugging. Method works in a way
that you can provide hand-written XML and send it to the server. On the other hand you can 
also trigger a method by providing method name instead of XML. 

```
Enter XML: <xml>foo</xml>           <-- will send XML
Enter XML: getRoster                <-- will run getRoster() method
Enter XML: requestPresence x@x.com  <-- will run with argument requestPresence(x@x.com)
```

Some valid XMPP XML will be declined (like sending `<presence/>`) because `simplexml_load_string()` 
is not able to parse it as being a valid XML. In cases you need to do some custom stuff like 
that and you are sure it is a XMPP valid XML, you can remove the parsing line and just let the
`send()` method do its magic.
 
 **Be aware! Be very aware!** sending an invalid XML to the server
will probably invalidate currently open XML session and you will probably need to restart the 
script.

# Dev documentation
For anyone willing to contribute, a quick breakdown of the structure:

---
- `Options.php` - everything that is variable about the library
- `Socket.php` - socket related implementation (connecting, reading, writing etc.)
- `XmppClient.php` - user friendly methods to interact with the library and stanza wrapper enabling users to call stanza methods through
instantiated class. This should contain as little logic as possible, turns out it's not so easy :)
---
- `AuthTypes` - contains methods to authenticate yourself to XMPP server. Besides concrete implementations there is also an 
abstract class with minor logic to avoid duplication and interface having all the necessary methods should the need for new
auth type arise. 

- `Buffers` - implementation of buffer (or should I say a simple array) which gets filled when socket is calling the `receive()`
method, and it flushes on any read, which happens when calling `getResponse()` method for example. A brief history of why: I had 
issues when a non-recoverable error would be thrown. In this situation I had to do 2 things: try to reconnect, show the error 
to the user. The thing is that `getResponse()` returns string, and in case of reconnection the program execution would continue
returning either nothing or returning error string after the server already connected for the second time, thus misinforming the 
user of the error which occurred before reconnection. Buffer was born. 

- `Exceptions` - this is more or less a standard. I am just overriding constructors so I can get my message in.

- `Loggers` - containing logic to store logs to the `logs/xmpp.log` file. The idea was to keep several log types inside (full, simple,
no logger), but I found the one made to be sufficient. 

---

`Xml` 

- `Xml.php` - a trait consisting of way too many regex matching. This should be reformatted. 
- `Stanzas` - main logic for all stanza communication with the server. This used to be just plain XML, but I have decided to
forward a socket dependency inside so that when you call the method, you actually also send it to the server. That used to be 
something like `$this->socket->send($this->iq->getRoster())` which is correct from the programming perspective, but for the
simplicity sake, I like the `$client->iq->getRoster()` more. I'm open to other suggestions. 

---

## CI
Continuous integration is done through [Travis CI](https://travis-ci.org/), and each push goes through a process which is currently
as simple as:
- check unit tests (and god knows I have them)
- check for syntax errors
- run `phpcs` (configuration in `phpcs.xml`)
- run `phpmd` (configuration in `phpmd.xml`)

## TODO's

- **unit testing** - unfortunately I have been prolonging this for far too long, maybe there is a good soul out there who enjoys 
writing tests.
- **throttling** - when an unrecoverable error occurs (currently I am catching `<stream:error>` ones which break the stream)
reconnect is being made automatically. In case this is happening over and over again, program will try connecting indefinitely, 
which is fine to any aspect except logs which will get clogged. I would like to throttle the connection so that it increases the
connection time each time it is unsuccessful. Problem here is that I can only catch the error when getting the response, and
response can be successful on the first XML exchange (for example when you send opening stream request), while breaking on the 
second request. With this in mind my only idea was to implement throttling with timestamps or something. 
- **sessions** - I presume this part is working correctly but should be tested from a framework
- **multiple connections** - I think this part works fine, but I am worried that triggering `getRoster()` while simultaneously
fetching a message may delete one server response. If you get in one batch both roster and message, it will be added to the buffer.
Calling back the response will get either roster or message, not both. And then buffer will be flushed. This is something that
needs thinking. 
- **structure of XmppClient** - in order to enable the `$client->stanza->method` I need to instantiate all stanzas within the 
class. I feel as this could be simplified.
