<?php
if (isset($sentcontact))
{
echo $sentcontact;
}
?>
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			
		<h1 class="ja-bottompadding">Send Us a Message!</h1>

		<form action="/contact" method="post" accept-charset="utf-8" class="form" role="form">

			<label class="sr-only" for="username">Username</label>
			<input type="text" name="username" value="<?php if (isset($_SESSION['username'])) { echo $_SESSION['username']; } ?>" class="form-control input-lg" placeholder="Username" maxlength="255">

			<label class="sr-only" for="email">Email</label>
			<input type="email" name="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>" class="form-control input-lg" placeholder="Email" required minlength="4" maxlength="255">

			<label class="sr-only" for="subject">Message Subject</label>
			<input type="text" name="subject" value="" class="form-control input-lg" placeholder="Message Subject" required minlength="1" maxlength="255">

			<label class="sr-only" for="message">Message Body</label>
			<textarea name="message" value="" class="form-control input-lg" rows="10" placeholder="Message Body" required minlength="1" maxlength="10000"></textarea>

			<div class="ja-bottompadding"></div>

			<button class="btn btn-lg btn-primary" type="submit" name="contactus">Send Message</button>

		</form>

		</div>
	</div>
</div>