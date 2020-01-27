<?php
/**
Handles user interactions with the application.
PHP 5
@author Sabrina Markon
@copyright 2017 Sabrina Markon, PHPSiteScripts.com
@license LICENSE.md
**/
class User
{
	private $pdo;
	private $username;
	private $password;
	private $email;
	private $emailhash;
	private $gravatarimagelg;
	private $usernameoremail;

	public function newSignup($settings) {

		$username = $_POST['username'];
		$password = $_POST['password'];
		$accounttype = 'Member';
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$signupip = $_SERVER['REMOTE_ADDR'];

		# make sure fields filled in. Make sure email is valid. Make sure passwords match.
		# make sure fields > x chars.
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from members where username=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($username));
		$q->setFetchMode(PDO::FETCH_ASSOC);
		$data = $q->fetch();
		if ($data['username'] == $username)
		{
			Database::disconnect();
			return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The username you chose isn't available.</strong></div>";
		}
		else
		{
			$verifiedcode = uniqid(); // just a random temporary string for the verification code.
			$sql = "insert into members (username,password,accounttype,firstname,lastname,email,signupdate,signupip,verifiedcode) values (?,?,?,?,?,?,NOW(),?,?)";
			$q = $pdo->prepare($sql);
			$q->execute(array($username,$password,$accounttype,$firstname,$lastname,$email,$signupip,$verifiedcode));
			Database::disconnect();
			$subject = "Welcome to " . $settings['sitename'] . "! Please verify your email!";
			$message = "Our Login URL: " . $settings['domain'] . "\nUsername: " . $username . "\nPassword: " . $password . "\n\n";
			$message .= "Please verify your email address by clicking here: " . $settings['domain'] . "/verify/" . $verifiedcode . "\n\n";
			$sendsiteemail = new Email();
			$send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings, '');

			return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Success! Thanks for Joining!</strong></div>";

			$username = null;
			$password = null;
			$accounttype = null;
			$firstname = null;
			$lastname = null;
			$email = null;
			$signupip = null;
		}

	}

	public function userVerification() {
		$verifiedcode = $_GET['code'];
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from members where verifiedcode=? limit 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($verifiedcode));
		$found = $q->rowCount();
		if($found > 0)
			{
			# successful email verification.
			$q->setFetchMode(PDO::FETCH_ASSOC);
			$memberdetails = $q->fetch();
			# update verification date & time.
			$sql = "update members set verifiedcode=NULL, verified='yes', verifieddate=NOW() where verifiedcode=?";
			$q = $pdo->prepare($sql);
			$q->execute(array($verifiedcode));
			return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your email was successfully verified!<br />
				You can now <a href=\"/login\">login</a>!</strong></div>";
			}
		else
			{
				# Invalid verification code.
				return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The validation code " . $verifiedcode . " was not found in the system.</strong></div>";
			}
		Database::disconnect();
	}

	public function userLogin($username,$password) {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from members where username=? and password=? and verified='yes' limit 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($username,$password));
		$valid = $q->rowCount();
		if($valid > 0)
			{
			# successful login.
			$q->setFetchMode(PDO::FETCH_ASSOC);
			$memberdetails = $q->fetch();
			# update last login date & time.
			$sql = "update members set lastlogin=NOW() where username=? and password=?";
			$q = $pdo->prepare($sql);
			$q->execute(array($username,$password));
			# This session SHOULD count for showing members area nav.
			$_SESSION['isadmin'] = 'no';
			# return the member fields.
			return $memberdetails;
			}
		else
			{
			# incorrect login.
			return false;
			}
		Database::disconnect();

	}

	public function resendVerificationEmail($settings) {
		$usernameoremail = $_POST['usernameoremail'];
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from members where username=? or email=? limit 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($usernameoremail,$usernameoremail));
		$found = $q->rowCount();
		if ($found > 0)
			{
			$q->setFetchMode(PDO::FETCH_ASSOC);
			$data = $q->fetch();
			$email = $data['email'];
			$verifiedcode = uniqid();
			# resend validation email.
			$subject = "Please re-verify your email!";
			$message = "Please verify your email address by clicking here: " . $settings['domain'] . "/verify/" . $verifiedcode . "\n\n";
			$sendsiteemail = new Email();
			$send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings, '');
			$sql = "update members set verified=?, verifiedcode=? where email=?";
			$q = $pdo->prepare($sql);
			$q->execute(array('no', $verifiedcode, $email));
			Database::disconnect();
			return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your verification email was resent to your email address.</strong></div>";
			}
		else
			{
			Database::disconnect();
			return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The username or email address you entered was not found.</strong></div>";
			}
	}
	
	public function forgotLogin($settings) {

		$usernameoremail = $_POST['usernameoremail'];
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "select * from members where username=? or email=? limit 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($usernameoremail,$usernameoremail));
		$found = $q->rowCount();
		if ($found > 0)
			{
			$q->setFetchMode(PDO::FETCH_ASSOC);
			$data = $q->fetch();
			$email = $data['email'];
			$username = $data['username'];
			$password = $data['password'];
			$subject = "Your " . $settings['sitename'] . " Login Details";
			$message = "Login URL: " . $settings['domain'] . "\nUsername: " . $username . "\nPassword: " . $password . "\n\n";
			
			$sendsiteemail = new Email();
			$send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings, '');
		
			return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your login details were sent to your email address.</strong></div>";
			}
		else
			{
			Database::disconnect();
			return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The username or email address you entered was not found.</strong></div>";
			}

	}

	public function getGravatar($username,$email) {

		$emailhash = trim($email);
		$emailhash = md5($emailhash);
		$gravatarimagelg = "<img src=\"https://gravatar.com/avatar/" . $emailhash . "?s=130\" alt=\"" . $username . "\" class=\"avatar img-circle img-thumbnail gravatar-lg\">";
		return $gravatarimagelg;
		
	}

	public function saveProfile($username, $settings) {

		$password = $_POST['password'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$oldemail = $_POST['oldemail'];
		$signupip = $_SERVER['REMOTE_ADDR'];

		# make sure fields filled in. Make sure email is valid. Make sure passwords match.
		# make sure fields > x chars.
		$verified = 'yes';
		$verifiedcode = null;
		if ($email !== $oldemail) {
			$verified = 'no';
			$verifiedcode = uniqid();
			# resend validation email.
			$subject = "Please re-verify your email!";
			$message = "Please verify your email address by clicking here: " . $settings['domain'] . "/verify/" . $verifiedcode . "\n\n";
			$sendsiteemail = new Email();
			$send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings, '');
		}
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "update members set password=?, firstname=?, lastname=?, email=?, signupip=?, verified=?, verifiedcode=? where username=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($password, $firstname, $lastname, $email, $signupip, $verified, $verifiedcode, $username));
		Database::disconnect();
		$_SESSION['password'] = $password;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['email'] = $email;
		$_SESSION['signupip'] = $signupip;

		return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your Account Details Were Saved!<br /> 
		If you changed your email address you will need to re-verify.</strong></div>";

	}

	public function updateChatLoginStatus($username, $loginstatus) {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "update members set login_status=? where username=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($loginstatus,$username));	
	}

	public function userLogout() {
		session_unset();
	}

	public function deleteUser($username) {
		
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$sql = "delete from members where username=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($username));
		Database::disconnect();
		return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Account " . $username . " Was Deleted</strong></div>";

	}

}
