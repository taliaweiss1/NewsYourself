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
		printf("<h1> News Yourself </h1> <br> <h2> Edit Post </h2>");
        require 'database.php';
        $id = $_POST["editPost"];
        $_SESSION["editPostId"]=$id;
        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
            die("Request forgery detected");
		}
		//if the user is authenticated using tokens, get the text of the post they wish to edit
        $stmt = $mysqli->prepare("select textInPost from posts where id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $text;
        while($row = $result->fetch_assoc()){
            $text=htmlspecialchars($row["textInPost"]);
			//create a form to submit the new post. The text area to submit the new post will include the old text for usability. 
            echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                echo "<textarea name = 'postText' rows='10' cols='30'>" .$text."</textarea>";
                echo"<input type = 'text' name = 'editPost' style = 'display:none;' value ='" . $id. "' id='editPost'/>";
                echo "<input type='submit' value='Edit Post' />";
                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
            echo "</form>";
			//option to go back to homepage
            echo "<form action = 'homepage.php'>";
                echo "<input type ='submit' value = 'Go Back To News Yourself'/>";
			echo"</form>";
            if(isset($_POST["postText"])){
				//check to see that the new post isn't empty. If it is they are told that their story must have to have text.
		        if(trim($_POST["postText"])== ""){
                    echo "Your story must have text!";
                }
				//if story has text
                else{
                    $id = $_SESSION["editPostId"];
                    $text = (string)$_POST["postText"];
                    require 'database.php';
                    if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
                        die("Request forgery detected");
                    }
					//update the story 
                    $stmt = $mysqli->prepare("update posts set textInPost=? where id=?");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('si', $text, $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
				}
			}
        }
        $stmt->close();
	?>
</body>
</html>