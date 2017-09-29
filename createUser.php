<!DOCTYPE html>
<html>
<head>
    <title>Join News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    Username Cannot Exceed 20 Characters
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
    //if password doesn't match retyped password
    if($pass1 != $pass2){
        echo "Passwords don't match. Try again.";
    }
    //if passwords match 
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
        //if username doesn't exist input the username and password into the users table 
        if(!$usernameExists){
            $stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('ss', $username, $pass1);
            $stmt->execute();
            $stmt->close();
			session_start();
			$_SESSION["loggedIn"] = "yes";
			$_SESSION["user"] = $_POST["newUsername"];
			header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/successCreateUser.html");
        }
   }
}
?>
</body>
</html>
