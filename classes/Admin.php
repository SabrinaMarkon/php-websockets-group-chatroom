<?php
/**
 * Handles admin access to the admin area of the application.
 * PHP 5
 * @author Sabrina Markon
 * @copyright 2017 Sabrina Markon, PHPSiteScripts.com
 * @license LICENSE.md
 **/
class Admin
{

    public function adminLogin($username,$password) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from adminsettings where adminuser=? and adminpass=? limit 1";
        $q = $pdo->prepare($sql);
        $q->execute(array($username,$password));
        $valid = $q->rowCount();
        if($valid > 0)
        {
            # successful login.
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $admindetails = $q->fetch();
            return $admindetails;
        }
        else
        {
            # incorrect login.
            return false;
        }
        Database::disconnect();

    }

    public function forgotLogin($sitename,$domain,$adminemail) {

        $usernameoremail = $_POST['usernameoremail'];
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from adminsettings where adminuser=? or adminemail=? limit 1";
        $q = $pdo->prepare($sql);
        $q->execute(array($usernameoremail,$usernameoremail));
        $found = $q->rowCount();
        if ($found > 0)
        {
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $data = $q->fetch();
            $adminemail = $data['adminemail'];
            $adminuser = $data['adminuser'];
            $adminpass = $data['adminpass'];
            $subject = "Your " . $sitename . " Admin Details";
            $message = "Login URL: " . $domain . "/admin\nUsername: " . $adminuser . "\nPassword: " . $adminpass . "\n\n";

            $sendsiteemail = new Email();
            $send = $sendsiteemail->sendEmail($adminemail,$adminemail,$subject,$message,$sitename,$domain,$adminemail, '');

            return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your login details were sent to your email address.</strong></div>";
        }
        else
        {
            Database::disconnect();
            return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>The username or email address you entered was not found.</strong></div>";
        }

    }

    public function adminLogout() {

        session_unset();

    }

}