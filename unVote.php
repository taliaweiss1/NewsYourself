<!DOCTYPE html>
    <?php
		session_start();
    ?>
<html>
<head>
	<title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    <?php
        require 'database.php';
		//delete the vote of the user for specified post
		$stmt = $mysqli->prepare("delete from votes where username=? and id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('si', $_SESSION["user"], $_POST["unVoteID"]);
		$stmt->execute();
		$stmt->close();
		//back to homepage
        header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
    ?>
</body>
</html>