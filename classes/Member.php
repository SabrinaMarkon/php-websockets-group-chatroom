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

    public function getAllMembers() {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from members order by username";
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

        # error checking. - do with ajax so it looks cool ?
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
            $sql = "insert into members (username,password,accounttype,firstname,lastname,email,signupdate,signupip) values (?,?,?,?,?,?,NOW(),?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($username,$password,$accounttype,$firstname,$lastname,$email,$signupip));
            Database::disconnect();

            $subject = "Welcome to " . $settings['sitename'] . "!";
            $message = "Our Login URL: " . $settings['domain'] . "\nUsername: " . $username . "\nPassword: " . $password . "\n\n";
            $sendsiteemail = new Email();
            $send = $sendsiteemail->sendEmail($email, $settings['adminemail'], $subject, $message, $settings['sitename'], $settings['domain'], $settings['adminemail'], '');

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

    public function saveMember($id) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $signupip = $_POST['signupip'];
        $verified = $_POST['verified'];
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "update `members` set username=?, password=?, firstname=?, lastname=?, email=?, signupip=?, verified=? where id=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($username, $password, $firstname, $lastname, $email, $signupip, $verified, $id));

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

    public function getGravatar($username,$email) {

        $emailhash = trim($email);
        $emailhash = md5($emailhash);
        $gravatarimagelg = "<img src=\"http://gravatar.com/avatar/" . $emailhash . "?s=130\" alt=\"" . $username . "\" class=\"avatar img-circle gravatar-sm\">";
        return $gravatarimagelg;

    }
}