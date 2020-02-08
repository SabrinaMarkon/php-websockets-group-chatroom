<?php
if (isset($showresend))
{
echo $showresend;
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">

		<h1 class="ja-bottompadding">Resend Verification Email</h1>

		<form action="/resend" method="post" accept-charset="utf-8" class="form" role="form">

			<label class="sr-only" for="usernameoremail">Your Username or Email Address</label>
			<input type="text" name="usernameoremail" value="" class="form-control input-lg" 
			placeholder="Your Username or Email Address" required minlength="4" maxlength="255" autocomplete="email">

			<div class="ja-bottompadding"></div>

			<button class="btn btn-lg btn-primary" type="submit" name="resendverify">Send</button>

		</form>

		<div class="ja-bottompadding"></div>

		</div>
	</div>
</div>
