<!DOCTYPE html>
    <?php
        session_start();
    ?>
<html>
<head>
    <title>Set Session</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
<?php
    printf("<h1>News Yourself</h1> <br> <h2>Your Favorites:</h2>");
    //get all of the post IDs of the posts that the user has favorited
    require 'database.php';
    $stmt = $mysqli->prepare("select id from favorites where username=? ");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('s', $_SESSION["user"]);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
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
        require 'database.php';
        //use the favorited post ids to get all of the data of the post
        $stmt2 = $mysqli->prepare("select title, username, textInPost, link from posts where id=$row[id]"); 
        if(!$stmt2){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while($row2 = $result2->fetch_assoc()){
            printf("<h2 id='title'> %s </h2> By: %s <br> Up Votes: %s Down Votes: %s <br> %s <br> \n",
                    htmlspecialchars( $row2["title"] ),
                    htmlspecialchars( $row2["username"] ),
                    htmlspecialchars($upVoteNum),
                    htmlspecialchars($downVoteNum),
                    htmlspecialchars( $row2["textInPost"] ));
            if($row2["link"] != ""){
                printf("Associated Link: %s",
                htmlspecialchars( $row2["link"] ));
            }
            printf("<br> <br>");
        }
        $stmt2->close();
    }
    $stmt->close();
    //go back to homepage form
    echo "<form action='homepage.php'>";
        echo"<input type='submit' value='Go Back to Homepage' />";
    echo"</form>";
?>
</body>
</html>