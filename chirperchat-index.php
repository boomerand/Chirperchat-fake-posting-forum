<?php  
	session_start();
	ob_start();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ChirperChat Login Page</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto:400,500,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="chirperchat-styles.css">
</head>
<body>
	<header>
		<div id="header-bar">
			<a href="chirperchat-index.php"><h1>chirper<span>chat</span></h1></a>
		</div>
	</header>
	<div id="welcome-header">
		<h2>Log in or sign up for an account</h2>
	</div>
	<div id="content-wrapper">
		<div id="content">
			
			<!-- BEGIN LOGIN FORM -->
			<form id="login" action="chirperchat-process.php" method="post">
				<h3>Returning Users</h3>
				<?php 
					if(isset($_SESSION['login-errors'])) {
						foreach($_SESSION['login-errors'] as $name => $message) {
						?>
						<p class="errors"><?=$message ?></p>
						<?php
						unset($_SESSION['login-errors']);
						}
					}
				?>
				<input type="hidden" name="action" value="login">
				<div class="form-group">
				    <label for="email" id="email">Email</label>
				    <input type="text" class="form-control" name="email" placeholder="Enter email address">
				</div>
				<div class="form-group">
				    <label for="password" id="password">Password</label>
				    <input type="password" class="form-control" name="password" placeholder="Enter password">
				</div>
				<div class="form-group">
					<input type="submit" id="submit" class="btn btn-primary btn-lg" value="Login">
				</div>
			</form>
			<!-- END LOGIN FORM -->

			<!-- BEGIN REGISTRATION FORM -->	
			<form id="registration" action="chirperchat-process.php" method="post">
				<h3>New Users</h3>
				<?php 
					if(isset($_SESSION['reg-errors'])) {
						foreach($_SESSION['reg-errors'] as $name => $message) {
						?>
						<p class="errors"><?=$message ?></p>
						<?php
						unset($_SESSION['reg-errors']);
						}
					}
				?>
				<input type="hidden" name="action" value="register">
				<div class="form-group">
				    <label for="first_name" id="first_name">First Name</label>
				    <input type="text" class="form-control" name="first_name" placeholder="Enter First Name">
				</div>
				<div class="form-group">
				    <label for="last_name" id="last_name">Last Name</label>
				    <input type="text" class="form-control" name="last_name" placeholder="Enter Last Name">
				</div>
				<div class="form-group">
				    <label for="email" id="email">email</label>
				    <input type="text" class="form-control" name="email" placeholder="Enter Email">
				</div>
				<div class="form-group">
				    <label for="password" id="password">Password</label>
				    <input type="password" class="form-control" name="password" placeholder="Enter Password">
				</div>
				<div class="form-group">
				    <label for="passord_confirm" id="passord_confirm">Confirm Password</label>
				    <input type="password" class="form-control" name="password_confirm" placeholder="Confirm Password">
				</div>
				<div class="form-group">
					<input type="submit" id="register" class="btn btn-success btn-lg" value="Register">
				</div>
			</form>
			<!-- END REGISTRATION FORM -->	

		</div>
		<img id="birds-footer" src="images/birds.png" alt="birds">
		<p class="copyright">© 2014 - Rand DeCastro. This is a fictitious site meant for educational purposes only.</p>
	</div>	
</body>
</html>