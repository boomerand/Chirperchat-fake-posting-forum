<?php 
	session_start();
	ob_start();
	require('chirperchat-new-connection.php');
	$user_query = fetch_record("SELECT * FROM users WHERE id =" . $_GET['id']);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ChirperChat Welcome Page</title>
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
		<h2>Welcome, <?=$user_query['first_name'] ?>!</h2>
		<p class="logout pull-right"><a href="chirperchat-logout.php">Log out</a></p>
	</div>
	<div id="content-wrapper">
<!-- BEGIN CONTENT DIV -->		
		<div id="content">
<!-- BEGIN POST MESSAGE FORM -->
			<form id="message-form" action="chirperchat-process.php" method="post">
				<input type="hidden" name="action" value="post_message">
				<div class="form-group">
				    <label for="message">Post a message</label>
				    <textarea rows="7" class="form-control" name="message" placeholder="Enter your message here..."></textarea>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-success btn-lg pull-right" value="Post message">
				</div>
			</form>
<!-- END POST MESSAGE FORM -->
<!-- RUN PHP QUERY TO RETRIEVE ALL POSTED MESSAGES -->
		<?php 
			$message_query = fetch_all("SELECT * FROM users JOIN messages ON users.id = messages.user_id ORDER BY messages.created_at DESC"); 
			//do the join here instead of query on 48
			foreach($message_query as $message) 
			{
		?>
<!-- BEGIN MESSAGE DIV -->					
				<div class="message">
					<h4 class="user-name"><?=$message['first_name'] . " " . $message['last_name'] ?><span> &nbsp;-&nbsp; <?=$message['created_at'] ?></span></h4>
					<p class="message-text"><?=$message['message']?></p>
<!-- BEGIN DELETE MESSAGE FORM -->						
		<?php 
			if($message['user_id'] == $_SESSION['user_id']) 
				{
		?>
						<form id="del-msg-form" action="chirperchat-process.php" method="post">
							<input type="hidden" name="action" value="delete_msg">
							<input type="hidden" name="message_id" value="<?=$message['id'] ?>">
							<div class="form-group" id="delete-msg-btn">
								<input type="submit" class="btn btn-primary btn-sm" value="Delete Message">
							</div>
						</form>
<!-- END DELETE MESSAGE FORM -->							
		<?php
				}
		?>
<!-- RUN PHP QUERY TO RETRIEVE ALL COMMENTS ASSOCIATED WITH EACH MESSAGE -->							
		<?php
			$comment_query = fetch_all("SELECT * FROM users JOIN comments ON users.id = comments.user_id WHERE messages_id = {$message['id']} ORDER BY comments.created_at");
			//do the join on line 58 instead of running extra query on 61
			foreach($comment_query as $comment) 
				{
		?>
						<div class="comment">
							<h4 class="comment-user-name"><?=$comment['first_name'] . " " . $comment['last_name'] ?><span> &nbsp;-&nbsp; <?=$comment['created_at'] ?></span></h4>
							<p class="comment-text"><?=$comment['comment']?></p>
<!-- BEGIN DELETE COMMENT FORM -->
		<?php
			if($comment['user_id'] == $_SESSION['user_id'])	
					{
		?>								
							<form id="delete-comment-form" action="chirperchat-process.php" method="post">
								<input type="hidden" name="action" value="delete_comment">
								<input type="hidden" name="comment_id" value="<?=$comment['id'] ?>">
								<div class="form-group" id="delete-comment-btn">
									<input type="submit" class="btn btn-primary btn-xs " value="Delete Comment">
								</div>
							</form>
		<?php
					}
		?>
<!-- END DELETE COMMENT FORM -->
						</div>
		<?php
				}
		?> 
<!-- BEGIN POST COMMENT FORM -->						
					<form id="comment-form" action="chirperchat-process.php" method="post">
						<input type="hidden" name="action" value="post_comment">
						<input type="hidden" name="message_id" value="<?=$message['id'] ?>">
						<div class="form-group">
						    <label for="comment">Post a comment</label>
						    <textarea rows="3" class="form-control" name="comment" placeholder="Enter your comment here..."></textarea>
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-success btn-sm pull-right" value="Post Comment">
						</div>
					</form>
<!-- END POST COMMENT FORM -->						
				</div>
<!-- END MESSAGE DIV -->					
		<?php
			}
		?>
		</div>
<!-- END CONTENT DIV -->
		<img id="birds-footer" src="images/birds.png" alt="birds">
		<p class="copyright">© 2014 - Rand DeCastro. This is a fictitious site meant for educational purposes only.</p>
	</div>	
</body>
</html>
