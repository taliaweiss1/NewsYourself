<!DOCTYPE html>
<html>
<head>
	<title>News Yourself</title>
    <link rel = "stylesheet" type = "text/css" href = "styleNewsSite.css" />
	<meta charset="utf-8"/>
</head>
<body>
    <h1>
         News Yourself
    </h1>
    <!--form for loging in, takes user to set session to set the session for a registered user-->
    <form action = "setSession.php" method = "POST">
    <p>
         <label for="username">Username: </label>
         <input type = "text" name = "username" id="username"/>
    </p>
	<p>
		 <label for="password">Password: </label>
         <input type = "password" name = "password" id="password"/>
		 <input type="submit" value="Login" />
	</p>
    </form>
    <!--takes a potential new user to a create user page -->
    <form action = "createUser.php" method = "POST">
    <p>
		<input type="submit" value="Become A User" />
	</p>
    </form>
    <?php
    session_start();
    if (isset($_SESSION["loggedIn"])){
        if($_SESSION["loggedIn"] == "yes"){
            printf("Welcome, %s <br>", htmlentities( $_SESSION["user"]));?>
            <form action = "logout.php">
                <input type = "submit" value = "Logout"/>
            </form>
            <?php
        }
    }
    ?>
</body>
</html>