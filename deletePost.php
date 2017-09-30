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
            $id = $_POST["deletePost"];
            $stmt = $mysqli->prepare("delete from comments where id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $stmt = $mysqli->prepare("delete from posts where id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
?>
</html>