<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();
// heavy lifiting before 
// first connect to database 
include "connect.php";


if($_GET['session']){
	$session = $_GET['session'];
	$_SESSION['ses'] = $session;
}

$sql = "SELECT * FROM game_data WHERE session='$session'";
$result = mysqli_query($conn,$sql);
if(mysqli_num_rows($result) > 0 ){
	// do nothing 
	while($row = mysqli_fetch_assoc($result)){
		$session = $row['session'];
	}
}else{
	$make = "INSERT INTO game_data (turn,playerDice,machineDice,callAmt,callQty,pRoll,mRoll,WH,mCall,mAmt,refer,whoWon,session) VALUES (1,5,5,0,0,'00000','00000','NNN',0,0,0,0,$session)";
	// update the table 
	if (mysqli_query($conn, $make)) {
		//echo "Machine Called ! ";
		//header("Refresh:0");
	}else {
		echo "Error: " . $make . "<br>" . mysqli_error($conn);
	}

}

$usrr = $_SESSION['username'];
$sql = "UPDATE game_data SET user='$usrr' WHERE session='$session'";
mysqli_query($conn,$sql);

// bot functions 
// bot start ---------------------------------------------------
function bot_player($session){
	$session=$_SESSION['ses'];
	// code for the bot test
// first get all the data for the bot 
 // remove this wile prod 
	//echo $session;
include("bot-connect.php");
// get data from table 
$get_bot = "SELECT turn,playerDice,machineDice,pRoll,mRoll,mCall,mAmt,callAmt,callQty,refer from game_data WHERE session='$session' ";
$result = mysqli_query($con, $get_bot);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		$playerDice = $row['playerDice'];
		$machineDice = $row['machineDice'];
		$turn = $row['turn'];
		$pRoll = $row['pRoll'];
		$mRoll = $row['mRoll'];
		$pAmt = $row['callAmt']; // face number 
		$pQTY = $row['callQty']; // amount /qty
		$mAmt = $row['mCall']; // face numnber
		$mQTY = $row['mAmt']; // amount /qty
    }
 }else {
    //echo "assigning error here ";
 }

 /*
echo "<h4>Current Machine data : </h4>" ; 
echo "Turn Number: " . $turn . "<br>";
echo "Machine Dice: " . $machineDice . "<br>";
echo "FOR REF playerDice: " . $playerDice . "<br>";
echo "FOR REF player roll : " . $pRoll . "<br>";
echo "Machine Roll: " . $mRoll . "<br>";
echo "<h4>Player call: </h4>" ; 
echo "AMOUNT:  " . $pAmt . "<br>";
echo "QTY:  " . $pQTY . "<br><br>";
*/
// conditions for calling for the bot 
// first get the player dice and player call values 

$total_dice = $playerDice + $machineDice;
//echo "Total dice for turn " . $turn . " is: " . $total_dice . "<br>";

// let the machine decide a face value and amount 

// get array of number for roll 
$m_arr_roll = [];
for ($face = 0; $face<$machineDice; $face++){
	$m_arr_roll[$face] = $mRoll[$face];
}

$dice_amt = $pAmt; // face value
$dice_qty = $pQTY; // number of dices with that face value 


$can_raise = false;
$check_win = false;
// prediction algo for calling 
$matched_values = 0;
	// assuming no one calls face value one 
	for($x = 0; $x < $machineDice; $x++){
		if($m_arr_roll[$x] == $dice_amt || $m_arr_roll[$x] == 1 ){
			$matched_values++;
		}
	}

//echo "Matched values: " . $matched_values . "<br>";

//4-> machine calls case 2

