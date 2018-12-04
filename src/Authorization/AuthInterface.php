<?php
/**
 * Created by PhpStorm.
 * User: mdupor
 * Date: 04/12/2018
 * Time: 12:03
 */

namespace Norgul\Xmpp\Authorization;


interface AuthInterface
{
    /**
     * Based on auth type, return the right format of credentials to be sent to the server
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function encodedCredentials($username, $password);
}