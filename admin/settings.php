<?php
include "control.php";
if (isset($showupdate))
{
    echo $showupdate;
}
$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
foreach ($settings as $key => $value)
{
    $$key = $value;
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <h1 class="ja-bottompadding">Site Settings</h1>

            <form method="post" accept-charset="utf-8" class="form" role="form">

                <label class="sr-only" for="adminuser">Your Website Name:</label>
                <input type="text" name="adminuser" value="<?php echo $adminuser ?>" class="form-control input-lg" placeholder="Admin Username">

                <label class="sr-only" for="adminpass">Admin Password</label>
                <input type="password" name="adminpass" value="<?php echo $adminpass ?>" class="form-control input-lg" placeholder="Admin Password">

                <label class="sr-only" for="confirm_adminpass">Confirm Password</label>
                <input type="password" name="confirm_adminpass" value="<?php echo $adminpass ?>" class="form-control input-lg" placeholder="Confirm Password">

                <label class="sr-only" for="adminemail">Your Admin Email</label>
                <input type="text" name="adminemail" value="<?php echo $adminemail ?>" class="form-control input-lg" placeholder="Admin Email">

                <label class="sr-only" for="adminemail">Your PayPal Email</label>
                <input type="text" name="adminpaypal" value="<?php echo $adminpaypal ?>" class="form-control input-lg" placeholder="PayPal Email">

                <label class="sr-only" for="sitename">Your Website Name:</label>
                <input type="text" name="sitename" value="<?php echo $sitename ?>" class="form-control input-lg" placeholder="Website Name">

                <label class="sr-only" for="domain">Your Domain:</label>
                <input type="text" name="domain" value="<?php echo $domain ?>" class="form-control input-lg" placeholder="Website URL (start with http://)">

                <div class="ja-bottompadding"></div>

                <button class="btn btn-lg btn-primary" type="submit" name="savesettings">Save Settings</button>

            </form>

            <div class="ja-bottompadding"></div>

        </div>
    </div>
</div>