// for interm bot change a lot of this code 
if ($dice_qty <= $total_dice  && $dice_amt != 0){
	// first condition: pQTY < P Dice
	if ($dice_qty < $playerDice){
		// here raise 
		//echo "Raising amount case 1 <br>" ;
		$can_raise = true;

	}elseif($dice_qty == $playerDice){
		// here we need to call conditionally 
		if($matched_values == 0){
			// call cause mach have no matching values
			//echo "Calling <br>";
			//echo " it is case 2 equal" ;
			$mach_call = true;
			$check_win = true;
			$sql_machine = "UPDATE game_data SET refer=2,WH='MC' WHERE turn = '$turn' AND session='$session'" ;
			if (mysqli_query($con, $sql_machine)) {
				//echo "machine challenged <br>" ;
			//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine . "<br>" . mysqli_error($conn);
			}
		}else{
			// no cal so the play will contine
			//echo "Raising here case 2 <br>" ;
			//echo $matched_values;
			$can_raise = true;
		}
	}elseif($dice_qty > $playerDice && $matched_values <= 3){
		// here we will call 
		//echo "Calling <br> " ;
		//echo " it is case 3 greater <br>";
		$mach_call = true;
		$check_win = true;
		$sql_machine = "UPDATE game_data SET refer=2,WH='MC' WHERE turn = '$turn' AND session='$session' " ;
			if (mysqli_query($con, $sql_machine)) {
				//echo "machine challenged <br>" ;
			//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine . "<br>" . mysqli_error($conn);
			}

	}elseif($dice_qty > $playerDice && $matched_values > 3){
		// raise here 
		//echo "Raising <br>";
		//echo " its case 4 greater with matched values <br> " ;
		$can_raise = true;
	}


}else{
		//echo "Dice exceedded or play has just begun " ;
}	

// predicition algo for raising 

//cupt-5 -> for machine raising

if($can_raise == true && $dice_amt != 0){
	// raise here 
	//echo " its being raised here <br> " ;
	// conditions for raising 
	// case 1: dice qty less than player dice 
	if($dice_qty < $playerDice && $dice_qty <= $total_dice){
		//echo "case one <br>" ;
		// simple raise here 
		$mAmt = $dice_amt;
		$mQTY = $dice_qty + 2;
		//echo "Machine Prediction: <br> ";
		//echo "Amount: " . $mAmt . "<br> ";
		//echo "QTY: " . $mQTY . "<br>";
		// update the table 
		$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY',curr_amt='$mAmt',curr_qty='$mQTY' WHERE turn = '$turn' AND session='$session' ";
		if (mysqli_query($con, $sql_machine_call)) {
			//echo "<br>";
			//header("Refresh:0");
		}else {
			echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
		}
	}
	elseif($dice_qty == $playerDice && $dice_qty <= $total_dice){
		// matched raise here 
		//echo "case 2 <br>" ;
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 1;
			//echo "Machine Prediction: <br> ";
			//echo "Amount: " . $mAmt . "<br> ";
			//echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY', curr_amt='$mAmt',curr_qty='$mQTY' WHERE turn = '$turn' AND session='$session' ";
			if (mysqli_query($con, $sql_machine_call)) {
				//echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}else{
			//echo " case 2 face change now <br> "; 
		}
	}
	elseif($dice_qty > $playerDice && $matched_values >= 1 && $dice_qty <= ($total_dice - 2)){
		//echo "case three <br>";
		// still a simple raise but by 2
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 2;
			//echo "Machine Prediction: <br> ";
			//echo "Amount: " . $mAmt . "<br> ";
			//echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' ,curr_amt='$mAmt',curr_qty='$mQTY' WHERE turn = '$turn' AND session='$session' ";
			if (mysqli_query($con, $sql_machine_call)) {
				//echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}else{
			//echo " case 3 face change now <br> "; 
		}
	}
	elseif($matched_values >= 5 && $dice_qty <= ($total_dice-3)){
		// definite raise
		//echo "case four <br>";
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 3;
			//echo "Machine Prediction: <br> ";
			//echo "Amount: " . $mAmt . "<br> ";
			//echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' ,curr_amt='$mAmt',curr_qty='$mQTY' WHERE turn = '$turn' AND session='$session' ";
			if (mysqli_query($con, $sql_machine_call)) {
				//echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}
		else{
			//echo " case 4 face change now <br> "; 
		}
	}
	else{
		//echo "case four <br>" ;
	}
}

// update refer here to make use of it 
// remove these while prod
mysqli_close($con);
}

