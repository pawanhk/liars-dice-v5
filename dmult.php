<?php
// do login verification here
error_reporting(E_ALL ^ E_WARNING);
session_start(); 
include "connect.php";
$username =  $_SESSION['username'];
$sql = "SELECT * FROM accounts WHERE user='$username'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0){
	while($row = mysqli_fetch_assoc($result)){
		$admin_stat = $row['admin_status'];
	}
} 
if($admin_stat != 10){
	header("Location: index.php?access=denied");
}

// file download stuff

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title> Liar's Dice </title>
	<link rel="stylesheet" type="text/css" href="main.css">
	<link rel="stylesheet" type="text/css" href="index.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fascinate+Inline&family=Orbitron&display=swap" rel="stylesheet">  
	<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet"> 
	<link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&display=swap" rel="stylesheet"> 
	<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet"> 
	<link rel = "icon" href = "img/icon.png" type = "image/x-icon">
	<!-- JavaScript Bundle with Popper -->

</head>
<style>
  <?php 
  include "css/main.css";
  include "css/index.css";
  include "css/admin.css";
   ?>
</style>
<body class="d-flex flex-column min-vh-100">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="	anonymous"></script>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
  		<a class="navbar-brand" href="index.php">{Research Project}</a>
  		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    		<span class="navbar-toggler-icon"></span>
  		</button>
  		<div class="collapse navbar-collapse" id="navbarNavDropdown">
  			<span class="move-right">
    		<ul class="navbar-nav ml-auto">
     			<li class="nav-item">
       			 	
      			</li>
      			<?php
      			if($username){
      			echo "<li class='nav-item'><a class='nav-link'> " . $username . "</a></li>";
      			echo "<li class='nav-item'>
        			<a class='nav-link' href='profile.php'> profile </a>
      			</li>";
      			echo "<li class='nav-item'>
        			<a class='nav-link' href='logout.php'> logout </a>
      			</li>";
      			if($admin_stat == 10){
      					echo "<li class='nav-item'>
        			<a class='nav-link' href='admin.php'> admin </a>
      			</li>";
      			}
      			}else{
      				echo "<li class='nav-item'>
        			<a class='nav-link' href='login.php'> login </a>
      			</li>";
      			echo "<li class='nav-item'>
        			<a class='nav-link' href='signup.php'> signup </a>
      			</li>";
      			}
      			?>
      			 <li class="nav-item">
        			<a class="nav-link" href="https://github.com/pawanhk/liars-dice-0.0.2">Version Number: 3.0.0 </a>
      			</li>
    		</ul>
    		</span>
  		</div>
	</nav>
	<?php
	$filename = 'files/multi.csv';
	$sql =  "SELECT * FROM game_data_mult";
	$result = mysqli_query($conn,$sql);
	if(mysqli_num_rows($result) > 0){
		$user_arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			if($row['id'] == 1){
				$id = "ID";
			}else{
				$id = $row['id'];
			}
			if($row['session'] == 1){
				$session = "session";
			}else{
				$session = $row['session'];
			}
			if($row['turn'] == 999){
				$turn = "turn";
			}else{
				$turn = $row['turn'];	
			}
			if($row['room'] == "lobi"){
				$room = "roomname";
			}else{
				$room = $row['room'];
			}
			if($row['po'] == "playerOne"){
				$po = "playerOne";
			}else{
				$po = $row['po'];
			}
			if($row['pt'] == "playerTwo"){
				$pt = "playerTwo";
			}else{
				$pt = $row['pt'];
			}
			$por = $row['por'];
			$ptr = $row['ptr'];
			if($row['cba'] == 7){
				$cba = "amount ";
			}else{
				$cba = $row['cba'];
			}
			if($row['cbq'] == 7){
				$cbq = "qty ";
			}else{
				$cbq = $row['cbq'];
			}
			$wh = $row['WH'];
			$whoWon = $row['whoWon'];
			if($row['pows'] == 999){
				$pows = $po . " winstreaks";
			}else{
				$pows = $row['pows'];
			}
			if($row['ptws'] == 999){
				$ptws = $pt . " winstreaks";
			}else{
				$ptws = $row['ptws'];
			}
			if($row['frtime'] == 1){
				$frtimeo = "host time " ;
			}else{
				$frtimeo = $row['frtime'];
			}
			if($row['frtime2'] == 1){
				$frtimet = "player time " ;
			}else{
				$frtimet = $row['frtime2'];
			}
			$user_arr[] = [$id,$session,$turn,$room,$po,$pt,$por,$ptr,$cba,$cbq,$wh,$whoWon,$pows,$ptws,$frtimeo,$frtimet];
		}
	}

		// file creation
	$file = fopen($filename,"w");

	foreach ($user_arr as $line){
		if($line){
			 fputcsv($file,$line);
		}else{
			echo "error";
		}
	}

	if($file){
		fclose($file);
	}

	// download
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-Type: application/csv; "); 

	//readfile($filename);


	?>

	<div class="container-admin">
		<h2> Your file is ready for download ! </h2>
		<button type="button" class="btn btn-secondary btn-lg"><a download="multi.csv" href="files/multi.csv">Download </a></button><br><br>
	</div>

	<br><br><br><br><br><br><br><br>
	<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted mt-auto">
  <div class="text-center p-4">
  	<span class="move-down">
    <a class="text-reset fw-bold" href="privacy.php"><span class="footer-elms">Privacy Policy </span> </a>
    <a class="text-reset fw-bold" href="contact.php"><span class="footer-elms">Contact  </span> </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">FaQ's </span>  </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">P23Productions </span>  </a>
    </span>

  </div>
</footer>
</body>
</html>