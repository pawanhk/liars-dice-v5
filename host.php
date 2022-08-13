<?php
error_reporting(E_ALL ^ E_WARNING);
include "connect.php";
ob_start();
session_start(); 
$username =  $_SESSION['username'];
$session = $_SESSION['session'];
$currp = $username;
$_SESSION['current_better'] = $currp;

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


if($wh=="kick"){
		$sql = "DELETE FROM game_data_mult WHERE session='$session'";
		if(mysqli_query($conn,$sql)){

		}else{
			echo "destroy erro";
		}
		$turn = 1;
		$sql = "INSERT INTO game_data_mult (session,turn,room,po,pt,wh) VALUES ('$session','$turn','$room','$currp','none','kick')";
		if(mysqli_query($conn,$sql)){
			//header("Refresh:0","host.php");
		}else{
			echo "insertion error";
		}
}

// decide who starts the betting process
// default the better to host 
if($wh == "pob"){
	$_SESSION['current_better'] = $opp;
}


//request(5);
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
	include "css/play-syl.css";
   ?>
</style>
<body class="d-flex flex-column min-vh-100">
	<script type="text/javascript" src="autoPlayer.js"></script>
	<script type="text/javascript" src="autoReaction.js"></script>
	<script type="text/javascript" src="auto.js"></script>
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
      			}else{
      				echo "<li class='nav-item'>
        			<a class='nav-link' href='login.php'> login </a>
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

<div class="playground">
	<?php // game interface will go here ?> 
	<table class="master-table">
		<div class="cols">
					<div class="col-1">
						<td>
							<h1 class="main-header"> Room: <?php echo $room; ?>  </h1>
							<p>   session: <?php echo $session; ?> </p>
							<?php // main button controls here ?>
							<div class="main-control">
								<form method="POST">
									<button name="dice" type="submit" class="btn btn-success">Roll Dice</button><br><br>
									<button name="challenge" type="submit" class="btn btn-success">Challenge </button><br><br>
								</form>
								<form method="POST">
								<button name = "leave" type="submit" class="btn btn-danger">Leave Room </button><br><br>
								</form>
								<h2> Raise the Bet </h2>
								<form method="POST">
									<div class="input-group input-group-sm mb-3 ">
									  <div class="input-group-prepend ">
									    <span class="input-group-text " id="inputGroup-sizing-sm">Face Value</span>
									  </div>
									  <input name="amt" type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm ">
									</div>
									<div class="input-group input-group-sm mb-3 ">
									  <div class="input-group-prepend ">
									    <span class="input-group-text " id="inputGroup-sizing-sm">Amount</span>
									  </div>
									  <input name="qty"type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm ">
									</div>
									<button onclick="onSecondClick()" name="raise" type="submit" class="btn btn-success">Raise </button><br><br>
								</div>
							</form>
							<form method="POST">
								<button name="turn" type="submit" class="btn btn-secondary btn-lg">Next Turn </button>
							</form><small style="color:green;"><?php 
												if($req == "req"){
													echo $opp . " wants to change turn ";
												}else{
													//echo "x";
												}
												?></small><br>
						</td>
					</div>
					<div id="reactionTime"> </div>
					<div class="sep-line"></div>
					<div class="col-2">
						<td class="move-over">
							<h1 class="main-header"> Turn: <?php echo $turn ?>  </h1><br>
							<div class="machine-info">
								<table class="mach-table">
									<td> 
										<table>
											<div id="livePlayer"></div> 
										<br>
										</table>
										<h3 > Rating: <?php echo $ptrt; ?> </h3>
										<h3> Win streaks: <?php echo $ptws; ?> </h3>
										<br><br><br>
										<div class="player-info">
											<h2> <?php 
											if($username){
											echo $username;
											}else{
												echo "Player";
											}
											?> </h2> <br>
											<h3> Rating: <?php echo $port ;?> </h3>
											<h3> Win streaks: <?php echo $pows; ?>   </h3>
											<br>
											<h3> Commentary: 
												<?php
												// decide who plays 
												$sql = "SELECT * FROM game_data_mult WHERE turn='$turn'";
												$result = mysqli_query($conn,$sql);
												if(mysqli_num_rows($result)>0){
													while ($row = mysqli_fetch_assoc($result)) {
														$wh = $row['WH'];
													}
												}
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
											?>
											</h3>
										</div>
										<div class="comment">
											<br><br>
										</div>
									</td> 
									<?php
									// display creator bottom user top based on conditions 
									if($wh == "poc" || $wh == "ptc"){
										include "reveal-roll-p.php";
									}else{
										include "locked-roll-p.php";
									}
									?>
									<td class="move-er">
										<h2> Current Bid </h2><br>
										<div id="liveData">
										</div>
										<h3>
											Reaction Time: <div id="liveReaction"></div>
										</h3><br>
										<h2> Host Controls:  </h2><br>
										<form method="POST">
											<table>
												<td>
													<button class="btn btn-danger" name="reset" type="submit">Reset Game </button><br><br>
											<button class="btn btn-danger" name="kick" type="submit">Kick <?php 
											if($opp != "none"){
												echo $opp;
											}else{
												echo "";
											}
											?> </button><br><br>
												</td>
											</table>
											<br><br>
										</form><br>
										<br><br><br>
									</td>
								</table>
							</div>
						</td>
					</div>
			</div>
		</table>
	</div>
	<br><br><br><br>
	<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted mt-auto">
  <div class="text-center p-4">
  	<span class="move-down">
    <a class="text-reset fw-bold" href="privacy.php"><span class="footer-elms">Privacy Policy </span> </a>
    <a class="text-reset fw-bold" href="contact.php"><span class="footer-elms">Contact  </span> </a>
    <a class="text-reset fw-bold" href="about.php"><span class="footer-elms">About </span>  </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">P23Productions </span>  </a>
    </span>

  </div>
</footer>
</body>
</html>
<?php

// heavy lifting 
// when dice is rolled 
function rollDice(){
	$limit = 5;
	$arr = [0,0,0,0,0];
	for($x=0;$x<5;$x++){
		$arr[$x] = rand(1,6);
	}
	return $arr;
}

function checkWinner($conn,$por,$ptr,$turn,$challenger,$opp,$session,$cba,$cbq,$port,$ptrt,$pows,$ptws,$username){
	$correct_values = 0;
		for($x=0;$x<5;$x++){
			if($por[$x] == $cba ||$por[$x] == '1'){
				$correct_values++;
			}
		}
		for($x=0;$x<5;$x++){
			if($ptr[$x] == $cba || $ptr[$x] == '1'){
				$correct_values++;
			}
		}
		// now calculate values 
		if($correct_values >= $cbq){
			// challenger looses 
			$hso++;
			$sql = "UPDATE accounts set hs='$hso' WHERE user='$opp'";
			if(mysqli_query($conn,$sql)){

			}else{
				echo "account error";
			}
			$ptws++;
			$sql = "UPDATE game_data_mult SET ptws='$ptws' WHERE session='$session'";
			if(mysqli_query($conn,$sql)){
			}else{
				echo "streak error ";
			}
			return $opp;
			// update ratings
		}else{
			// challenger wins
			// update hs
			$hs++;
			echo $hs;
			$sql = "UPDATE accounts set hs='$hs' WHERE user='$username'";
			if(mysqli_query($conn,$sql)){

			}else{
				echo "account error";
			}
			$pows++;
			$sql = "UPDATE game_data_mult SET pows='$pows' WHERE session='$session'";
			if(mysqli_query($conn,$sql)){
			}else{
				echo "streak error ";
			}
			return $challenger; 
			// update ratings
		}
}

//when dice is rolled 
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['dice'])){
			// function to roll dice 
		$arr = rollDice();
		$roll = implode("", $arr);
		// insert into db
		// player one
		$sql = "UPDATE game_data_mult SET por='$roll',WH='por' WHERE turn='$turn' AND session='$session'";
		if(mysqli_query($conn,$sql)){
			header("Refresh:0");
		}else{
			echo "dice roll failed ";
		}
	}
}