// check the win conditions 
$sql = "SELECT id,turn,playerDice,machineDice,pRoll,mRoll,mCall,mAmt,callAmt,callQty,refer,whoWon,rtime,rtime2 from game_data WHERE session='$session' ";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
		$playerDice = $row['playerDice'];
		$machineDice = $row['machineDice'];
		$turn = $row['turn'];
		$pRoll = $row['pRoll'];
		$mRoll = $row['mRoll'];
		$pAmt = $row['callAmt']; // face number 
		$pQTY = $row['callQty']; // amount 
		$mAmt = $row['mCall']; // face numnber
		$mQTY = $row['mAmt']; // amount 
		$refer = $row['refer'];
		$dec = $row['whoWon'];
		$rtime = $row['rtime'];
		$rtime2 = $row['rtime2'];
		$wh = $row['wh'];
		echo $wh;
    }
  } else {
    echo "assigning error here ";
  }
$playContin = true;
//check win / lose
if($playerDice <= 0){
	// move to loose screen 
	$playContin = false;
	// update the table and move the neccessary info to a new table 
	$get_rid = "DELETE FROM game_data WHERE session='$session'";
	// make the new record 
	$session = rand(1000,10000);
	$reset = "INSERT INTO game_data (turn,playerDice,machineDice,callAmt,callQty,pRoll,mRoll,WH,mCall,mAmt,refer,whoWon,session) VALUES (1,5,5,0,0,'00000','00000','NNN',0,0,0,0,$session)";
	// update the table 
	if (mysqli_query($conn, $get_rid)) {
		//echo "Machine Called ! ";
		//header("Refresh:0");
	}else {
		echo "Error: " . $get_rid . "<br>" . mysqli_error($conn);
	}
	// update the table again 
	if (mysqli_query($conn, $reset)) {
		//echo "Machine Called ! ";
		//header("Refresh:0");
	}else {
		echo "Error: " . $reset . "<br>" . mysqli_error($conn);
	}
	header("Location: you-lose.php");
}elseif($machineDice <= 0){
	// move to win screen 
	// update the table and move the neccessary info to a new table 
	$get_rid = "DELETE FROM game_data";
	// make the new record 
	$reset = "INSERT INTO game_data (turn,playerDice,machineDice,callAmt,callQty,pRoll,mRoll,WH,mCall,mAmt,refer,whoWon,pws,mws,session) VALUES (1,5,5,0,0,'00000','00000','NNN',0,0,0,0,0,0,$session)";
	// update the table 
	if (mysqli_query($conn, $get_rid)) {
		//echo "Machine Called ! ";
		//header("Refresh:0");
	}else {
		echo "Error: " . $get_rid . "<br>" . mysqli_error($conn);
	}
	// update the table again 
	if (mysqli_query($conn, $reset)) {
		//echo "Machine Called ! ";
		//header("Refresh:0");
	}else {
		echo "Error: " . $reset . "<br>" . mysqli_error($conn);
	}
	header("Location: you-win.php");
	$playContin = false;
}else{
	$playContin = true;
}
$reveal = false;

function check_win($turn,$player_dice,$machine_dice,$player_roll,$machine_roll,$player_amt,$player_qty,$machine_amt,$machine_qty,$caller,$sql,$dec,$session){
	// check the bid 
	// 2-> player 3-> machine 
	$conn = $sql;
	$total_dice_win = $player_dice + $machine_dice;
	if ($caller == 0){
		//echo "Machine challenge ";
		$correct_face_value = $player_amt;
		$correct_face_qty = $player_qty;
		$matchs = 0;
		//echo "Matchs: " . $matchs . "<br>";
		for ($x = 0; $x < $player_dice; $x++){
			if ($correct_face_value == $player_roll[$x] || $player_roll[$x] == 1){
				$matchs++;
			}
		}
		for ($y = 0; $y < $machine_dice; $y++){
			if ($correct_face_value == $machine_roll[$y] || $machine_roll[$y] == 1){
				$matchs++;
			}
		}
		if($matchs >= $correct_face_qty){
			// player wins
			// update the table
			//$machine_dice--; 
			$win_update = "UPDATE game_data SET whoWon=2,machineDice='$machine_dice' WHERE turn='$turn' AND session='$session'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				//echo "Hola - 1 and winner = " . $dec ;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}

		}else{
			//$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice' WHERE turn='$turn' AND session='$session'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				//echo "Hola -2 and winner = " . $dec;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}
		}
	}
	elseif($caller == 1){
		//echo "Player challenge ";
		$correct_face_value = $machine_amt;
		$correct_face_qty = $machine_qty;
		$matchs = 0;
		for ($x = 0; $x < $player_dice; $x++){
			if ($correct_face_value == $player_roll[$x] || $player_roll[$x] == 1 ){
				$matchs++;
			}
		}
		for ($y = 0; $y < $machine_dice; $y++){
			if ($correct_face_value == $machine_roll[$y] || $machine_roll[$y] == 1 ){
				$matchs++;
			}
		}
		if($matchs >= $correct_face_qty){
			// machine wins 
			// update the table
			//echo "matched correct values = " . $matchs ;
			//$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice',WH='PC' WHERE turn='$turn' AND session='$session'";
			if (mysqli_query($conn, $win_update)) {
				//echo "Hola -3 and winner = " . $dec;
				//header("Refresh:0");
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}

		}else{
			// player wins
			//$machine_dice--;
			$win_update = "UPDATE game_data SET whoWon=2,machineDice='$machine_dice',WH='PC' WHERE turn='$turn' AND session='$session'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				//echo "Hola 4 and winner = " . $dec;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}
		}
	}
}


