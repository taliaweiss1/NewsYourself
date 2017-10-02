<!DOCTYPE html>
    <?php
    session_start();
    ?>
<html lang = "en">
<head>
	<title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    <h1>
         News Yourself
    </h1>
    <?php
    if (isset($_SESSION["loggedIn"])){
        //if someone is logged in, it gives them the option to logout, create a post, and show their favorite posts
        if($_SESSION["loggedIn"] == "yes"){
            printf("<h2 class='log'> Welcome, %s </h2> <br>", htmlentities( $_SESSION["user"]));
            ?>
            <form class='log' action = "logout.php">
                <input type = "submit" value = "Logout"/>
            </form>
            <form class='log' action = "createPost.php">
                <input type = "submit" value = "Create A Post"/>
            </form>
            <form class='log' action = "favorites.php">
                <input type = "submit" value = "Show Me My Favorites"/>
            </form>
            <?php
        }
    }
    else{
        //form for loging in, takes user to set session to set the session for a registered user
        echo"<form action = 'setSession.php' method = 'POST' class='log'>";
            echo"<label for='username'>Username: </label>";
            echo"<input type = 'text' name = 'username' id='username'/>";
            echo"<label for='password'>   Password: </label>";
            echo"<input type = 'password' name = 'password' id='password'/>";
            echo"<input type='submit' value='Login' />";
        echo"</form>";
        //takes a potential new user to a create user page
        echo"<form action = 'createUser.php' class='log'>";
                echo "<input type='submit' value='Become A User' />";
        echo"</form>";
    }
    //form that shows a button to any visitor to view most popular posts
    echo"<form action = 'mostPopular.php' class='log'>";
            echo "<input type='submit' value='View Popular Posts' />";
    echo"</form>";
    require 'database.php';
    //get info for each post
    $stmt = $mysqli->prepare("select id, title, username, textInPost, link from posts"); 
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $pid = htmlspecialchars($row["id"]);
        require 'database.php';
        $upVoteNum;
        $downVoteNum;
        $idPost = htmlspecialchars($row['id']);
        //query to get number of upvotes for a post
        $stmt5 = $mysqli->prepare("select count(vote) as upVotes from votes where id=$idPost and vote='U'");
        if(!$stmt5){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt5->execute();
        $result5 = $stmt5->get_result();
        while($row5 = $result5->fetch_assoc()){
            //save up vote number
            $upVoteNum=htmlspecialchars($row5["upVotes"]);
        }   
        $stmt5->close();
        require 'database.php';
        //get number of down votes for a post
        $stmt6 = $mysqli->prepare("select count(vote) as downVotes from votes where id=$idPost and vote='D'");
        if(!$stmt6){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt6->execute();
        $result6 = $stmt6->get_result();
        while($row6 = $result6->fetch_assoc()){
            //save down vote number
            $downVoteNum=htmlspecialchars($row6["downVotes"]);
        }   
        $stmt6->close();
        //display post info, including up and down votes
        printf("<h2 class='title'> %s </h2> By: %s <br> Up Votes: %s Down Votes: %s <br> %s <br> \n",
            htmlspecialchars( $row["title"] ),
            htmlspecialchars( $row["username"] ),
            htmlspecialchars($upVoteNum),
            htmlspecialchars($downVoteNum),
            htmlspecialchars( $row["textInPost"] ));
        //if there is a link
        if($row["link"] != ""){
            printf("Associated Link: %s",
            htmlspecialchars( $row["link"] ));
        }
        //get comments
        $stmt2 = $mysqli->prepare("select id, username, commentText from comments where id=$idPost"); 
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
                    if ((htmlspecialchars($row4["username"]) == $_SESSION["user"]) && (htmlspecialchars($row4["id"])) == htmlspecialchars($row["id"])){
                        $favoritedAlready = true;
                    }
                 }
                $stmt4->close();
                if(!$favoritedAlready){
                    //if did not favorite the post, give option to favorite it 
                    echo "<form action='markFavorite.php' method='POST'>";
                        echo"<input type = 'text' name = 'markFavPostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='markFavPostId'/>";
                        echo"<input class = 'submit' type='submit' value='Mark Favorite' />";
                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                    echo"</form>";
                }
                //if favorited the post give option to unmark it as a favorite
                else{
                    echo "<form action='unmarkFavorite.php' method='POST'>";
                        echo"<input type = 'text' name = 'markUnFavPostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='markUnFavPostId'/>";
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
                    if ((htmlspecialchars($row4["username"]) == $_SESSION["user"]) && (htmlspecialchars($row4["id"])) == htmlspecialchars($row["id"])){
                        //user has upvoted the post already
                        if(htmlspecialchars($row4["vote"]) == 'U'){
                            $upVotedAlready=true;
                        }
                        //user has downvoted the post already
                        if(htmlspecialchars($row4["vote"]) == 'D'){
                            $downVotedAlready=true;
                        }
                    }
                 }
                $stmt4->close();
                //if already upvoted -- give option to unupvote or switch to downvote
                if($upVotedAlready){
                    //un-up vote option (performs the query in unVote.php)
                    echo "<form class = 'inline' action='unVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'unVoteID' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='unVoteID'/>";
                        echo"<input type = 'text' name = 'unUpVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='unUpVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Un-Up Vote' />";
                    echo"</form>";
                    //switch to downVote option (performs the query in downVote.php)
                    echo "<form class = 'inline' action='downVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'upToDownVotePostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='upToDownVotePostId'/>";
                        echo"<input type = 'text' name = 'upToDownVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='upToDownVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Down Vote' />";
                        echo"<input type = 'number' name = 'upVoteToDownVote' style = 'display:none;' value = '1' class='upVoteToDownVote' />";
                    echo"</form>";
                }
                //if already downvoted -- give option to undownvote or switch to upvote 
                else if($downVotedAlready){
                    //switch to up vote option (performs query in upVote.php)
                    echo "<form class = 'inline' action='upVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'downToUpVotePostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='downToUpVotePostId'/>";
                        echo"<input type = 'text' name = 'downToUpVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='downToUpVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Up Vote' />";
                        echo"<input type = 'number' name = 'downVoteToUpVote' style = 'display:none;' value = '1' class='downVoteToUpVote' />";
                    echo"</form>";
                    //un-down vote option (performs the query in unVote.php)
                    echo "<form class = 'inline' action='unVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'unVoteID' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "'class='unVoteID'/>";
                        echo"<input type = 'text' name = 'unDownVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='unDownVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Un-Down Vote' />";
                    echo"</form>";
                }
                //did not vote-- give option to vote
                else{
                    //up vote option
                    echo "<form class = 'inline' action='upVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'upVotePostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='upVotePostId'/>";
                        echo"<input type = 'text' name = 'upVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='upVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Up Vote' />";
                        echo"<input type = 'number' name = 'upVote' style = 'display:none;' value = '0' class='upVote' />";
                    echo"</form>";
                    //down vote option
                    echo "<form class = 'inline' action='downVote.php' method='POST'>";
                        echo"<input type = 'text' name = 'downVotePostId' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='downVotePostId'/>";
                        echo"<input type = 'text' name = 'downVotePostUsername' style = 'display:none;' value ='" . $_SESSION['user'] . "' class='downVotePostUsername'/>";
                        echo"<input class = 'submit' type='submit' value='Down Vote' />";
                        echo"<input type = 'number' name = 'downVote' style = 'display:none;' value = '0' class='downVote' />";
                    echo"</form>";
                }
            }
        }
        //prints the comments
        printf("<h3 class='title'> Comments: </h3>");
        while($row2 = $result2->fetch_assoc()){
            printf("%s: %s <br>",
            htmlspecialchars($row2["username"]),
            htmlspecialchars($row2["commentText"]));
            if(isset($_SESSION["loggedIn"])){
                if($_SESSION["loggedIn"] == "yes"){
                    //if one of the comments is by the logged in user, give option to edit and delete their comment
                    if($_SESSION["user"] == htmlspecialchars($row2["username"])){
                        $text=htmlspecialchars_decode($row2['commentText']);
                        //edit their comment
                        echo "<form class = 'inline' action='editComment.php' method='POST'>";
                                echo"<input type = 'text' name = 'editComment' style = 'display:none;' value ='" . htmlspecialchars($row2['id']) . "' class='editComment'/>";
                                echo"<input type = 'text' name = 'editCommentText' style = 'display:none;' value = '$text' class='editCommentText'/>";
                                echo"<input class = 'submit' type='submit' value='Edit' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                        echo"</form>";
                        //delete their comment
                        echo "<form class = 'inline' action='deleteComment.php' method='POST'>";
                                echo"<input type = 'text' name = 'deleteComment' style = 'display:none;' value ='" . htmlspecialchars($row2['id']) . "' class='deleteComment'/>";
                                echo"<input type = 'text' name = 'deleteCommentText' style = 'display:none;' value ='" . htmlspecialchars($row2['commentText']) . "' class='deleteCommentText'/>";
                                echo"<input class = 'submit' type='submit' value='Delete' />";
                                echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                        echo"</form>";
                        printf("<br>");
                    }
                }
            }
        }
        $stmt2->close();
        //after each post, give option to post a comment for the logged in user
        if(isset($_SESSION["loggedIn"])){
            if($_SESSION["loggedIn"] == "yes"){
                echo "<form action= " . htmlentities($_SERVER['PHP_SELF']) . " method='POST'>";
                    echo"<p>";
                        echo "<textarea name = 'comment' rows='2' cols='30'></textarea>";
                        echo "<input type = 'text' name = 'postNum' style = 'display:none;' value ='" . htmlspecialchars($row['id']) . "' class='postNum'/>";
                        echo "<input type='submit' value='Post Comment' />";
                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                    echo "</p>";
                echo "</form>";
                //if they wish to post a comment and their comment isn't empty
                if(isset($_POST["comment"])){
                    if(trim($_POST["comment"])!=""){
                        require 'database.php';
                        //use id from post to insert comment
                        $comment = (string)$_POST['comment'];
                        $user = (string)$_SESSION["user"];
                        $id = (int)$_POST["postNum"];
                        if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
                            die("Request forgery detected");
                        }
                        //if no forgery, add comment to post
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
        }
        //if logged in and you are the user who made the post, you are given the option to edit and delete it 
        if(isset($_SESSION["loggedIn"])){
            if($_SESSION["loggedIn"] == "yes"){
                if($_SESSION["user"] == htmlspecialchars($row["username"])){
                    //edit post
                    echo "<form class = 'inline' action='editPost.php' method='POST'>";
                        echo"<input type = 'text' name = 'editPost' style = 'display:none;' value ='" . $row['id'] . "' class='editPost'/>";
                        echo"<input type='submit' value='Edit Post' />";
                        echo "<input type='hidden' name='token' value=" . $_SESSION['token'] . "/>";
                    echo"</form>";
                    //delete post
                    echo "<form class = 'inline' action='deletePost.php' method='POST'>";
                        echo"<input type = 'text' name = 'deletePost' style = 'display:none;' value ='" . $row['id'] . "' class='deletePost'/>";
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