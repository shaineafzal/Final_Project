<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
?>

<?php


// The code for connecting to my database I got from lecture
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","afzals-db","EUhwv7xKbCMM3XOm","afzals-db");
if(!$mysqli || $mysqli->connect_errno){
	echo "Connection error ".$mysqli->connect_errno."".$mysqli->connect_error;
	}

$server = htmlspecialchars($_SERVER["PHP_SELF"]);

$username = $_POST["username"];
$password = $_POST["password"];
$usercheck = "";
$passcheck = "";


$select = 'SELECT username, password, color FROM person WHERE username="'.$username.'"';
$result = mysqli_query($mysqli, $select);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
      $usercheck = $row["username"];
	  $passcheck = $row["password"];
    }
} 
else {
    echo "User does not exist, please register";
}

if($username == $usercheck){
	if($password == $passcheck){
		echo "Successful";
	}
}

$mysqli->close();
?>
