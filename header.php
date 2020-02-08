<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Collectors Cave</title>
<link href="https://fonts.googleapis.com/css?family=Creepster|Roboto&display=swap" rel="stylesheet">
<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script src="https://kit.fontawesome.com/b5d058e525.js" crossorigin="anonymous"></script>
<link href="/css/custom.css" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="//code.jquery.com/jquery.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/navigation.js"></script>
</head>
<body>

<div class="container text-center">
		
	<header id="ja-heading">
		<div class="overlay">
			<h2 id="ja-heading-welcome">welcome to</h2>
			<h1 id="ja-heading-title">Collectors Cave!</h1>
		</div>
		<video id="videoBG" autoplay muted loop poster="/images/fireworks.jpg">
			<source src="/images/fireworks.mp4" type="video/mp4">
		</video>
	</header>

	<div class="btn-group btn-group-justified ja-navgroup" role="group" aria-label="Main Navigation Menu" id="navbar">

		<div class="ja-desktopnav">
			<?php
			if (isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 'no')
			{
			?>
			<a href="/main" type="button" class="btn ja-navbutton ja-navbutton-first" role="button">MAIN</a>
			<a href="/profile" type="button" class="btn ja-navbutton" role="button">PROFILE</a>
			<a href="/chatroom" type="button" class="btn ja-navbutton" role="button">JOIN CHAT!</a>
			<a href="/contact" type="button" class="btn ja-navbutton" role="button">CONTACT</a>
			<a href="/logout" type="button" class="btn ja-navbutton" role="button">LOGOUT</a>
			<?php
			}
			else
			{
			?>
			<a href="/" type="button" class="btn ja-navbutton ja-navbutton-first" role="button">HOME</a>
			<a href="/login" type="button" class="btn ja-navbutton" role="button">LOGIN</a>
			<a href="/register" type="button" class="btn ja-navbutton" role="button">REGISTER</a>
			<a href="/aboutus" type="button" class="btn ja-navbutton" role="button">ABOUT</a>
			<a href="/contact" type="button" class="btn ja-navbutton" role="button">CONTACT</a>
			<?php
			}
			?>
		</div>

		<div class="hamburger" onclick="toggleMenu(this)">
			<div class="bar1"></div>
			<div class="bar2"></div>
			<div class="bar3"></div>
		</div>

		<div class="navmobile">
			<ul>
				<?php
				if ((isset($_SESSION['username'])) && (isset($_SESSION['password']))) {
					?>
					<li class="list-item navmobilelink"><a href="/main">MAIN</a></li>
					<li class="list-item navmobilelink"><a href="/profile">PROFILE</a></li>
					<li class="list-item navmobilelink"><a href="/chatroom">CHAT!</a></li>
					<li class="list-item navmobilelink"><a href="/contact">CONTACT</a></li>
					<li class="list-item navmobilelink"><a href="/logout">LOGOUT</a></li>
					<?php
				}
				else {
					?>
					<li class="list-item navmobilelink"><a href="/">HOME</a></li>
					<li class="list-item navmobilelink"><a href="/login">LOGIN</a></li>
					<li class="list-item navmobilelink"><a href="/register">REGISTER</a></li>
					<li class="list-item navmobilelink"><a href="/aboutus">ABOUT</a></li>
					<li class="list-item navmobilelink"><a href="/contact">CONTACT</a></li>
					<?php
				}
				?>
			</ul>
		</div>

	</div>

	<div class="ja-content">
