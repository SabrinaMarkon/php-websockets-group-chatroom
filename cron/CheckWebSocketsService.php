<?php
/*
 * CheckWebSocketsService checks to make sure the websockets daemon is running, in case it failed
 * and was not restarted automatically by systemd.
 * 
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, PHPSiteScripts.com
 * @license LICENSE.md
 * 
 */
class CheckWebSocketsService {

  public function checkDaemon() {
    $output = `systemctl status chat-server`;
    $checkifdead = stripos($output, 'dead');
    $checkifinactive = stripos($output, 'inactive');
    $checkifkilled = stripos($output, 'killed');
    if ($checkifdead || $checkifinactive || $checkifkilled) {
      // The chat-server.service isn't running, so start it.
      $startup = `systemctl start chat-server`;
    }
  }
}

$checkwebsockets = new CheckWebSocketsService();
$checkwebsockets->checkDaemon();

