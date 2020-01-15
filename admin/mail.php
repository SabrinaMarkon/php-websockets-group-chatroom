<?php
include "control.php";
if (isset($showupdate))
{
    echo $showupdate;
}
$allsavedmails = new Mail();
$savedmails = $allsavedmails->getAllSavedMails();
?>

<!-- tinyMCE -->
<script language="javascript" type="text/javascript" src="/../js/tinymce/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">
    tinymce.init({
        setup : function(ed) {
            ed.on('init', function() {
                this.getDoc().body.style.fontSize = '22px';
                this.getDoc().body.style.fontFamily = 'Calibri';
                this.getDoc().body.style.backgroundColor = '#ffffff';
            });
        },
        selector: 'textarea',  // change this value according to your HTML
        body_id: 'elm1=message',
        height: 600,
        theme: 'modern',
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
        image_advtab: true,
        templates: [
            { title: 'Test template 1', content: 'Test 1' },
            { title: 'Test template 2', content: 'Test 2' }
        ],
        content_css: [
//            '/../css/bootstrap.min.css',
//            '/../css/bootstrap-theme.min.css',
//            '/../css/custom.css'
        ]
    });
</script>
<!-- /tinyMCE -->

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <h1 class="ja-bottompadding">Email Members</h1>

            <form action="/admin/mail" method="post" accept-charset="utf-8" class="form" role="form">
                <div class="form-group textfield">
                    <div class="row">
                        <div class="col-sm-3"><label for="id">Edit Saved Mail:</label></div>
                        <div class="col-sm-6">
                            <select name="id" class="form-control">
                                <option value="" disabled selected>Select saved mail to edit</option>
                                <?php
                                foreach($savedmails as $savedmail)
                                    if (isset($showeditmail) && $showeditmail !== '') {
                                        if ($savedmail['id'] === $showeditmail['id']) {
                                            echo "<option value='" . $savedmail['id'] . "' selected>" . $savedmail['subject'] . "</option>";
                                        } else {
                                            echo "<option value='" . $savedmail['id'] . "'>" . $savedmail['subject'] . "</option>";
                                        }
                                    } else {
                                        echo "<option value='" . $savedmail['id'] . "'>" . $savedmail['subject'] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input type="hidden" name="_method" value="POST">
                            <button class="btn btn-md btn-primary pull-left" type="submit" name="editmail" style="margin-right:10px;">EDIT</button>
                            <?php
                            if (isset($showeditmail) && $showeditmail !== '') {
                                ?>
                                <button class="btn btn-md btn-primary pull-left" type="button" name="showallmails"
                                        onclick="parent.location = '/admin/mail'">RETURN
                                </button>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10 text-center"><br />
                    <p>Please use the personalization substitution below anywhere in your subject or message, typed EXACTLY as shown (cAsE sEnSiTiVe):</p><br />
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered">
                            <tbody>
                                <tr><td><strong>Type This:</strong></td><td><strong>To Substitute This:</strong></td></tr>
                                <tr><td>~USERNAME~</td><td>Member's Username</td></tr>
                                <tr><td>~FULLNAME~</td><td>Member's  First and Last Name</td></tr>
                                <tr><td>~FIRSTNAME~</td><td>Member's First Name</td></tr>
                                <tr><td>~LASTNAME~</td><td>Member's Last Name</td></tr>
                                <tr><td>~EMAIL~</td><td>Member's Email Address</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-1"></div>
            </div><br />

            <?php
            if (isset($showeditmail) && $showeditmail !== '') {

            // EDIT EXISTING MAIL:
            ?>
            <form action="/admin/mail/<?php echo $showeditmail['id']; ?>" method="post" accept-charset="utf-8" class="form" role="form">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="sr-only" for="name">Email Subject</label>
                            <input type="text" name="subject" placeholder="Email Subject" class="form-control" value="<?php echo $showeditmail['subject']; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="sr-only" for="htmlcode">Email Message</label>
                            <textarea name="message" id="message" placeholder="Email Message" class="form-control" rows="30"><?php echo $showeditmail['message']; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">

                        <div class="col-sm-3"></div>
                        <div class="col-sm-2">
                            <button class="btn btn-lg btn-primary" type="button" name="showallmail" onclick="parent.location = '/admin/mail'">RETURN</button>
                        </div>
                        <div class="col-sm-2">
                            <input type="hidden" name="_method" value="PATCH">
                            <button class="btn btn-lg btn-primary" type="submit" name="savemail">SAVE</button>
                        </div>
                        <div class="col-sm-1">
                            <button class="btn btn-lg btn-primary" type="submit" name="sendmail">SEND</button>
                        </div>
            </form>
                        <div class="col-sm-2">
                            <form action="/admin/mail/<?php echo $showeditmail['id']; ?>" method="post" accept-charset="utf-8" class="form" role="form">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-lg btn-primary" type="submit" name="deletemail">DELETE</button>
                            </form>
                        </div>
                        <div class="col-sm-3"></div>

                    </div>
                </div>
                <?php

                } else {

                // CREATE NEW MAIL:
                ?>
                <form action="/admin/mail" method="post" accept-charset="utf-8" class="form" role="form">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="sr-only" for="name">Email Subject</label>
                                <input type="text" name="subject" placeholder="Email Subject" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="sr-only" for="htmlcode">Email Message</label>
                                <textarea name="message" id="message" placeholder="Email Message" class="form-control" rows="30"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">

                            <div class="col-sm-5"></div>
                            <div class="col-sm-1">
                                <input type="hidden" name="_method" value="POST">
                                <button class="btn btn-lg btn-primary" type="submit" name="addmail">SAVE</button>
                            </div>
                            <div class="col-sm-1">
                                <button class="btn btn-lg btn-primary" type="submit" name="sendmail">SEND</button>
                            </div>
                            <div class="col-sm-5"></div>

                        </div>
                    </div>
                </form>
                    <?php
                    }
                    ?>

                    <div class="ja-bottompadding"></div>

        </div>
    </div>
</div>