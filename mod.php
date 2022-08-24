<?php
include "connect.php";
error_reporting(E_ALL ^ E_WARNING);
ob_start();
session_start(); 
$username =  $_SESSION['username'];
$session = $_SESSION['session'];
$currp = $username;

// get user information
$sql = "SELECT * FROM accounts WHERE user='$username'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$email = $row['email'];
		$hs = $row['hs'];
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

// get second user info
$sql = "SELECT * FROM accounts WHERE user='$opp'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$emailo = $row['email'];
		$hso = $row['hs'];
	}
}


// functions
function request($sec=10){
	header("Refresh:".$sec);
}


// start by creating a session if not already there 
	// insert into table if not there
$sql = "SELECT * FROM game_data_mult WHERE session='$session'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	// do nothing
	while($row = mysqli_fetch_assoc($result)){
		$turn = $row['turn'];
	}
}else{
	// creating session
	$turn = 1;
	$sql = "INSERT INTO game_data_mult (session,turn,room,po) VALUES ('$session','$turn','$room','$currp')";
	if(mysqli_query($conn,$sql)){
		header("Refresh:0","host.php");
	}
}

$sql = "UPDATE game_data_mult SET po='$currp',room='$room' WHERE session='$session' AND turn='$turn'";
if(mysqli_query($conn,$sql)){
	// done
}else{
	echo "user joining error";
}


// get the information
$sql = "SELECT * FROM game_data_mult WHERE session='$session' AND turn='$turn'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$por = $row['por'];
		$ptr = $row['ptr'];
		$cba = $row['cba'];
		$cbq = $row['cbq'];
		$wh = $row['WH'];
		$whoWon = $row['whoWon'];
		$pows = $row['pows'];
		$ptws = $row['ptws'];
		$port = $row['port'];
		$ptrt = $row['ptrt'];
		$rtime = $row['rtimeo'];
		$rtime2 = $row['rtimet'];
		$frtime = $row['frtime'];
		$req = $row['reqt'];
		$pt = $row['pt'];
	}
}

echo $wh

//request(5);
?>