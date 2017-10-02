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
        //if the user has up voted already we will update their vote in the votes table
        if(isset($_POST['downVoteToUpVote'])){
            if($_POST['downVoteToUpVote'] == 1){
                require 'database.php';
                $stmt = $mysqli->prepare("update votes set username = ?, id = ?, vote = 'U' where username =? and id=?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('sisi', $_SESSION["user"], $_POST["downToUpVotePostId"], $_SESSION["user"], $_POST["downToUpVotePostId"]);
                $stmt->execute();
                $stmt->close();
                header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
            }
        }
        //if the user has not voted yet, we will insert their vote into votes 
        if(isset($_POST['upVote'])){
            if($_POST['upVote'] == 0){
                require 'database.php';
                $stmt = $mysqli->prepare("insert into votes (username, id, vote) values (?, ?, 'U')");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('si', $_SESSION["user"], $_POST["upVotePostId"]);
                $stmt->execute();
                $stmt->close();
                header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
            }
        }
    ?>
</body>
</html>