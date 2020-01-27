<?php
class Email
{
	private $headers;
	private $subject;
	private $message;
	private $toemail;
	private $fromemail;
	private $htmlheader;

	public function sendEmail($toemail, $fromemail, $subject, $message, $settings, $htmlheader) {
		
	$headers = "From: " . $settings['sitename'] . "<" . $settings['domainemail'] . ">\n";
	$headers .= "Reply-To: <" . $fromemail . ">\n";
	$headers .= "X-Sender: <" . $settings['domainemail'] . ">\n";
	$headers .= "X-Mailer: PHP\n";
	$headers .= "X-Priority: 3\n";
	$headers .= "Return-Path: <" . $settings['domainemail'] . ">\n";
	
	$headers .= $htmlheader;

	@mail($toemail, $subject, wordwrap(stripslashes($message)), $headers, "-f " . $settings['domainemail']);

	}

}