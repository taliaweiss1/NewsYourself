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
	<h1>News Yourself</h1>
	<h2>Create Post:</h2>
	<!--form to create post includes title, text and associated link-->
	<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
    <p>
         <label for="postTitle">Title for Your Story: </label>
         <input type = "text" name = "postTitle" id="postTitle"/>
		 <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
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
	<!--go back to homepage button-->
	<form action="homepage.php">
		 <input type = "submit" value = "Take Me Back to News Yourself"/>
	</form>
	<?php
		if(isset($_POST['postText'])){
			if(trim($_POST['postText']) !=""){
				require 'database.php';
				$postText = (string)$_POST['postText'];
				//if there is text, title and link in the post, we input all three pieces of data, along with the username of the logged in user
				if(($_POST['postTitle'] !="") && ($_POST['postLink'] !="")){
						$postTitle = (string)$_POST['postTitle'];
						$postLink = (string)$_POST['postLink'];
						if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
							die("Request forgery detected");
						}
						$stmt = $mysqli->prepare("insert into posts (textInPost, username, title, link) values (?, ?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('ssss', $postText, $_SESSION["user"], $postTitle, $postLink);
						$stmt->execute();
						$stmt->close();
					}
				//if there is a link but there is no title then we input all of the previous case's information without the title 
				else if($_POST['postLink'] !=""){
						$postLink = (string)$_POST['postLink'];
						if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
							die("Request forgery detected");
						}
						$stmt = $mysqli->prepare("insert into posts (textInPost, username , link) values (?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('sss', $postText, $_SESSION["user"], $postLink);
						$stmt->execute();
						$stmt->close();
				}
				//if there is a title but there is no link then we input everything but the link
				else if($_POST['postTitle'] !=""){
						$postTitle = (string)$_POST['postTitle'];
						if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
							die("Request forgery detected");
						}
						$stmt = $mysqli->prepare("insert into posts (textInPost, username , title) values (?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('sss', $postText, $_SESSION["user"], $postTitle);
						$stmt->execute();
						$stmt->close();
				}
				//if the only inputted text is the story then we insert the story and the username of the logged in user
				else{
						if(!hash_equals($_SESSION['token'], str_replace('/','',$_POST['token']))){
							die("Request forgery detected");
						}
						$stmt = $mysqli->prepare("insert into posts (textInPost, username) values (?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						$stmt->bind_param('ss', $postText, $_SESSION["user"]);
						$stmt->execute();
						$stmt->close();
				}
				header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
			}
			//if they did not input a story into the story field, then they are told that they must
			else{
				echo "You must enter a story!";
			}
		}
	?>
</body>
</html>