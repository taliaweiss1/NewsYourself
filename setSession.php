<!DOCTYPE html>
<html>
<head>
    <title>Set Session</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
<?php
    if(isset($_POST['username']) && isset($_POST['password'])){
    $username =  (string)$_POST["username"];
    $password = (string)$_POST["password"];
    require 'database.php';
        $stmt = $mysqli->prepare("select username from users");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $usernameMatches = false;
        //check to see if username already exists 
        while($row = $result->fetch_assoc()){
            if($username == htmlspecialchars($row["username"] )){
                $usernameMatches = true;
                break;
            }
        }
        if($usernameMatches){
			require 'database.php';
			// Use a prepared statement
//			if(!hash_equals($_SESSION['token'], $_POST['token'])){
//                die("Request forgery detected");
//			}
			$stmt = $mysqli->prepare("select password from users where username=?");
		// Bind the parameter
		$stmt->bind_param('s', $username);
		$stmt->execute();
		// Bind the results
		$stmt->bind_result($pwd_hash);
		$stmt->fetch();
		$pwd_guess = $_POST['password'];
		// Compare the submitted password to the actual password hash
		if(password_verify($pwd_guess, $pwd_hash)){
		// Login succeeded!
		session_start();
		$_SESSION["loggedIn"] = "yes";
        $_SESSION["user"] = $username;
		$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string
		// Redirect to your target page
		header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
		} else{
		// Login failed; redirect back to the login screen
		header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/wrongPassword.html");
		}
		}
        //username doesn't exist-- take user to a wrong username page
        else{
            header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/wrongUsername.html");
        }
    }
?>
</body>
</html>