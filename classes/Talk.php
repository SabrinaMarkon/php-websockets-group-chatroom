<?php
/**
Handles Forum Posting.
PHP 5
@author Sabrina Markon
@copyright 2017 Sabrina Markon, PHPSiteScripts.com
@license LICENSE.md
 **/
class Talk
{
    private $username;
    private $subject;
    private $message;

    function getAllPosts($postid) {

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from talk where parent_id='$postid' order by id";
        $q = $pdo->prepare($sql);
        $q->execute();
        $children = $q->rowCount();
        if ($children > 0) {
            if ($postid === 0) {
                echo '<ul class="root_post" id="top_root_post">';
            } else {
                echo '<ul class="root_post">';
            }
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $posts = $q->fetchAll();
            foreach ($posts as $post) {

                $sql = "select * from talk where parent_id='" . $post['id'] . "' order by id";
                $q = $pdo->prepare($sql);
                $q->execute();
                $badge = $q->rowCount();
                if ($badge > 0) {
                    $chevron = "<i id=\"chevron" . $post['id'] . "\" class=\"fa fa-chevron-down\"></i>";
                } else {
                    $chevron = "<i class=\"fa fa-chevron-down hidechevron\"></i>";
                }
                //echo "<li id=\"" . $post['id'] . "\">" . $chevron . "  " . $post['subject']. "  <span class=\"whoandwhen\">" . $post['username'] . " Posted " . $post['dateadded'] . "</span>";
                echo "<li id=\"" . $post['id'] . "\">";
                ?>
<div class="media">
    <div class="media-left media-top"><?php echo $chevron ?>
        <?php
        $esql = "select * from members where username=?";
        $eq = $pdo->prepare($esql);
        $eq->execute(array($post['username']));
        $useremail = $eq->fetch();
        $userfound = $eq->rowCount();
        if ($userfound > 0) {
            $email = $useremail['email'];
            $emailhash = trim($email);
            $emailhash = md5($emailhash);
            echo "<img src=\"http://gravatar.com/avatar/" . $emailhash . "?s=130\" alt=\"" . $post['username'] . "\" class=\"avatar img-circle gravatar-sm media-object\">";
        }
        ?>
    </div>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $post['subject']; ?></h4>
<!--        <p>--><?php //echo $post['message']; ?><!--</p>-->
    </div>
</div>
<?php
                if ($badge > 0) {
                    echo  " <span class=\"badge\">" . $badge . "</span>";
                    $this->getAllPosts($post['id']);
                }
                echo "</li>";
            }
            echo "</ul>";
        }
        Database::disconnect();
    }

    function addPost($username,$email,$neworreply) {


        ///##################### Need to pass the neworreply and the parentid

        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $parent_id = $_POST['parent_id'];

        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "insert into talk (parent_id, username, email, subject, message, dateadded) values (?, ?, ?, ?, ?, NOW())";
        $q = $pdo->prepare($sql);
        $q->execute(array($parent_id, $username, $email, $subject, $message));

        if ($parent_id === 0) {
            $show = 'Topic';
        } else {
            $show = 'Reply';
        }

        Database::disconnect();

        return "<center><div class=\"alert alert-success\" style=\"width:75%;\"><strong>Your " . $show . " Was Added!</strong></div>";

    }

    function editPost($id) {

    }

    function deletePost($id) {

    }


}