<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Check if somebody just signed in this will assign their user name to the session
if((session_status() == PHP_SESSION_ACTIVE)){
	if(isset($_POST["userlog"])){
		$_SESSION["username"] = $_POST["userlog"];
	}
    else if(isset($_POST["userreg"])){
		$_SESSION["username"] = $_POST["userreg"];
	}
}

// If you try to come to this page and are not logged in it redirects to login page
if((!empty($_POST["action"]) && $_POST["action"] == "end") || (empty($_SESSION["username"]))){
	$_SESSION= array();
	session_destroy();
	$filePath = explode('/', $_SERVER['PHP_SELF'], -1);
	$filePath = implode('/', $filePath);
	$redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
	header("Location: {$redirect}/LoginPage.html", true);
	die();
} 

// If there is an open session and you leave and come back it will assign your session username to the post variable
if((session_status() == PHP_SESSION_ACTIVE) && (!empty($_SESSION["username"]))){
	$_POST["userlog"] = $_SESSION["username"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<LINK href="ffsr.css" rel="stylesheet" type="text/css">
<title>ffscoutingreport.php</title>
</head>
<body>

<?php

// Assign username variable
$getuser = "";
$getuser = $_POST["userlog"];

// Identify server
$server = htmlspecialchars($_SERVER["PHP_SELF"]);

// The code for connecting to my database I got from lecture
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","afzals-db","EUhwv7xKbCMM3XOm","afzals-db");
if(!$mysqli || $mysqli->connect_errno){
	echo "Connection error ".$mysqli->connect_errno."".$mysqli->connect_error;
	}

// Code for removing a player from user's database
if(!empty($_POST["remdb"])){
	
$playertorem = 	$_POST["remdb"];

// Delete player from table
$remplayer = "DELETE FROM persondb WHERE name=\"$playertorem\"";
	  	  
if ($mysqli->query($remplayer) === FALSE) {
    echo "Delete failed";
}	  
	  	  
}

// Code to add player to user database
if(!empty($_POST["adddb"])){
	
$playertoadd = 	$_POST["adddb"];

$iname = "";
$ipos = "";
$itname = "";
$iavgppg = "";
$icost = "";
$itouches = "";
$iinjuryrisk = "";

$qadd = "SELECT p.name, p.position, t.name AS Tname, f.avgppg, f.cost, f.touches, i.injuryrisk FROM player p
INNER JOIN team t ON t.id = p.tid 
INNER JOIN fantasy_stats f ON f.pid = p.id
INNER JOIN injuries i ON i.pid = p.id
WHERE p.name = \"$playertoadd\"
ORDER BY f.avgppg DESC";

$radd = mysqli_query($mysqli, $qadd);

// Get all of players info from database, assign info to variables, insert values into database, and output table
if(mysqli_num_rows($radd) > 0){
    while($row = mysqli_fetch_assoc($radd)){
	  $iname = $row["name"];
      $ipos = $row["position"];
      $itname = $row["Tname"];
      $iavgppg = $row["avgppg"];
      $icost = $row["cost"];
      $itouches = $row["touches"];
      $iinjuryrisk = $row["injuryrisk"];	
    } 
}
else {
  echo "0 results";
}

$addplayer = "INSERT INTO persondb (username, name, position, tname, avgppg, cost, touches, injuryrisk) 
              values ('".$getuser."', '".$iname."', '".$ipos."', '".$itname."', '".$iavgppg."','".$icost."', '".$itouches."', '".$iinjuryrisk."')";
	  	  
if ($mysqli->query($addplayer) === FALSE) {
    echo "Insert failed";
}
}

if(!empty($_POST["userlog"])){
static $username = "";
static $color = "";
static $tcol = "";
$totalcost = 0;
$totalpts = 0;


//Select statement to retrieve database info
$select = "SELECT username, color FROM person WHERE username=\"$getuser\"";
$result = mysqli_query($mysqli, $select);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
      $username = $row["username"];
	  $color = $row["color"];
    }
}

// Use users team color to customize page
if($color == "white"){
	$tcol = "black";
}
else{
	$tcol = "white";
}

// Format header and subheader, customize with users team color
echo "<div id=\"header\" style=\"border: 4px solid black; background-color:$color; color:$tcol;\">
      <h1>Fantasy Football Scouting Report<br><br></h1></div>";
echo "<div id=\"subhead\" style=\"margin-left: 20%; text-align: center; width: 800px; border: 2px solid black; background-color:$tcol; 
      color: $color\"><h2>*Preparation is the Foundation of Success*</h2></div>";

// Select statement to retrieve user database	  
$pdbsel = "SELECT name, position, tname, avgppg, cost, touches, injuryrisk FROM persondb
WHERE username = \"$getuser\"
ORDER BY avgppg DESC";	  

// Execute query and output table
$pdbres = mysqli_query($mysqli, $pdbsel);

echo "<br><div style=\"font-size:x-large; margin-left: 32%; font-weight: bold; background-color: white;border: 4px solid black; 
      width: 500px;\">Players you have added to your favorites</div>";

// Set up table
echo "<br><table border=1 style=\"background: white; margin-left: 23%;\"><br><tr><td>Add to Favorites<td>Name<td>Position<td>Team
       <td>Fantasy ppg<td>Cost<td>Touches per game<td>Injury Risk";

if(mysqli_num_rows($pdbres) > 0){
    while($row = mysqli_fetch_assoc($pdbres)){
      echo '<tr>';
	  echo '<td>'. "<form method=\"post\" action=\"$server\"><input type=\"hidden\" name=\"userlog\" value=\"$getuser\">
	  <input type=\"hidden\" name=\"remdb\" value=\"". $row["name"]. "\">
	  <input type=\"submit\" value=\"Delete\"></form>" .'<td>'. $row["name"]. '<td>' . $row["position"]
	  . '<td>'. $row["tname"]. '<td>' . $row["avgppg"]. '<td>' . $row["cost"]. '<td>'. $row["touches"]. '<td>' . $row["injuryrisk"].'<br>';
	  
	  $totalcost = $totalcost + $row["cost"];
	  $totalpts = $totalpts + $row["avgppg"];
    } 
}
	else {
      echo "0 results";
    }
echo "<tr><td colspan=\"4\">Total Cost: $totalcost</td><td colspan=\"4\">Total PPG: $totalpts</td>";
echo '</table>';	  

// Echo html to customize appearance and create drop down list to choose position
echo "<br>";
echo "<div style=\"font-size:x-large; margin-left: 32%; font-weight: bold; background-color: $color; color: $tcol; border: 4px solid black;
      width: 550px;\">Select which position of players you would like to see</div><br>";

echo "<form method=\"post\" action=\"$server\">";
echo "<select name=\"position\"><option value=\"QB\">Quarterback</option><option value=\"RB\">Running Back</option>
      <option value=\"WR\">Wide Receiver</option>";
echo "<option value=\"K\">Kicker</option><option value=\"D\">Defense</option></select><input type=\"submit\">";
echo "<input type=\"hidden\" name=\"userlog\" value=\"$getuser\"></form>";
}

