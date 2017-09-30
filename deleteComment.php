<!DOCTYPE html>
<html>
<head>
    <title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<?php
session_start();
require 'database.php';
            $id = $_POST["deleteComment"];
            $text = $_POST["deleteCommentText"];
			if(!hash_equals($_SESSION['token'], $_POST['token'])){
				die("Request forgery detected");
			}
			else{
				$stmt = $mysqli->prepare("delete from comments where id=? and commentText=?");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->bind_param('is', $id, $text);
				$stmt->execute();
				$stmt->close();
				header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
			}
?>
</html>