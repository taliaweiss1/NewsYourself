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
            $id = $_POST["editPost"];
            $_SESSION["editPostId"]=$id;
            if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
                die("Request forgery detected");
			}
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
                $text=(string)$row["textInPost"];
                echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                            echo"<p>";
                                echo "<textarea name = 'postText' rows='10' cols='30'>" .$text."</textarea>";
                                echo"<input type = 'text' name = 'editPost' style = 'display:none;' value ='" . $id. "'id='editPost'/>";
                                echo "<input type='submit' value='Edit Post' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                            echo "</p>";
                        echo "</form>";
                echo "<form action = 'homepage.php'>";
                    echo"<p>";
                        echo "<input type ='submit' value = 'Go Back To News Yourself'/>";
                    echo "</p>";
                if(isset($_POST["postText"])){
                    if(trim($_POST["postText"])== ""){
                        echo "Your story must have text!";
                    }
                    else{
                        $id = $_SESSION["editPostId"];
                        $text = $_POST["postText"];
                        require 'database.php';
                        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
                            die("Request forgery detected");
                        }
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
                ?>
                <?php
                }
                $stmt->close();
?>