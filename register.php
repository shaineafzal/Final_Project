<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
?>

<?php


$mysqli = new mysqli("oniddb.cws.oregonstate.edu","afzals-db","EUhwv7xKbCMM3XOm","afzals-db");
if(!$mysqli || $mysqli->connect_errno){
	echo "Connection error ".$mysqli->connect_errno."".$mysqli->connect_error;
	}

$server = htmlspecialchars($_SERVER["PHP_SELF"]);

$username = $_POST["username"];
$password = $_POST["password"];

if($_POST["color"]){
	$color = $_POST["color"];
}

if($color){
$insert = "INSERT INTO person(username, password, color) VALUES ('". $username. "', '" . $password. "', '" . $color. "')";
if ($mysqli->query($insert) === TRUE) {
    echo "New entry successful";
} else {
    echo "Username is taken";
}
}
else{
	$insert = "INSERT INTO person(username, password) VALUES ('". $name. "', '" . $password. "')";
	if ($mysqli->query($insert) === TRUE) {
    echo "New entry successful";
} else {
    echo "Username is taken";
}
}

$mysqli->close();
?>
