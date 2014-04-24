<?php 
session_start();
ob_start();
require('chirperchat-new-connection.php');

// CHECKS TO SEE IF USER HAS FILLED OUT EITHER THE LOGIN OR REGISTRATION FORMS
if(isset($_POST['action']) && $_POST['action'] == 'register') {
	register_user($_POST); //If registration form is complete, register user
}
elseif(isset($_POST['action']) && $_POST['action'] == 'login') {
	login_user($_POST); //If login form is complete and user has an account, login user
}
elseif(isset($_POST['action']) && $_POST['action'] == 'post_message') {
	post_message($_POST); //If message is complete, post message
}
elseif(isset($_POST['action']) && $_POST['action'] == 'post_comment') {
	post_comment($_POST); //If message is complete, post message
}
elseif(isset($_POST['action']) && $_POST['action'] == 'delete_msg') {
	delete_message($_POST); //Delete logged-in user message and associated comments
}
elseif(isset($_POST['action']) && $_POST['action'] == 'delete_comment') {
	delete_comment($_POST); //Delete logged-in user comments
}
else {
	session_destroy(); // malicious navigation to process.php OR someone is trying to log off;
	header("Location: chirperchat-index.php");
	exit();
}

// FUNCTION TO REGISTER NEW USERS
function register_user($post) {
	$_SESSION['reg-errors'] = array();
	$register_query = fetch_all("SELECT email, password FROM users"); 
	foreach($register_query as $user) {
		if(($user['password'] == $post['password']) && ($user['email'] == $post['email']))
		{
			$_SESSION['reg-errors'][] = "D'oh! You already have an account. Try logging in instead!";
			header("Location: chirperchat-index.php");
		}
	}
	/// ----------------------- BEGIN VALIDATION CHECKS -------------------------//
	if(empty($post['first_name'])) //Checks that user entered first name
	{
		$_SESSION['reg-errors'][] = "Enter your first name. Your parents gave it you - use it!";
	}
	if(empty($post['last_name'])) //Checks that user entered last name
	{
		$_SESSION['reg-errors'][] = "Enter your last name. It can be fake - we won't tell.";
	}
	if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) //Makes sure a valid email address was used
	{
		$_SESSION['reg-errors'][] = "Please use a valid email. I hear gmail accounts are free.";
	}
	if(strlen($post['password']) < 5)
	{
		$_SESSION['error'][] = "Password must be greater than 5 characters";
	}
	if(empty($post['password'])) //Checks that user entered a password
	{
		$_SESSION['reg-errors'][] = "Enter a password - preferably something more complex than 'password' or '123456'.";
	}
	if(($post['password'] == '12345') || ($post['password'] == 'password')) 
	{
		$_SESSION['reg-errors'][] = "Seriously? Try a password that's a littler harder to hack.";
	}
	if($post['password'] !== $post['password_confirm']) //Checks that confirm password matches password
	{
		$_SESSION['reg-errors'][] = "Passwords much match!";
	}
	
	////----------------------- END OF VALIDATION CHECKS -------------------------//
	if (count($_SESSION['reg-errors']) > 0)
	{
		header("Location: chirperchat-index.php");
	}
	else //now you need to insert the data into the database
	{
		$first_name = escape_this_string($post['first_name']);
		$last_name = escape_this_string($post['last_name']);
		$email = escape_this_string($post['email']);
		$password = escape_this_string($post['password']);
		$user_query = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at) VALUES ('{$first_name}', '{$last_name}', '{$email}', '{$password}', NOW(), NOW())";
		$_SESSION['user_id'] = run_mysql_query($user_query);
		header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
	}
}

// FUNCTION TO LOG IN RETURNING USERS
function login_user($post) {
	$_SESSION['login-errors'] = array();
	if((empty($post['email'])) OR (empty($post['password'])))
	{
		$_SESSION['login-errors'][] = "Enter your email and password to login.";
		header("Location: chirperchat-index.php");
	}
	$login_query = "SELECT * FROM users WHERE users.password = '{$post['password']}' AND users.email = '{$post['email']}'";
	$user = fetch_all($login_query); //Go and attempt to grab user with above credential
	if(empty($user)) //If no record is found, the query returns an empty array
	{
		$_SESSION['login-errors'][] = "Cannot find a user with that email and password. Try again.";
		header("Location: chirperchat-index.php");
	}
	if (count($_SESSION['login-errors']) == 0)
	{
		$_SESSION['user_id'] = $user[0]['id'];
		$_SESSION['logged_in'] = TRUE;
		header('Location: chirperchat-message-board.php?id=' . $_SESSION['user_id']);
	}
}

// FUNCTION TO POST A MESSAGE
function post_message($post) {
	$_SESSION['msg-errors'] = array();
	if(empty($post['message'])) //Checks that user entered a message
	{
		header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
	}
	else
	{
		$escaped_quote = escape_this_string($post['message']);
		$message_query = "INSERT INTO messages (message, user_id, created_at, updated_at) VALUES ('{$escaped_quote}', '{$_SESSION['user_id']}', NOW(), NOW())";
		$_SESSION['message_id'] = run_mysql_query($message_query);
		header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
	}
}

// FUNCTION TO POST A COMMENT
function post_comment($post) {
	$_SESSION['comment-errors'] = array();
	if(empty($post['comment'])) //Checks that user entered a comment
	{
		header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
	}
	else
	{
		$escaped_quote = escape_this_string($post['comment']);
		$comment_query = "INSERT INTO comments (comment, user_id, messages_id, created_at, updated_at) VALUES ('{$escaped_quote}', '{$_SESSION['user_id']}', '{$_POST['message_id']}', NOW(), NOW())";
		// var_dump($_SESSION);
		$comment = run_mysql_query($comment_query);
		header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
	}
}

// FUNCTION TO DELETE A MESSAGE
function delete_message($post) {
	// Delete comments associated with messages first
	// No need to run a query to comments table as ON CASCADE DELETE is active on comments table
	$delete_message_query = "DELETE FROM messages where id ='{$_POST['message_id']}'";
	$delete_message = run_mysql_query($delete_message_query);
	header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);	
}

// FUNCTION TO DELETE A COMMENT
function delete_comment($post) {
	$delete_comment_query = "DELETE FROM comments WHERE id = '{$_POST['comment_id']}'";
	$delete_comment = run_mysql_query($delete_comment_query);
	header("Location: chirperchat-message-board.php?id=" . $_SESSION['user_id']);
}
?>