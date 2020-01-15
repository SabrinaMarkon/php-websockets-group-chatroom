<?php
/**
 *Handles admin logout
 *PHP 5
 *@author Sabrina Markon
 *@copyright 2017 Sabrina Markon, PHPSiteScripts.com
 *@license README-LICENSE.txt
 **/
class Logout
{
    function __construct () {
        session_unset();
    }
}