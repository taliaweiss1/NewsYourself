<?php
if(isset($_POST['postText'])){
    $postText =  (string)$_POST["postText"];
}
else{
    header("Location: http://ec2-13-59-48-200.us-east-2.compute.amazonaws.com/~talia.weiss/wrongUsername.html");
}
?>