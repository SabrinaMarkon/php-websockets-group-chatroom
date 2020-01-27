<?php
/*
 * CheckWebSocketsService checks to make sure the websockets daemon is running, in case it failed.
 *
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, PHPSiteScripts.com
 * @license LICENSE.md
 * 
 */
require_once('../config/Database.php');
require_once('../config/Settings.php');
require_once('../classes/Email.php');

class CheckWebSocketsService {

  public function checkDaemon() {
    
  }
}

$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
foreach ($settings as $key => $value)
{
    $$key = $value;
}
$checkwebsockets = new CheckWebSocketsService();
$checkwebsockets->checkDaemon();

