<!DOCTYPE html>
	<?php
		session_start();
	?>
<html>
<head>
    <title>Join News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
	<h1>News Yourself</h1>
	<h2>Become a User:</h2>
    <h3>Username Cannot Exceed 20 Characters</h3>
	<!--form to become a user-->
	<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
	<p>
		<label for="newUsername">Create Username: </label>
        <input type = "text" name = "newUsername" id="newUsername"/>
	</p>
    <p>
        <label for="newPassword">Create Password:</label>
        <input type = "password" name = "newPassword" id = "newPassword"/>
    </p>
    <p>
        <label for="retypePassword">Retype Password:</label>
        <input type = "password" name = "retypePassword" id = "retypePassword"/>
    </p>
    <p>
		<input type="submit" value="Create User" />
	</p>
	</form>
	<?php
	if(isset($_POST['newUsername']) && isset($_POST['newPassword']) && isset($_POST['retypePassword'])){
		$username =  (string)$_POST["newUsername"];
		$pass1 = (string)$_POST["newPassword"];
		$pass2 = (string)$_POST["retypePassword"];
		//check to see that the user inputted a username
		if(trim($username) == ""){
			echo "You must enter a username";
		}
		else{
			//if password doesn't match retyped password
			if($pass1 != $pass2){
				echo "Passwords don't match. Try again.";
			}
			//if passwords match, check to see if the username already exists
			else{
				require 'database.php';
				$stmt = $mysqli->prepare("select username from users");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$stmt->execute();
				$result = $stmt->get_result();
				echo "<ul>\n";
				$usernameExists = false;
				//check to see if username already exists 
				while($row = $result->fetch_assoc()){
					if($username == htmlspecialchars( $row["username"] )){
						echo "Username already exists. Try another.";
						printf("\t<li>%s</li>\n",
						htmlspecialchars( $row["username"] )
						);
						$usernameExists = true;
						break;
					}
				}
				//if username doesn't exist input the username and hashed and salted password into the users table 
				if(!$usernameExists){
					$passHash = password_hash($pass1, PASSWORD_DEFAULT);
					$stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
					if(!$stmt){
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					$stmt->bind_param('ss', $username, $passHash);
					$stmt->execute();
					$stmt->close();
					$_SESSION["loggedIn"] = "yes";
					$_SESSION["user"] = $_POST["newUsername"];
					$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string
					header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/successCreateUser.html");
				}
			}
		}
	}
	//option to go back to the homepage
	echo "<form action='homepage.php'>";
		echo"<input type='submit' value='Go Back to Homepage' />";
	echo"</form>";
	?>
</body>
</html>
