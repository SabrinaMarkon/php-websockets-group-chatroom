<?php
/**
Handles admin adding, updating, or deleting members.
PHP 5
@author Sabrina Markon
@copyright 2017 Sabrina Markon, PHPSiteScripts.com
@license README-LICENSE.txt
 **/
class Member
{
    private $pdo;

    public function getAllMembers($orderby) {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from members order by $orderby";
        $q = $pdo->prepare($sql);
        $q->execute();
        $q->setFetchMode(PDO::FETCH_ASSOC);
        $members = $q->fetchAll();
        $memberarray = array();
        foreach ($members as $member) {
            array_push($memberarray, $member);
        }
//        print_r($memberarray);
//        exit;
        return $memberarray;

    }

    public function addMember($settings) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $accounttype = 'Member';
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $signupip = $_SERVER['REMOTE_ADDR'];

        # error checking.
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
            return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The username you chose already exists.</strong></div>";
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

            return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>New Member " . $username . " was Added!</strong></div>";

            $username = null;
            $password = null;
            $accounttype = null;
            $firstname = null;
            $lastname = null;
            $email = null;
            $signupip = null;
        }
    }

    public function saveMember($id, $settings) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $signupip = $_POST['signupip'];
        $verified = $_POST['verified'];
        $oldverified = $_POST['oldverified'];
        if ($verified == 'yes' && $oldverified == 'no') {
            $verifiedcode = null;
            $verifieddate = date("Y-m-d H:i:s");
            $sql = "update `members` set username=?, password=?, firstname=?, lastname=?, email=?, signupip=?, verified=?, verifiedcode=?, verifieddate=? where id=?";
        } 
        elseif ($verified == 'no' && $oldverified == 'yes') {
            $verifiedcode = uniqid();
            $verifieddate = null;
            $this->resendMember($id, $verifiedcode, $settings); 
            $sql = "update `members` set username=?, password=?, firstname=?, lastname=?, email=?, signupip=?, verified=?, verifiedcode=?, verifieddate=? where id=?";
        }
        else {
            $sql = "update `members` set username=?, password=?, firstname=?, lastname=?, email=?, signupip=? where id=?";
        }
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare($sql);
        $q->execute(array($username, $password, $firstname, $lastname, $email, $signupip, $verified, $verifiedcode, $verifieddate, $id));

//        if (!$q->execute(array($id, $username, $password, $firstname, $lastname, $email, $signupip, $verified))) {
//            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
//        }
 //     echo $q->rowCount();

        Database::disconnect();
        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Member " . $username . " was Saved!</strong></div>";

    }

    public function deleteMember($id) {

        $username = $_POST['username'];
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "delete from members where id=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Member " . $username . " was Deleted</strong></div>";

    }

    public function resendMember($id, $validationcode, $settings) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from members where id=? limit 1";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $found = $q->rowCount();
        if ($found > 0) {
			$q->setFetchMode(PDO::FETCH_ASSOC);
            $data = $q->fetch();
            $username = $data['username'];
            $email = $data['email'];
            $verifiedcode = uniqid();
            # If we already have a validation code from a member profile update.
            if ($validationcode) {
                $verifiedcode = $validationcode;
            } 
            # resend validation email.
            $subject = "Please re-verify your email!";
            $message = "Please verify your email address by clicking here: " . $settings['domain'] . "/verify/" . $verifiedcode . "\n\n";
            $sendsiteemail = new Email();
            $send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings, '');
            $sql = "update members set verified=?, verifiedcode=? where email=?";
            $q = $pdo->prepare($sql);
            $q->execute(array('no', $verifiedcode, $email));
            Database::disconnect();
            return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Resent Verification Email to username " . $username . "!</strong></div>";
        }
        else {
			Database::disconnect();
			return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>This member was not found.</strong></div>";
        }
    }

    public function getGravatar($username,$email) {

        $emailhash = trim($email);
        $emailhash = md5($emailhash);
        $gravatarimagelg = "<img src=\"https://gravatar.com/avatar/" . $emailhash . "?s=130\" alt=\"" . $username . "\" class=\"avatar img-circle gravatar-sm\">";
        return $gravatarimagelg;

    }
}