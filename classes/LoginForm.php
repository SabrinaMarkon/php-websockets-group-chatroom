<?php
/**
 Member login form.
 PHP 5
 @author Sabrina Markon
 @copyright 2017 Sabrina Markon, PHPSiteScripts.com
 @license README-LICENSE.txt
 **/
class LoginForm
{
	public $loginerror;
	public $showloginerror;
	public $content;

	public function showLoginForm($loginerror, $isadmin) {

		$showloginerror = "";
		if ($loginerror == 1)
		{
		$showloginerror = "<div class=\"alert alert-danger\"><strong>Incorrect Login</strong></div>";
		}
		$showverificationlink = "";
		if ($isadmin == 0) {
			$showverificationlink = "<span class=\"help-block\"><a href=\"/resend\">Resend Verification Email</a></span>";
		}

$content = <<<HEREDOC
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				
			<h1 class="ja-bottompadding">Login</h1>

			<form action="main" method="post" accept-charset="utf-8" class="form" role="form">

				$showloginerror

				<label class="sr-only" for="username">Username</label>
				<input type="text" name="username" value="" class="form-control input-lg" 
				placeholder="Username" required minlength="4" maxlength="255" autocomplete="username">

				<label class="sr-only" for="password">Password</label>
				<input type="password" name="password" value="" class="form-control input-lg" 
				placeholder="Password" required minlength="6" maxlength="255" autocomplete="current-password">

				<span class="help-block"><a href="forgot">Forgot Password?</a></span>
				
				$showverificationlink

				<button class="btn btn-lg btn-primary" type="submit" name="login">Login</button>

			</form>

			<div class="ja-bottompadding"></div>

			</div>
		</div>
	</div>
HEREDOC;

		return $content;
	}
}
