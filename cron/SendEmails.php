<?php
/*
 * SendEmails handles admin or member submitted mail outs and should be called as a scheduled job.
 *
 * @author Sabrina Markon
 * @copyright 2017 Sabrina Markon, PHPSiteScripts.com
 * @license LICENSE.md
 *
 * @param $domain The main url of the website.
 * @param $sitename The name of the website
 * @param $adminemail The website admin's support email address.
 * @param @adminname The name of the website admin.
 */
require_once('.../config/Database.php');
require_once('.../config/Settings.php');
require_once('.../classes/Email.php');

class SendEmails
{
    private $email;
    private $subject;
    private $message;
    private $headers;
    private $pdo;

    public function getMails($settings)
    {
        // get all mails that are marked as pending mailout.
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from mail where needtosend=1 order by id";
        $q = $pdo->prepare($sql);
        $q->execute();
        $q->setFetchMode(PDO::FETCH_ASSOC);
        $sendmails = $q->fetchAll();
        if ($sendmails) {
            foreach ($sendmails as $sendmail) {

                $id = $sendmail['id'];
                $senderuserid = $sendmail['username'];
                $subject = $sendmail['subject'];
                $message = $sendmail['message'];
                $save = $sendmail['save'];

                $sent = new Datetime();
                $sent = $sent->format('Y-m-d');

                $getsql = "select * from members where verified='yes' order by id";
                $getq = $pdo->prepare($getsql);
                $getq->execute();
                $getq->setFetchMode(PDO::FETCH_ASSOC);
                $members = $getq->fetchAll();
                if ($members) {
                    foreach ($members as $member) {
                        $username = $member['username'];
                        $firstname = $member['firstname'];
                        $lastname = $member['lastname'];
                        $email = $member['email'];
                        $fullname = $firstname . ' ' . $lastname;

                        // message disclaimer:
                        $disclaimer = "--------------------------------------------------------------<br><br>";
                        $disclaimer .= "This is a message from " . $settings['sitename'] . ". You are receiving this because you are a double opted-in member of " . $settings['sitename'] . " with username " . $username . "<br><br>";
                        $disclaimer .= "You can opt out of receiving all emails from this website by logging in and deleting your account here:<br><br><a href=\"" . $settings['domain'] . "/login\">" . $settings['domain'] . "/login</a><br><br>";
                        $disclaimer .= "Kindly allow up to 24 hours to stop receiving mail once you delete your account.<br><br>";
                        $disclaimer .= "Thank you,<br>" . $settings['adminname'] . "<br>" . $settings['sitename'] . "<br><br><br>";
                        $disclaimer .= "Live Removal Assistance or Questions: <a href=\"mailto:" . $settings['adminemail'] .  "\">" . $settings['adminemail'] . "</a><br><br>";
                        $disclaimer .= "This email is sent in strict compliance with International spam laws.<br><br>";

                        // full message and subject with disclaimer as well as this member's substitution:
                        $html = $message . "<br><br><br>" . $disclaimer;
                        $html = str_replace("~USERID~", $username, $html);
                        $html = str_replace("~FULLNAME~", $fullname, $html);
                        $html = str_replace("~FIRSTNAME~", $firstname, $html);
                        $html = str_replace("~LASTNAME~", $lastname, $html);
                        $html = str_replace("~EMAIL~", $email, $html);
                        $html = $html . "<br><br>Sent by: " . $senderuserid;
                        $subject = str_replace("~USERID~", $username, $subject);
                        $subject = str_replace("~FULLNAME~", $fullname, $subject);
                        $subject = str_replace("~FIRSTNAME~", $firstname, $subject);
                        $subject = str_replace("~LASTNAME~", $lastname, $subject);
                        $subject = str_replace("~EMAIL~", $email, $subject);

                        $htmlheader = "Content-Type: text/html; charset=windows-1252\n";

                        $sendsiteemail = new Email();
                        $sendsiteemail->sendEmail($email, $adminemail, $subject, $html, $settings, $htmlheader);
                    }
                }

                if ($save === '0') {
                    // delete the mail record if it is not one the user saved:
                    $deletesql = "delete from mail where id=?";
                    $deleteq = $pdo->prepare($deletesql);
                    $deleteq->execute(array($id));
                } else {
                    // update the mail record to show it has been sent:
                    $updatesql = "update mail set needtosend=0, sent=? where id=?";
                    $updateq = $pdo->prepare($updatesql);
                    $updateq->execute(array($sent, $id));
                }
            }
        }
        Database::disconnect();
    }
}

$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
$mail = new SendEmails();
$mail->getMails($settings);