// now for raising the bid
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['raise'])){
		$_SESSION['time1'] = floor(microtime(true) * 1000);
		$_SESSION['time11'] = 0;
		$amt = $_POST['amt'];
		$qty = $_POST['qty'];
		// player one
		$sql = "UPDATE game_data_mult SET cba='$amt',cbq='$qty',WH='pob' WHERE turn='$turn' AND session='$session'";
		$out = true;
		if(mysqli_query($conn,$sql)){
			header("Refresh:0");
		}
	}
}

// now for calling the bid 
if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(isset($_POST['challenge'])){
			$challenger = $currp;
			$_SESSION['time2'] = floor(microtime(true) * 1000);
			// reveal both sides and wait for 5 secs 
			$sql = "UPDATE game_data_mult SET WH='poc' WHERE turn='$turn' AND session='$session'";
			$out2 = true;
			if(mysqli_query($conn,$sql)){
				header("Refresh:0");
			}else{
				echo "challenge error";
			}
			$winner = checkWinner($conn,$por,$ptr,$turn,$challenger,$opp,$session,$cba,$cbq,$port,$ptrt,$pows,$ptws,$username);
			// update turn winner 
			$sql = "UPDATE game_data_mult SET whoWon='$winner' WHERE turn='$turn' AND session='$session'";
			if(mysqli_query($conn,$sql)){
				$turn_complete = true;
			}else{
				echo "winner errr ";
			}
	}
}

