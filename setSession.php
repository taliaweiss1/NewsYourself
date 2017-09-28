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
            $passwordMatches = false;
            $stmt = $mysqli->prepare("select password from users");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->execute();
            $passwords = $stmt->get_result();
            echo "<ul>\n";
            //check to see if username already exists 
            while($row = $passwords->fetch_assoc()){
                if($password == htmlspecialchars($row["password"] )){
                    $passwordMatches = true;
                    break;
                }
            }
            $stmt->close();
            if($passwordMatches){
                session_start();
                $_SESSION["loggedIn"] = "yes";
                header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
            }
            else{
                header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/homepage.php");
            }
        }
    }
?>
</body>
</html>