?>



<?php
// do login verification here
$username = $_SESSION['username'];
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
	<div id="reactionTime"></div>
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
							<h1 class="main-header"> Controls  </h1><br>
							<?php // main button controls here ?>
							<div class="main-control">
								<form method="POST">
									<button name="dice" type="submit" class="btn btn-success">Roll Dice</button><br><br>
									<button name="challenge" type="submit" class="btn btn-success">Challenge </button><br><br>
								</form>
								<form method="POST">
								<button name = "giveup" type="submit" class="btn btn-danger">Reset </button><br><br>
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
									<button name="raise" type="submit" class="btn btn-success">Raise </button><br><br>
								</div>
							</form>
							<form method="POST">
								<button name="turn" type="submit" class="btn btn-secondary btn-lg">Next Turn </button>
							</form>
						</td>
					</div>
					<div id="reactionTime"> </div>
					<div class="sep-line"></div>
					<div class="col-2">
						<td class="move-over">
							<h1 class="main-header"> Turn: <?php echo $turn ?> </h1><br>
							<?php 
							$roll_sql = "SELECT pRoll,mRoll,playerDice,machineDice,refer FROM game_data WHERE session='$session'";
							$roll_sql_result = mysqli_query($conn, $roll_sql);
							if (mysqli_num_rows($roll_sql_result) > 0) {
				    		while($row = mysqli_fetch_assoc($roll_sql_result)) {
									$playerDice = $row['playerDice'];
									$machineDice = $row['machineDice'];
									$pRoll = $row['pRoll'];
									$mRoll = $row['mRoll'];
				    		}
				  		}else {
				    		echo "assigning error here  1";
				  		}
						?>
							<div class="machine-info">
								<table class="mach-table">
									<td> 
										<table>
									<?php
									// get all the information first 
									if($_SERVER['REQUEST_METHOD'] == 'POST'){
										if(isset($_POST['challenge'])){	
													$reveal = true;
													$_SESSION['rev'] = true;
													//echo "<h5> MACHINE BID: </h5><br>"; 
													//echo "<h5> MAmount: " . $mAmt . "</h5><br>"; 
													//echo "<h5> MQTY: " . $mQTY . "</h5></td>";
													// check here if player won or machine won 
													$caller = 1;
													$sql = $conn;
													$session = $_SESSION['ses'];
													check_win($turn,$playerDice,$machineDice,$pRoll,$mRoll,$pAmt,$pQTY,$mAmt,$mQTY,$caller,$sql,$dec,$session);
													$out2 = true;
													$_SESSION['time2'] = floor(microtime(true) * 1000);

											  } else {
											    //echo "challenge error ";
											  }
												
										}
										if($_SERVER['REQUEST_METHOD'] == 'POST'){
											if(isset($_POST['giveup'])){
												// update the table 
												$get_rid_new = "DELETE FROM game_data WHERE session='$session'";
													// make the new reord 
												//$reset_new = "INSERT INTO game_data (turn,playerDice,machineDice,callAmt,callQty,pRoll,mRoll,WH,mCall,mAmt,refer,whoWon,pws,mws,frtime,session) VALUES (1,5,5,0,0,'00000','00000','NNN',0,0,0,0,0,0,0,0)";
												$reset_new = "UPDATE game_data SET turn=0,playerDice=5,machineDice=5,callAmt=0,callQty=0,pRoll=00000,mRoll=00000,WH='NNN',mCall=0,mAmt=0,refer=0,whoWon=0,pws=0,mws=0,frtime=0 WHERE session='$session'";
												$movenext = false;
												$move_next = false;
													// update the table 
												if (mysqli_query($conn, $get_rid_new)) {
														//echo "Machine Called ! ";
														//header("Refresh:0");
													$movenext = true ;
												}else {
														//echo "Error: " . $get_rid_new . "<br>" . mysqli_error($conn);
												}
													// update the table again 
												if (mysqli_query($conn, $reset_new)) {
														//echo "Machine Called ! ";
														//header("Refresh:0");
														$move_next = true;
												}else {
														//echo "Error: " . $reset_new . "<br>" . mysqli_error($conn);
													}
												if($move_next == true && $movenext == true){
														header("Refresh:0");
												}
											} else {
														  //escho "giveup error ";
											}		
										}



									if($refer == 2){
										// machine call 
										//echo "<h4> Machine has called ! </h4><br>";
										$reveal = true;
										//echo "<h4> MACHINE BID: </h4><br>"; 
										//echo "<h4> MAmount: " . $mAmt . "</h4><br>"; 
										//echo "<h4> MQTY: " . $mQTY . "</h4></td>";
										$caller = 0;
										$sql = $conn;
										$session = $_SESSION['ses'];
										check_win($turn,$playerDice,$machineDice,$pRoll,$mRoll,$pAmt,$pQTY,$mAmt,$mQTY,$caller,$sql,$dec,$session);
									}	
									?>
									<?php
										// get the values to confirm and get winner values 
										$dice_confirm = "SELECT playerDice,machineDice,whoWon,pws,mws,curr_amt,curr_qty,rtime,rtime2,session FROM game_data WHERE session='$session'";
										$sql_dice_confirm= mysqli_query($conn, $dice_confirm);
										if (mysqli_num_rows($sql_dice_confirm) > 0) {
				    					while($row = mysqli_fetch_assoc($sql_dice_confirm)) {
												$playerDice = $row['playerDice'];
												$machineDice = $row['machineDice'];
												$cdec = $row['whoWon'];
												$win_counter_player = $row['pws'];
												$win_counter_machine = $row['mws'];
												$curr_bid_amt = $row['curr_amt'];
												$curr_bid_qty = $row['curr_qty'];
												$session = $row['session'];
				    					}
				  					}else {
				    					//echo "assigning error here  1";
				  					}



				  					// count win streaks

				  					$last_turn = $turn - 1;

				  					$roll_get = "SELECT pRoll,mRoll,pws,mws,frtime FROM game_data WHERE turn='$last_turn' AND session='$session' ";
				  					if (mysqli_query($conn, $roll_get)) {
										while($row = mysqli_fetch_assoc($sql_dice_confirm)) {
												$win_counter_player = $row['pws'];
												$win_counter_machine = $row['mws'];
												$last_player_roll = $row['pRoll'];
												$last_machine_roll = $row['mRoll'];
												$frtime = $row=['frtime'];
				    					}
									}else {
										echo "Error: " . $roll_get . "<br>" . mysqli_error($conn);
									}
				  					
									$reduce_p = false;
									$reduce_m = false;
				  					if($cdec == 2){
				  						$win_counter_player++;
				  						$reduce_p = true;
				  					}elseif($cdec == 3){
				  						$win_counter_machine++;
				  						$reduce_m = true;
				  					}

				  					// update the table 
				  					//$win_update_streak = "UPDATE game_data SET pws='$win_counter_player',mws='$win_counter_machine' WHERE turn='$turn'";
									//if (mysqli_query($conn, $win_update_streak)) {
										// nothing to do really 
										//$hasDone = true;
									//}else {
										//echo "Error: " . $win_update_streak . "<br>" . mysqli_error($conn);
									//}
									?>
										<h2> Sylvie </h2> 
										<br>
										</table>
										<h3 > Dice's Remaining: <?php echo "<span class='dice-rem'>".   $machineDice . "</span" ?>  </h3>
										<h3> Win streaks: <?php echo "<span class='dice-rem'>".   $win_counter_machine . "</span" ; ?> </h3>
										<br><br>
										<div class="player-info">
											<h2> <?php 
											if($username){
											echo $username;
											}else{
												echo "Player";
											}
											?> 											</h2> <br>
											<h3> Dice's Remaining: <?php echo "<span class='dice-rem'>".   $playerDice . "</span" ?> </h3>
											<h3> Win streaks: <?php echo "<span class='dice-rem'>".   $win_counter_player . "</span" ?>  </h3>
											<br> 
										</div>
										<div class="comment">
											<br><br>
											<?php
											$sql = "SELECT wh FROM game_data WHERE session='$session' AND turn='$turn'";
											$result = mysqli_query($conn,$sql);
											if(mysqli_num_rows($result) > 0){
												while($row = mysqli_fetch_assoc($result)){
													$wh = $row['wh'];
												}
											}
											if($wh == "PC"){
												echo $username . " has called ! ";
											}elseif($wh == "MC"){
												echo "Sylvie has called ! " ;
											}
											if($cdec == 0){
												echo " Commentary: game running ...  <br><br>";
											}elseif($cdec == 2){
												echo " " . $username . " Wins the round ! <br>" ;
											}elseif($cdec == 3){
												echo "Sylvie wins the round ! <br>" ;
											}
											?>
										</div>
									</td> 
									<td class="move-ver">
										<h2> Machine Roll </h2><br>
										<?php
												$p_array = [0,0,0,0,0];
												for($x=0;$x<$playerDice;$x++){
													$p_array[$x] = $pRoll[$x];
												}
												$m_array = [0,0,0,0,0];
												for($y=0;$y<$machineDice;$y++){
													$m_array[$y] = $mRoll[$y];
												}
												$_SESSION['m_array_n'] = $m_array;
										?>

										<?php
									$sql = "SELECT rtime,rtime2,frtime FROM game_data WHERE turn='$turn' AND session='$session'";
									$result = mysqli_query($conn,$sql);
									if(mysqli_num_rows($result)){
										while($row = mysqli_fetch_assoc($result)){
											$rtime = $row['rtime'];
											$rtime2 = $row['rtime2'];
											$frtime = $row['frtime'];
										}
									}		
									if($reveal == true || $rtime2!=0){
											include "snippets/show_machine_roll_rev.php";
										}else{
											include "snippets/show_machine_roll.php";
										}
										?>
										<br><br><br><br> 
										<h2> Player Roll </h2><br>
										<div class="container">
										  <div class="row">
										    <div class="col-sm">
										      <h4> <?php echo $p_array[0] ?> </h4>
										    </div>
										    <div class="col-sm">
										      <h4> <?php echo $p_array[1] ?> </h4>
										    </div>
										    <div class="col-sm">
										     	<h4> <?php echo $p_array[2] ?> </h4>
										    </div>
										    <div class="col-sm">
										      <h4> <?php echo $p_array[3] ?> </h4>
										    </div>
										    <div class="col-sm">
										      <h4> <?php echo $p_array[4] ?> </h4>
										    </div>
										  </div>
										</div>
									</td>
									<td class="move-er">
										<h2> Current Bid </h2><br>
										<h3> Face Value: <?php echo "<span class='m-bid'>".   $curr_bid_amt . "</span" ?> </h3>
										<h3> Amount: <?php echo "<span class='m-bid'>".   $curr_bid_qty . "</span" ?> </h3><br><br><br>

										<h2> Player Last Bid  </h2><br>
										<h3> Face Value: <?php echo "<span class='m-bid'>".   $pAmt . "</span" ?> </h3>
										<h3> Amount: <?php echo "<span class='m-bid'>".   $pQTY . "</span" ?> </h3>
										<h3>
											Reaction Time: 
											<?php
											$irtime = (int)$rtime;
											$irtime2 = (int)$rtime2;
											if($irtime && $irtime2){
												$frtime = $irtime2 - $irtime;
												$sql = "UPDATE game_data SET frtime='$frtime' WHERE turn='$turn' AND session='$session'";
												if(mysqli_query($conn,$sql)){
													//header("Refresh:0");
													$_SESSION['frtime'] = $frtime;
												}
												echo $_SESSION['frtime'] . " ms";
											}
											else{
												$_SESSION['frtime'] = 0;
												echo "0 ms";
											}
											?>
										</h3><br><br><br>
									</td>
								</table>
							</div>
						</td>
					</div>
			</div>
		</table>
	</div>
	<br><br><br><br><br><br><br><br><br><br><br><br>
	<!-- Footer -->
	<footer class="text-center text-lg-start bg-light text-muted mt-auto">
  <div class="text-center p-4">
  	<span class="move-down">
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">Privacy Policy </span> </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">Contact  </span> </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">FaQ's </span>  </a>
    <a class="text-reset fw-bold" href="#"><span class="footer-elms">P23Productions </span>  </a>
    </span>

  </div>
