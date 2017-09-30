<!DOCTYPE html>
<html>
<head>
	<title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
    <h1>
         News Yourself
    </h1>
    <!--form for loging in, takes user to set session to set the session for a registered user-->
    <form action = "setSession.php" method = "POST">
    <p>
         <label for="username">Username: </label>
         <input type = "text" name = "username" id="username"/>
<!--         <input type="hidden" name="token" value="/// echo $_SESSION['token'];?>" />
-->    </p>
	<p>
		 <label for="password">Password: </label>
         <input type = "password" name = "password" id="password"/>
		 <input type="submit" value="Login" />
	</p>
    </form>
    <!--takes a potential new user to a create user page -->
    <form action = "createUser.php" method = "POST">
    <p>
		<input type="submit" value="Become A User" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	</p>
    </form>
    <?php
    session_start();
    require 'database.php';
        $stmt = $mysqli->prepare("select username from users");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $users = array();
        while($row = $result->fetch_assoc()){
           array_push($users, $row["username"]);
        }
    if (isset($_SESSION["loggedIn"])){
        if($_SESSION["loggedIn"] == "yes"){
            printf("Welcome, %s <br>", htmlentities( $_SESSION["user"]));
            printf("<br>" . $_SESSION["token"] . "<br>");
            ?>
            <form action = "logout.php">
                <input type = "submit" value = "Logout"/>
            </form>
            <form action = "createPost.php">
                <input type = "submit" value = "Create A Post"/>
            </form>
            <?php
        }
    }
    require 'database.php';
        $stmt = $mysqli->prepare("select id, title, username, textInPost, link from posts"); 
            //comments.commentText from posts left join comments on posts.id=comments.id");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        echo "<ul>\n";
        //check to see if username already exists 
        while($row = $result->fetch_assoc()){
                $pid = $row["id"];
                printf("\t <h2 id='title'> %s </h2> By: %s <br> %s <br> \n",
                    htmlspecialchars( $row["title"] ),
                    htmlspecialchars( $row["username"] ),
                    htmlspecialchars( $row["textInPost"] ));
                if($row["link"] != ""){
                    printf("Associated Link: %s",
                    htmlspecialchars( $row["link"] ));
                }
                $stmt2 = $mysqli->prepare("select id, username, commentText from comments where id=$row[id]"); 
                if(!$stmt2){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                printf("<h3> Comments: </h3>");
                while($row2 = $result2->fetch_assoc()){
                    printf("<br>%s: %s",
                    htmlspecialchars($row2["username"]),
                    htmlspecialchars($row2["commentText"]));
                    if(isset($_SESSION["loggedIn"])){
                        if($_SESSION["loggedIn"] == "yes"){
                            if($_SESSION["user"] == $row2["username"]){
                                echo "<form class = 'inline' action='editComment.php' method='POST'>";
                                        echo"<input type = 'text' name = 'editComment' style = 'display:none;' value ='" . $row2['id'] . "'id='editComment'/>";
                                        echo"<input type = 'text' name = 'editCommentText' style = 'display:none;' value ='" . $row2['commentText'] . "'id='editCommentText'/>";
                                        echo"<input class = 'submit' type='submit' value='Edit' />";
                                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                                echo"</form>";
                                echo "<form class = 'inline' action='deleteComment.php' method='POST'>";
                                        echo"<input type = 'text' name = 'deleteComment' style = 'display:none;' value ='" . $row2['id'] . "'id='deleteComment'/>";
                                        echo"<input type = 'text' name = 'deleteCommentText' style = 'display:none;' value ='" . $row2['commentText'] . "'id='deleteCommentText'/>";
                                        echo"<input class = 'submit' type='submit' value='Delete' />";
                                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                                echo"</form>";
                            }
                        }
                    }
                }
                 echo "</ul>\n";
                 $stmt2->close();
                if(isset($_SESSION["loggedIn"])){
                    if($_SESSION["loggedIn"] == "yes"){
                        echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                            echo"<p>";
                                echo "<textarea name = 'comment' rows='2' cols='30'></textarea>";
                                echo "<input type = 'text' name = 'postNum' style = 'display:none;' value ='" . $row['id'] . "' id='postNum'/>";
                                echo "<input type='submit' value='Post Comment' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                            echo "</p>";
                        echo "</form>";
        
                        if(isset($_POST["comment"])){
                            require 'database.php';
                            //use id from post to insert comment
                            $comment = (string)$_POST['comment'];
                            $user = (string)$_SESSION["user"];
                            $id = (int)$_POST["postNum"];
                            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                                die("Request forgery detected");
                            }
                            else{
                                $stmt3 = $mysqli->prepare("insert into comments (username, id, commentText) values (?, ?, ?)");
                                if(!$stmt3){
                                    printf("Query Prep Failed: %s\n", $mysqli->error);
                                    exit;
                                }
                                $stmt3->bind_param('sis', $user, $id, $comment);
                                $stmt3->execute();
                                $stmt3->close();
                            }
                        }
                    }
                }
        if(isset($_SESSION["loggedIn"])){
            if($_SESSION["loggedIn"] == "yes"){
                if($_SESSION["user"] == $row["username"]){
                    //$_SESSION["editPostId"]=$row['id'];
                    echo "<form class = 'inline' action='editPost.php' method='POST'>";
                                echo"<input type = 'text' name = 'editPost' style = 'display:none;' value ='" . $row['id'] . "'id='editPost'/>";
                                echo"<input type='submit' value='Edit Post' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                    echo"</form>";
                    echo "<form class = 'inline' action='deletePost.php' method='POST'>";
                            echo"<input type = 'text' name = 'deletePost' style = 'display:none;' value ='" . $row['id'] . "'id='deletePost'/>";
                            echo"<input class = 'submit' type='submit' value='Delete Post' />";
                            echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                     echo"</form>";
            }
            }
        }
        printf("\t<br><br>\n");
        }
        echo "</ul>\n";
        $stmt->close();
    ?>
</body>
</html>