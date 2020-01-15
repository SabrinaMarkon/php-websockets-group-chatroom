<?php
include "control.php";
if (isset($showupdate))
{
echo $showupdate;
}
$getposts = new Talk();

$showcontent = new PageContent();
echo $showcontent->showPage('Members Area Talk Page');
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <h1 class="ja-bottompadding">Create New Topic</h1>

            <form action="/talk" method="post" accept-charset="utf-8" class="form" role="form">

                <label class="sr-only" for="subject">Your Subject</label>
                <input type="text" name="subject" value="" class="form-control input-lg" placeholder="Subject">

                <label class="sr-only" for="message">Your Message</label>
                <textarea name="message" value="" class="form-control input-lg" rows="10" placeholder="Message"></textarea>

                <div class="ja-bottompadding"></div>

                <input type="hidden" name="neworreply" value="new">
                <button class="btn btn-lg btn-primary" type="submit" name="talk">Add Topic</button>

            </form>

        </div>
    </div>

    <div class="ja-bottompadding"></div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <h1 class="ja-bottompadding">Posts</h1>

            <div>
                <?php
                $allposts = $getposts->getAllPosts(0);
                ?>
            </div>

        </div>
    </div>

</div>

