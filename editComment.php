<!DOCTYPE html>
<html lang = "en">
<head>	
    <title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
	<?php
		session_start();
		printf("<h1> News Yourself </h1> <br> <h2> Edit Comment </h2>");
		require 'database.php';
        $id = (int)$_POST["editComment"];
        $comment = (string)$_POST["editCommentText"];
        $_SESSION["editCommentId"]=$id;
        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
			die("Request forgery detected");
		}
		//if user is authenticated using tokens, we get the comment that they with to edit
        else{
            $stmt = $mysqli->prepare("select commentText from comments where id=? and commentText=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('is', $id, $comment);
            $stmt->execute();
            $result = $stmt->get_result();
            $text;
			//create a form to submit the updated comment. The comment text area will include the old comment for usability. 
            while($row = $result->fetch_assoc()){
                $text=(string)htmlspecialchars($row["commentText"]);
                echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                    echo"<p>";
						echo "<textarea name = 'commentPostText' rows='10' cols='30'>" .$text."</textarea>";
						echo"<input type = 'text' name = 'editComment' style = 'display:none;' value ='" . $id. "' id='editComment'/>";
                        echo"<input type = 'text' name = 'editCommentText' style = 'display:none;' value ='" . $comment. "' id='editCommentText'/>";
                        echo "<input type='submit' value='Edit Comment' />";
                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                    echo "</p>";
                echo "</form>";
				//option to go back to the homepage
                echo "<form action = 'homepage.php'>";
                    echo"<p>";
                        echo "<input type ='submit' value = 'Go Back To News Yourself'/>";
                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
				    echo "</p>";
				echo "</form>";
                if(isset($_POST["commentPostText"])){
					//if the user made their comment empty, we tell them that the comment must have text 
                    if(trim($_POST["commentPostText"])== ""){
                        echo "Your comment must have text!";
                    }
					//if the user did not make their comment empty, we update the comment
					else{
                        $id = $_SESSION["editCommentId"];
                        $text = $_POST["commentPostText"];
                        require 'database.php';
                        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
                            die("Request forgery detected");
                        }
                        else{
                            $stmt2 = $mysqli->prepare("update comments set commentText=? where id=? and commentText=?");
                            if(!$stmt2){
                                printf("Query Prep Failed: %s\n", $mysqli->error);
                                exit;
                             }
                            $stmt2->bind_param('sis', $text, $id, $comment);
                            $stmt2->execute();
                            $result = $stmt2->get_result();
                            $stmt2->close();
                            header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
                        }
                    }
                }
            }
            $stmt->close();
        }
    ?>
</body>
</html>