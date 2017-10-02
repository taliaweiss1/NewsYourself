<!DOCTYPE html>
    <?php
    session_start();
    ?>
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
    <?php
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
            printf("<h2 id='login'> Welcome, %s </h2> <br>", htmlentities( $_SESSION["user"]));
            ?>
            <form action = "logout.php" id=  "login">
                <input type = "submit" value = "Logout"/>
            </form>
            <form action = "createPost.php" id = "login">
                <input type = "submit" value = "Create A Post"/>
            </form>
            <form action = "favorites.php" id = "login">
                <input type = "submit" value = "Show Me My Favorites"/>
            </form>
            <?php
        }
    }
    else{
        //form for loging in, takes user to set session to set the session for a registered user
        echo"<form action = 'setSession.php' method = 'POST' id='login'>";
            echo"<label for='username'>Username: </label>";
            echo"<input type = 'text' name = 'username' id='username'/>";
            echo"<label for='password'>   Password: </label>";
            echo"<input type = 'password' name = 'password' id='password'/>";
            echo"<input type='submit' value='Login' />";
        echo"</form>";
        //takes a potential new user to a create user page
        echo"<form action = 'createUser.php' id='login'>";
                echo "<input type='submit' value='Become A User' />";
        echo"</form>";
    }
    echo"<form action = 'mostPopular.php' id='login'>";
            echo "<input type='submit' value='View Popular Posts' />";
    echo"</form>";
        require 'database.php';
        $stmt = $mysqli->prepare("select id, title, username, textInPost, link from posts"); 
            //comments.commentText from posts left join comments on posts.id=comments.id");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        //check to see if username already exists 
        while($row = $result->fetch_assoc()){
                $pid = $row["id"];
                require 'database.php';
                $upVoteNum;
                $downVoteNum;
                $stmt5 = $mysqli->prepare("select count(vote) as upVotes from votes where id=$row[id] and vote='U'");
                if(!$stmt5){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt5->execute();
                $result5 = $stmt5->get_result();
                while($row5 = $result5->fetch_assoc()){
                    $upVoteNum=$row5["upVotes"];
                }   
                $stmt5->close();
                require 'database.php';
                $stmt6 = $mysqli->prepare("select count(vote) as downVotes from votes where id=$row[id] and vote='D'");
                if(!$stmt6){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt6->execute();
                $result6 = $stmt6->get_result();
                while($row6 = $result6->fetch_assoc()){
                    $downVoteNum=$row6["downVotes"];
                }   
                $stmt6->close();
                printf("<h2 id='title'> %s </h2> By: %s <br> Up Votes: %s Down Votes: %s <br> %s <br> \n",
                    htmlspecialchars( $row["title"] ),
                    htmlspecialchars( $row["username"] ),
                    htmlspecialchars($upVoteNum),
                    htmlspecialchars($downVoteNum),
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
                if(isset($_SESSION["loggedIn"])){
                    if($_SESSION["loggedIn"] == "yes"){
                        //check to see if the user already favorited the post
                        $favoritedAlready = false;
                        $upVotedAlready = false;
                        $downVotedAlready = false;
                        require 'database.php';
                        $stmt4 = $mysqli->prepare("select id, username from favorites"); 
                        if(!$stmt4){
                            printf("Query Prep Failed: %s\n", $mysqli->error);
                            exit;
                        }
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                         while($row4 = $result4->fetch_assoc()){
                            //user has already favorited the post
                            if (($row4["username"] == $_SESSION["user"]) && ($row4["id"]) == $row["id"]){
                                $favoritedAlready = true;
                            }
                         }
                        $stmt4->close();
                        if(!$favoritedAlready){
                        //if did not favorite the post, give option to favorite it 
                        echo "<form action='markFavorite.php' method='POST'>";
                            echo"<input type = 'text' name = 'markFavPostId' style = 'display:none;' value ='" . $row['id'] . "'id='markFavPostId'/>";
                            echo"<input type = 'text' name = 'markFavUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='markFavUsername'/>";
                            echo"<input class = 'submit' type='submit' value='Mark Favorite' />";
                            echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                        echo"</form>";
                        }
                        //if favorited the post tell them that they favorited the post and give option to unmark it as a favorite
                        else{
                            echo "<form action='unmarkFavorite.php' method='POST'>";
                                echo"<input type = 'text' name = 'markFavPostId' style = 'display:none;' value ='" . $row['id'] . "'id='markFavPostId'/>";
                                echo"<input type = 'text' name = 'markFavUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='markFavUsername'/>";
                                echo"<input class = 'submit' type='submit' value='UnMark Favorite' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                        echo"</form>";
                        }
                        require 'database.php';
                        //check to see if the user has upvoted alredy
                        $stmt4 = $mysqli->prepare("select id, username, vote from votes"); 
                        if(!$stmt4){
                            printf("Query Prep Failed: %s\n", $mysqli->error);
                            exit;
                        }
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        while($row4 = $result4->fetch_assoc()){
                            //user has already favorited the post
                            if (($row4["username"] == $_SESSION["user"]) && ($row4["id"]) == $row["id"]){
                                if($row4["vote"] == 'U'){
                                    $upVotedAlready=true;
                                }
                                if($row4["vote"] == 'D'){
                                    $downVotedAlready=true;
                                }
                            }
                         }
                        $stmt4->close();
                        //if already upvoted -- give option to unupvote or switch to downvote and tells the user that they downvoted this post
                        if($upVotedAlready){
                            echo "<form class = 'inline' action='unVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'unVoteID' style = 'display:none;' value ='" . $row['id'] . "'id='unVoteID'/>";
                                echo"<input type = 'text' name = 'unUpVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='unUpVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Un-Up Vote' />";
                            echo"</form>";
                            echo "<form class = 'inline' action='downVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'upToDownVotePostId' style = 'display:none;' value ='" . $row['id'] . "'id='upToDownVotePostId'/>";
                                echo"<input type = 'text' name = 'upToDownVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='upToDownVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Down Vote' />";
                                echo"<input type = 'number' name = 'upVoteToDownVote' style = 'display:none;' value = '1' id='upVoteToDownVote' />";
                            echo"</form>";
                        }
                        //if already downvoted -- give option to undownvote or switch to upvote and tells the user that they downvoted this post 
                        else if($downVotedAlready){
                            echo "<form class = 'inline' action='upVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'downToUpVotePostId' style = 'display:none;' value ='" . $row['id'] . "'id='downToUpVotePostId'/>";
                                echo"<input type = 'text' name = 'downToUpVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='downToUpVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Up Vote' />";
                                echo"<input type = 'number' name = 'downVoteToUpVote' style = 'display:none;' value = '1' id='downVoteToUpVote' />";
                            echo"</form>";
                            echo "<form class = 'inline' action='unVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'unVoteID' style = 'display:none;' value ='" . $row['id'] . "'id='unVoteID'/>";
                                echo"<input type = 'text' name = 'unDownVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='unDownVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Un-Down Vote' />";
                            echo"</form>";
                        }
                        //did not vote-- give option to vote
                        else{
                            echo "<form class = 'inline' action='upVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'upVotePostId' style = 'display:none;' value ='" . $row['id'] . "'id='upVotePostId'/>";
                                echo"<input type = 'text' name = 'upVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='upVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Up Vote' />";
                                echo"<input type = 'number' name = 'upVote' style = 'display:none;' value = '0' id='upVote' />";
                            echo"</form>";
                            echo "<form class = 'inline' action='downVote.php' method='POST'>";
                                echo"<input type = 'text' name = 'downVotePostId' style = 'display:none;' value ='" . $row['id'] . "'id='downVotePostId'/>";
                                echo"<input type = 'text' name = 'downVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "'id='downVotePostUsername'/>";
                                echo"<input class = 'submit' type='submit' value='Down Vote' />";
                                echo"<input type = 'number' name = 'downVote' style = 'display:none;' value = '0' id='downVote' />";
                            echo"</form>";
                        }
                    }
                }
                printf("<h3 id='title'> Comments: </h3>");
                while($row2 = $result2->fetch_assoc()){
                    printf("%s: %s <br>",
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
                                printf("<br>");
                            }
                        }
                    }
                }
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
                            if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
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
        printf("<br><br>\n");
        }
        $stmt->close();
    ?>
</body>
</html>