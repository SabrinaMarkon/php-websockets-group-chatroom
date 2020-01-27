<?php
class Contact
{
	private $username;
	private $email;
	private $subject;
	private $message;

	public function sendContact($settings) {

	$username = $_POST['username'];
	$email = $_POST['email'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];

	if (!empty($username))
		{
		$message .= "\n\nSent by Username: " . $username . "\n\n";
		}

	$sendsiteemail = new Email();
	$send = $sendsiteemail->sendEmail($settings['adminemail'], $email, $subject, $message, $settings, '');

	return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your Message was Sent!</strong></div>";
	
	}
}