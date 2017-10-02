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
        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
			die("Request forgery detected");
		}
		//if no forgery, update favorite table with the user who wishes to favorite and the post id of the post
        require 'database.php';
		$stmt = $mysqli->prepare("insert into favorites (username, id) values (?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('si', $_SESSION["user"], $_POST["markFavPostId"]);
		$stmt->execute();
		$stmt->close();
        header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
    ?>
</body>
</html>