<!DOCTYPE html>
<html>
<head>
    <title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
	<?php
		session_start();
		require 'database.php';
		$id = $_POST["deletePost"];
		if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
			die("Request forgery detected");
		}
		//if user is authenticated using tokens, delete all of the comments associated with a post 
		else{
			$stmt = $mysqli->prepare("delete from comments where id=?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->close();
			//after deleting all of the comments associated with a post, delete the post
			$stmt = $mysqli->prepare("delete from posts where id=?");
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->close();
			header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
		}
	?>
</body>
</html>