<!DOCTYPE html>
	<?php
		session_start();
	?>
<html>
<head>
    <title>Create A Post</title>
	<link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    <?php
        printf("<h1> News Yourself </h1> <br> <h2> Popular Page </h2> <br>");
        require 'database.php';
        $stmt = $mysqli->prepare("select count(votes.vote) as upVotes, posts.id, posts.title, posts.textInPost, posts.username, posts.link from votes join posts on (posts.id = votes.id) where vote='U' group by votes.id having upVotes>2"); 
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $upVoteNum;
            $downVoteNum;
            $pid=htmlspecialchars($row["id"]);
            require 'database.php';
            $stmt5 = $mysqli->prepare("select count(vote) as upVotes2 from votes where id=$pid and vote='U'");
            if(!$stmt5){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt5->execute();
            $result5 = $stmt5->get_result();
            while($row5 = $result5->fetch_assoc()){
                $upVoteNum=$row5["upVotes2"];
            }   
            $stmt5->close();
            require 'database.php';
            $stmt6 = $mysqli->prepare("select count(vote) as downVotes from votes where id=$pid and vote='D'");
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
        }
        $stmt->close();
        echo "<form action='homepage.php'>";
            echo"<input type='submit' value='Go Back to Homepage' />";
        echo"</form>";
    ?>
</body>
</html>