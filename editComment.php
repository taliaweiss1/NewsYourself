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
            require 'database.php';
            $id = $_POST["editComment"];
            $comment = $_POST["editCommentText"];
            $_SESSION["editCommentId"]=$id;
            if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
				die("Request forgery detected");
			}
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
                while($row = $result->fetch_assoc()){
                    $text=(string)$row["commentText"];
                    echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                                echo"<p>";
                                    echo "<textarea name = 'commentPostText' rows='10' cols='30'>" .$text."</textarea>";
                                    echo"<input type = 'text' name = 'editComment' style = 'display:none;' value ='" . $id. "'id='editComment'/>";
                                    echo"<input type = 'text' name = 'editCommentText' style = 'display:none;' value ='" . $comment. "'id='editCommentText'/>";
                                    echo "<input type='submit' value='Edit Comment' />";
                                    echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                                echo "</p>";
                            echo "</form>";
                    echo "<form action = 'homepage.php'>";
                        echo"<p>";
                            echo "<input type ='submit' value = 'Go Back To News Yourself'/>";
                            echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                        echo "</p>";
                    if(isset($_POST["commentPostText"])){
                        if(trim($_POST["commentPostText"])== ""){
                            echo "Your comment must have text!";
                        }
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
</html>