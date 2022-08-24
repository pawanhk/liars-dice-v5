<?php 
error_reporting(E_ALL ^ E_WARNING);
session_start();
include "connect.php";
$session = $_SESSION['session'];
$username =  $_SESSION['username'];
$currp = $username;
// get turn again incase 
$sql = "SELECT turn from game_data_mult WHERE session='$session'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$tur = $row['turn'];
	}
}
// get the second player 
$sql = "SELECT * FROM rooms WHERE session='$session'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$opp = $row['user'];
		$room = $row['name'];
	}
}
$sql = "SELECT WH,cba,cbq FROM game_data_mult WHERE session='$session' AND turn='$tur'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$wh = $row['WH'];
		$cba = $row['cba'];
		$cbq = $row['cbq'];
	}
}

// echo here 


// decide who plays 
if ($wh == "por"){
	echo $currp . " has rolled dice !";
	$turn_complete = false;
}
if($wh == "NNN"){
	echo "waiting for players to make move !";
	$turn_complete = false;
	$allow = false;
}
if($wh == "ptr"){
	echo  $opp ." has rolled dice  !";
	$turn_complete = false;
	$allow = false;
}
if($wh == "pob"){
	echo $currp." has raised bet!";
	$turn_complete = false;
	$allow = false;
}
if($wh == "ptb"){
	echo  $opp ." has raised bet!";
	$turn_complete = false;
	$allow = false;
}
if($wh == "poc"){
	echo  $currp ." has called ! ";
	$turn_complete = false;
	$allow = true;
}
if($wh == "ptc"){
	echo  $opp . " has called ! ";
	$turn_complete = true;
	$allow = true;
}	
if($whoWon == $currp && $allow){
	echo $curpp . " wins round ! ";
	$turn_complete = true;
	$allow = true;
}
if($whoWon == $opp && $allow){
	echo $opp . " wins round ! ";
	$turn_complete = true;
}
if($wh == "reset"){
	echo $currp . " has reset the game, <br>waiting for players to make a move";
}
if($wh == "kick"){
	echo "player " . $opp . " removed by host";
}

echo $cba;
echo $cbq;
								

?>