// Code for once a position is chosen
if(!empty($_POST["position"])){
	static $pos;
	$pos = $_POST["position"];

// Select statement to get all players from selected position
$query = "SELECT p.name, p.position, t.name AS Tname, f.avgppg, f.cost, f.touches, i.injuryrisk FROM player p
INNER JOIN team t ON t.id = p.tid 
INNER JOIN fantasy_stats f ON f.pid = p.id
INNER JOIN injuries i ON i.pid = p.id
WHERE p.position = \"$pos\"
ORDER BY f.avgppg DESC";

$result = mysqli_query($mysqli, $query);

// Dynamically output data table with database info
echo "<br><table border=1 style=\"background: white; margin-left: 22%;\"><br><tr><td>Add to Favorites<td>Name<td>Position<td>Team
      <td>Fantasy ppg<td>Cost<td>Touches per game<td>Injury Risk";


if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
      echo '<tr>';
	  echo '<td>'. "<form method=\"post\" action=\"$server\"><input type=\"hidden\" name=\"userlog\" value=\"$getuser\">
	  <input type=\"hidden\" name=\"position\" value=\"$pos\"><input type=\"hidden\" name=\"adddb\" value=\"". $row["name"]. "\">
	  <input type=\"submit\" value=\"Add\"></form>" .'<td>'. $row["name"]. '<td>' . $row["position"]
	  . '<td>'. $row["Tname"]. '<td>' . $row["avgppg"]. '<td>' . $row["cost"]. '<td>'. $row["touches"]. '<td>' . $row["injuryrisk"].'<br>';
    } 
}
	else {
      echo "0 results";
    }

echo '</table>';
} 	  

//Close table connection
$mysqli->close();

echo "<br><br><br><br><br>";
echo "<br><div style=\" font-weight: bold; margin-left: 32%; background-color: $color; color: $tcol;border: 4px solid black; 
      width: 500px;\">Here is a video if you need a little motivation</div>";

// Found a good site that showed me how to embed youtube vid without iframes at http://jeromejaglale.com/doc/php/youtube_generate_embed_from_url

$url = 'https://www.youtube.com/watch?v=xPubMOh_Tw8';
preg_match(
        '/[\\?\\&]v=([^\\?\\&]+)/',
        $url,
        $matches
    );
	
$id = $matches[1];
 
$wide = '448';
$tall = '270';
echo '<object width="' . $wide . '" height="' . $tall . '"><param name="movie" value="http://www.youtube.com/v/' . $id . 
'&amp;hl=en_US&amp;fs=1?rel=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always">
</param><embed src="http://www.youtube.com/v/' . $id . '&amp;hl=en_US&amp;fs=1?rel=0" type="application/x-shockwave-flash" 
allowscriptaccess="always" allowfullscreen="true" width="' . $wide . '" height="' . $tall . '"></embed></object>';

echo "<br><br><br>";

//Logout button
echo "<form method=\"post\" action=\"$server\">";
echo "<input type=\"hidden\" name=\"action\" value=\"end\">";
echo "<input type=\"submit\" value=\"Logout\">";
echo "</form>";
echo "<br><br>"
?>


</body>
</html>
