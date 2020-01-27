<?php
class Email
{
	private $headers;
	private $subject;
	private $message;
	private $toemail;
	private $fromemail;

	public function sendEmail($toemail, $fromemail, $subject, $message, $sitename, $adminemail, $htmlheader) {
		
	$headers = "From: " . $sitename . "<" . $fromemail . ">\n";
	$headers .= "Reply-To: <" . $adminemail . ">\n";
	$headers .= "X-Sender: <" . $adminemail . ">\n";
	$headers .= "X-Mailer: PHP5\n";
	$headers .= "X-Priority: 3\n";
	$headers .= "Return-Path: <" . $adminemail . ">\n";
	
	$headers .= $htmlheader;

	@mail($toemail, $subject, wordwrap(stripslashes($message)), $headers, "-f $fromemail");

	}

}