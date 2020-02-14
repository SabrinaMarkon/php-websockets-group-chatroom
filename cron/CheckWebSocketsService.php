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
    $checkifstopped = stripos($output, 'stopped');
    if ($checkifdead || $checkifinactive || $checkifkilled || $checkifstopped) {
      // The chat-server.service isn't running, so start it.
      $startup = `systemctl start chat-server`; // DOESN'T WORK because of permissions yet (we don't want php running as root).
    }
    echo $output;
  }
}

$checkwebsockets = new CheckWebSocketsService();
$checkwebsockets->checkDaemon();