if($out==true){
	$time1 = $_SESSION['time1'];
	$stime1 = (string)$time1;
	//echo $time1 . "<br>";
	$sql = "UPDATE game_data_mult SET rtimeo='$stime1' WHERE turn='$turn' AND session='$session'";
	if(mysqli_query($conn,$sql)){
		header("Refresh:0");
		//$reveal = true;
	}else{
		echo "time error ";
	}
}
if($out2 == true){
	$time2 = $_SESSION['time2'];
	$stime2 = (string)$time2;
	//echo $time2;
	$sql = "UPDATE game_data_mult SET rtimet='$stime2' WHERE turn='$turn' AND session='$session'";
	if(mysqli_query($conn,$sql)){
		header("Refresh:0");
		//$reveal = true;
	}else{
		echo "time error ";
	}
}

$irtime = (int)$rtime;
$irtime2 = (int)$rtime2;
if($irtime && $irtime2){
	$frtime = $irtime2 - $irtime;
	$sql = "UPDATE game_data_mult SET frtime='$frtime' WHERE turn='$turn' AND session='$session'";
	if(mysqli_query($conn,$sql)){
		//header("Refresh:0");
		$_SESSION['frtime'] = $frtime;
	}
}
else{
	//$_SESSION['frtime'] = 0;
}


// now for turn completetion
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['turn'])){
			if($turn_complete == 1){
				$turn++;
				$sql = "INSERT INTO game_data_mult (session,turn,room,po,pows,ptws,port,ptrt) VALUES ('$session','$turn','$room','$currp','$pows','$ptws','$port','$ptrt')";
				if(mysqli_query($conn,$sql)){
					header("Refresh:0");
				}else{
					echo "new turn error" ;
				}
			}
			 $sql = "DELETE FROM multiplayer" ;
				mysqli_query($conn,$sql);
				$sql = "INSERT INTO multiplayer SELECT * FROM multiplayer";
				mysqli_query($conn,$sql);
	}
}


// now for reseting the game
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['reset'])){
			// reset session
		$sql = "DELETE FROM game_data_mult WHERE session='$session'";
		if(mysqli_query($conn,$sql)){

		}else{
			echo "destroy erro";
		}
		$turn = 1;
		$sql = "INSERT INTO game_data_mult (session,turn,room,po,pt,wh) VALUES ('$session','$turn','$room','$currp','$opp','reset')";
		if(mysqli_query($conn,$sql)){
			header("Refresh:0","host.php");
		}else{
			echo "insertion error";
		}

	}
}

// now for kicking the player 
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['kick'])){
			// kick player session
			$sql = "UPDATE game_data_mult SET wh='kick' WHERE session='$session'";
			if(mysqli_query($conn,$sql)){

			}else{
				echo "kick error";
			}
	}
}

// now for leaving
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['leave'])){
			// kick player session
			//$sql = "DELETE FROM game_data_mult WHERE session='$session'";
			//if(mysqli_query($conn,$sql)){
				header("Location: multiplayer.php?session=deleted");
			//}else{
				//echo "kick error";
			//}
	}
}

// copy data over to multiplayer and fetch from there 
// refresh just once here to reload everything 

ob_end_flush();
?>