</footer>
<?php
// heavy lifting after rolling 
//header("Refresh:10");
$get_curr_dice = "SELECT playerDice,machineDice FROM game_data WHERE turn='$turn' AND session='$session'";
	$get_curr_dice_sql = mysqli_query($conn, $get_curr_dice);
	if (mysqli_num_rows($get_curr_dice_sql) > 0) {
	    while($row = mysqli_fetch_assoc($result)) {
			$playerDice = $row['playerDice'];
			$machineDice = $row['machineDice'];
	    }
	 }else {
	    //echo "assigning error here rolling side  ";
	 }
	function rollDicePlayer($curr_playerDice){
		// random number gen loop through the roll
		// splice the string into an array
		$arrdice = str_split($curr_playerDice);
		for($side = 0; $side < $curr_playerDice; $side++){
			$arrdice[$side] = rand(1,6);
		}
		return $arrdice;
	}
	function rollDiceMachine($curr_machineDice){
		$arrdice = str_split($curr_machineDice);
		for($side = 0; $side < $curr_machineDice; $side++){
			$arrdice[$side] = rand(1,6);
		}
		return $arrdice;
	}

	if($playContin == true){
		$hasCompletedTurn = false;
		$partA = 0;
		$partB = 0;
		$partC = 0;
		$curr_player_dice = $playerDice;
		$curr_mach_dice = $machineDice;
		// rolling and calling
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(isset($_POST['dice'])){
					if ($hasCompletedTurn == false){	
						//echo "rolling  ";
						$dice_roll_player = rollDicePlayer($curr_player_dice);// array with roll 
						$dice_roll_machine = rollDiceMachine($curr_mach_dice);// array with roll 
						// convert back to string 
						$st_roll_player = implode("",$dice_roll_player);
						$st_roll_machine = implode("",$dice_roll_machine);
						$session = $_SESSION['ses'];
						$insert_roll = "UPDATE game_data SET pRoll = '$st_roll_player', mRoll='$st_roll_machine' WHERE turn='$turn' AND session='$session' " ;
						//$insert_roll = "INSERT INTO game_data (turn,pRoll,mRoll,refer) VALUES ('$turn','$st_roll_player','$st_roll_machine','$turn')"; 
						//echo "Dice has been rolled ! ";	
						if (mysqli_query($conn, $insert_roll)) {
								//echo "Dice has been rolled ! ";
								header("Refresh:0");
						  	}else {
								echo "Error: " . $insert_roll . "<br>" . mysqli_error($conn);
						  	}
					}else{
						//echo "Turn complete cannot roll " ;
					}
				}
				$partA = 1;
			}
		// amt raising 
		// only if dice is rolled 
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(isset($_POST['raise'])){	
					//echo "raising amount <br>  ";
					$db_pAmt = $_POST['amt'];
					$db_pQty = $_POST['qty'];
					$session = $_SESSION['ses'];
					//echo "Amount: " . $db_pAmt . "<br>";
					//echo "QTY:  " . $db_pQty . "<br>";
					// insert into table 
					// first get the existing values so the data can be copied 
					$get_rolls_call = "SELECT pRoll,mRoll FROM game_data WHERE session='$session' ";
					$rolls_call_result = mysqli_query($conn, $get_rolls_call);
					if (mysqli_num_rows($rolls_call_result) > 0) {
			    while($row = mysqli_fetch_assoc($rolls_call_result)) {
			    	// if needed add more here
					$pRoll = $row['pRoll'];
					$mRoll = $row['mRoll'];
			    }
			  } else {
			    //echo "assigning error here ";
			  }
			  	$insert_call = "UPDATE game_data SET callAmt='$db_pAmt', callQty='$db_pQty',curr_amt='$db_pAmt',curr_qty='$db_pQty' WHERE turn='$turn' AND session='$session' ";
					//$insert_call = "INSERT INTO game_data (turn,callAmt,callQty,pRoll,mRoll,refer) VALUES ('$turn','$db_pAmt','$db_pQty','$pRoll','$mRoll','$turn')"; 
						if (mysqli_query($conn, $insert_call)) {
						//echo "Amount called  ! ";
						header("Refresh:0");
					  }else {
						echo "Error: " . $insert_call . "<br>" . mysqli_error($conn);
					  }
				$out = true;
				$_SESSION['time1'] = floor(microtime(true) * 1000);
				}
				$partC = 1;
			}
			// call the bot 
			bot_player($session);
			$session = $_SESSION['ses'];
			if($out==true){
				$time1 = $_SESSION['time1'];
				$stime1 = (string)$time1;
				//echo $time1 . "<br>";
				$sql = "UPDATE game_data SET rtime='$stime1' WHERE turn='$turn' AND session='$session'";
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
				$sql = "UPDATE game_data SET rtime2='$stime2' WHERE turn='$turn' AND session='$session'";
				if(mysqli_query($conn,$sql)){
					header("Refresh:0");
					//$reveal = true;
				}else{
					echo "time error ";
				}
			}
		// turn changing 
		// stuff here only if the turn button is presed 
			$get_winner = "SELECT whoWon,playerDice,machineDice FROM game_data WHERE turn='$turn' AND session='$session'";
			$get_winner_sql = mysqli_query($conn, $get_winner);
			if (mysqli_num_rows($get_winner_sql) > 0) {
	    		while($row = mysqli_fetch_assoc($result)) {
					$dec = $row['whoWon'];
					$playerDice = $row['playerDice'];
					$machineDice = $row['machineDice'];
	    		}
	 		}else {
	    		//echo "assigning error here decide side  ";
	 		}
	 		//echo "The winner is ..." . $dec . "<br>";
		//echo "curr player dice: " . $playerDice . "<br>";
		//echo "curr mach dice: " . $machineDice . "<br>";
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(isset($_POST['turn'])){	
					// call the next turn by raising the turn number 
					$turn++;
					/*
					if($win_counter_player != 0 && $reduce_p == true){
						$win_counter_player--;
					}
					if($win_counter_machine != 0 && $reduce_m == true){
						$win_counter_machine--;
					}
					*/

					$turn_update = "INSERT INTO game_data (turn,playerDice,machineDice,pws,mws,session) VALUES ('$turn','5','5','$win_counter_player','$win_counter_machine','$session')"; 
					//echo "curr player dice: " . $playerDice . "<br>";
					//echo "curr mach dice: " . $machineDice . "<br>";
					if (mysqli_query($conn, $turn_update)) {
								//echo "Moved to next turn  ! ";
								header("Refresh:0");
					}else{
								echo "Error: " . $turn_update . "<br>" . mysqli_error($conn);
					}
					// update values again 
				}else{
						//echo "Turn complete cannot roll " ;
				}
				if ($partA == 0 || $partC == 0){
					//echo "<h1> Not all option complete </h1>" ;
				}else{
					// move the next turn 
				}
				// update the new table for safekeeping delete table first 
					 $sql = "DELETE FROM singleplayer" ;
					 mysqli_query($conn,$sql);
					 $sql = "INSERT INTO singleplayer SELECT * FROM game_data";
					 mysqli_query($conn,$sql);
			}
		}



mysqli_close($conn);
ob_end_flush();
?>
</body>
</html>