<?php
/**
 *Handles admin adding, updating, or deleting pages.
 *PHP 5
 *@author Sabrina Markon
 *@copyright 2017 Sabrina Markon, PHPSiteScripts.com
 *@license README-LICENSE.txt
 **/
class Page
{
    private $pdo;

    public function getAllPages() {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from pages order by name";
        $q = $pdo->prepare($sql);
        $q->execute();
        $q->setFetchMode(PDO::FETCH_ASSOC);
        $pages = $q->fetchAll();
        $pagearray = array();
        foreach ($pages as $page) {
            array_push($pagearray, $page);
        }
        return $pagearray;

    }

    public function editPage($id) {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "select * from pages where id=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $editpage = $q->fetch();
        $found = $q->rowCount();
        Database::disconnect();
        if ($found > 0) {
            return $editpage;
        }
    }

    public function addPage($domain) {

        $name = $_POST['name'];
        $htmlcode = $_POST['htmlcode'];
        $slug = $_POST['slug'];
        if (empty($name) || empty($slug) || empty($htmlcode)) {
            return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>Error: You need complete all fields.</strong></div>";
        }
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "insert into `pages` set name=?, htmlcode=?, slug=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($name, $htmlcode, $slug));
        Database::disconnect();
        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>New Page " . $name . " was Added!</strong><br>New URL: <a href=" . $domain . "/" . $slug . " target=\"_blank\">" . $domain . "/" . $slug . "</a></div>";

    }

    public function savePage($id) {

        $name = $_POST['name'];
        $htmlcode = $_POST['htmlcode'];
        $slug = $_POST['slug'];
        if (empty($name) || empty($slug) || empty($htmlcode)) {
            return "<center><div class=\"alert alert-danger\" style=\"width:75%;\"><strong>Error: You need complete all fields.</strong></div>";
        }
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "update `pages` set name=?, htmlcode=?, slug=? where id=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($name, $htmlcode, $id, $slug));
        Database::disconnect();
        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Website Content for Page " . $name . " was Saved!</strong></div>";

    }

    public function deletePage($id) {

        $name = $_POST['name'];
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $sql = "delete from pages where id=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Website Content for Page " . $name . " was Deleted</strong></div>";

    }
}