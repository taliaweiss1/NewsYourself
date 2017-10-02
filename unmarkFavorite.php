    <?php
		session_start();
        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
			die("Request forgery detected");
		}
		//if no forgery, delete the favorite for this user
        require 'database.php';
		$stmt = $mysqli->prepare("delete from favorites where username=? and id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('si', $_SESSION["user"], $_POST["markUnFavPostId"]);
		$stmt->execute();
		$stmt->close();
		//back to homepage
        header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
    ?>