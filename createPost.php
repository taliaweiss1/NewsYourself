<!DOCTYPE html>
<html>
<head>
    <title>Create A Post</title>
	<link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
    <p>
         <label for="postTitle">Title for Your Story: </label>
         <input type = "text" name = "postTitle" id="postTitle"/>
    </p>
	<p>
         Type your story here! ****This field is required to submit a story**** 
    </p>
    <p>
		 <textarea name = "postText" rows="10" cols="30"></textarea>
	</p>
    <p>
        <label for="postLink">Want to Link a Related Article? Put it Here! </label>
         <input type = "url" name = "postLink" id="postLink"/>
    </p>
    <p>
        <input type = "submit" value = "Submit my Story!"/>
    </p>
    </form>
	<p>
		<form action="homepage.php">
			 <input type = "submit" value = "Take Me Back to News Yourself"/>
		</form>
	</p>
	<?php
		session_start();
		if(isset($_POST['postText'])){
			if(trim($_POST['postText']) !=""){
				require 'database.php';
				$postText = $_POST['postText'];
				//if there is text, title and link
				if(($_POST['postTitle'] !="") && ($_POST['postLink'] !="")){
						$postTitle = $_POST['postTitle'];
						$postLink = $_POST['postLink'];
						$stmt = $mysqli->prepare("insert into posts (textInPost, username, title, link) values (?, ?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('ssss', $postText, $_SESSION["user"], $postTitle, $postLink);
						$stmt->execute();
						$stmt->close();
					}   					
				else if($_POST['postLink'] !=""){
						$postLink = $_POST['postLink'];
						$stmt = $mysqli->prepare("insert into posts (textInPost, username , link) values (?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('sss', $postText, $_SESSION["user"], $postLink);
						$stmt->execute();
						$stmt->close();
				}
				else if($_POST['postTitle'] !=""){
						$postTitle = $_POST['postTitle'];
						$stmt = $mysqli->prepare("insert into posts (textInPost, username , title) values (?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('sss', $postText, $_SESSION["user"], $postTitle);
						$stmt->execute();
						$stmt->close();
				}
				else{
						$stmt = $mysqli->prepare("insert into posts (textInPost, username) values (?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('ss', $postText, $_SESSION["user"]);
						$stmt->execute();
						$stmt->close();
				}
			}
			else{
				echo "You must enter a story!";
			}
		}
	?>
</body>
</html>