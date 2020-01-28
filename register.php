<?php
if (isset($showregistration))
{
echo $showregistration;
$showcontent = new PageContent();
echo $showcontent->showPage('success');
$Layout = new Layout();
$Layout->showFooter();
exit;
}
$showcontent = new PageContent();
echo $showcontent->showPage('register');
?>

<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">

		<h1 class="ja-bottompadding">Sign Up</h1>

			<form action="/register" method="post" accept-charset="utf-8" class="form" role="form">
			
						<div class="row">
							<div class="col-xs-6 col-md-6">
								<label class="sr-only" for="firstname">First Name</label>
								<input type="text" name="firstname" value="" class="form-control input-lg" placeholder="First Name" required minlength="1" maxlength="255">
							</div>
							<div class="col-xs-6 col-md-6">
								<label class="sr-only" for="lastname">Last Name</label>
								<input type="text" name="lastname" value="" class="form-control input-lg" placeholder="Last Name" required minlength="1" maxlength="255">
							</div>
						</div>
				
						<label class="sr-only" for="email">Your Email</label>
						<input type="email" name="email" value="" class="form-control input-lg" placeholder="Your Email" required minlength="4" maxlength="255">

						<label class="sr-only" for="username">Username</label>
						<input type="text" name="username" value="" class="form-control input-lg" placeholder="Username" required minlength="4" maxlength="255">

						<label class="sr-only" for="password">Password</label>
						<input type="password" name="password" value="" class="form-control input-lg" placeholder="Password" required minlength="6" maxlength="255">

						<label class="sr-only" for="confirm_password">Confirm Password</label>
						<input type="password" name="confirm_password" value="" class="form-control input-lg" placeholder="Confirm Password" required minlength="6" maxlength="255">

						<span class="help-block ja-white">By clicking Create My Account, you agree to our <a href="#" data-toggle="modal" data-target="#termsModal">Terms</a></span>
						
						<div class="ja-bottompadding"></div>

						<button class="btn btn-lg btn-primary" type="submit" name="register">Create My Account</button>

			</form>

			<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModal" aria-hidden="true">
				<div class="modal-dialog ja-modal-width">
					<div class="modal-content ja-modal">
						<div class="modal-body">

						<?php
						$terms = new PageContent();
						$showterms = $terms->showPage('terms');
						echo $showterms;
						?>

						</div>
						<div class="modal-footer">
							<button class="btn btn-lg btn-primary" type="button" data-dismiss="modal" aria-hidden="true">Close</button>
						</div>
					</div>
				</div>
			</div>

			<div class="ja-bottompadding"></div>
 
		</div>
	</div>
</div>
