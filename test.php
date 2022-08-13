<?php
ob_start();
// heavy lifting before turn 
include("test_connect.php");

// bot start ---------------------------------------------------
function bot_player(){
	// code for the bot test
// first get all the data for the bot 
 // remove this wile prod 
include("bot-connect.php");
// get data from table 
$get_bot = "SELECT turn,playerDice,machineDice,pRoll,mRoll,mCall,mAmt,callAmt,callQty,refer from game_data ";
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
    echo "assigning error here ";
 }
echo "<h4>Current Machine data : </h4>" ; 
echo "Turn Number: " . $turn . "<br>";
echo "Machine Dice: " . $machineDice . "<br>";
echo "FOR REF playerDice: " . $playerDice . "<br>";
echo "FOR REF player roll : " . $pRoll . "<br>";
echo "Machine Roll: " . $mRoll . "<br>";
echo "<h4>Player call: </h4>" ; 
echo "AMOUNT:  " . $pAmt . "<br>";
echo "QTY:  " . $pQTY . "<br><br>";

// conditions for calling for the bot 
// first get the player dice and player call values 

$total_dice = $playerDice + $machineDice;
echo "Total dice for turn " . $turn . " is: " . $total_dice . "<br>";

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

echo "Matched values: " . $matched_values . "<br>";

// for interm bot change a lot of this code 

if ($dice_qty <= $total_dice  && $dice_amt != 0){
	// first condition: pQTY < P Dice
	if ($dice_qty < $playerDice){
		// here raise 
		echo "Raising amount case 1 <br>" ;
		$can_raise = true;

	}elseif($dice_qty == $playerDice){
		// here we need to call conditionally 
		if($matched_values == 0){
			// call cause mach have no matching values
			echo "Calling <br>";
			echo " it is case 2 equal" ;
			$mach_call = true;
			$check_win = true;
			$sql_machine = "UPDATE game_data SET refer=2 WHERE turn = '$turn' " ;
			if (mysqli_query($con, $sql_machine)) {
				echo "machine challenged <br>" ;
			//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine . "<br>" . mysqli_error($conn);
			}
		}else{
			// no cal so the play will contine
			echo "Raising here case 2 <br>" ;
			echo $matched_values;
			$can_raise = true;
		}
	}elseif($dice_qty > $playerDice && $matched_values <= 3){
		// here we will call 
		echo "Calling <br> " ;
		echo " it is case 3 greater <br>";
		$mach_call = true;
		$check_win = true;
		$sql_machine = "UPDATE game_data SET refer=2 WHERE turn = '$turn' " ;
			if (mysqli_query($con, $sql_machine)) {
				echo "machine challenged <br>" ;
			//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine . "<br>" . mysqli_error($conn);
			}

	}elseif($dice_qty > $playerDice && $matched_values > 3){
		// raise here 
		echo "Raising <br>";
		echo " its case 4 greater with matched values <br> " ;
		$can_raise = true;
	}


}else{
		echo "Dice exceedded or play has just begun " ;
}	

// predicition algo for raising 

if($can_raise == true && $dice_amt != 0){
	// raise here 
	echo " its being raised here <br> " ;
	// conditions for raising 
	// case 1: dice qty less than player dice 
	if($dice_qty < $playerDice && $dice_qty <= $total_dice){
		echo "case one <br>" ;
		// simple raise here 
		$mAmt = $dice_amt;
		$mQTY = $dice_qty + 2;
		echo "Machine Prediction: <br> ";
		echo "Amount: " . $mAmt . "<br> ";
		echo "QTY: " . $mQTY . "<br>";
		// update the table 
		$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
		if (mysqli_query($con, $sql_machine_call)) {
			echo "<br>";
			//header("Refresh:0");
		}else {
			echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
		}
	}
	elseif($dice_qty == $playerDice && $dice_qty <= $total_dice){
		// matched raise here 
		echo "case 2 <br>" ;
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 1;
			echo "Machine Prediction: <br> ";
			echo "Amount: " . $mAmt . "<br> ";
			echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
			if (mysqli_query($con, $sql_machine_call)) {
				echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}else{
			echo " case 2 face change now <br> "; 
		}
	}
	elseif($dice_qty > $playerDice && $matched_values >= 1 && $dice_qty <= ($total_dice - 2)){
		echo "case three <br>";
		// still a simple raise but by 2
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 2;
			echo "Machine Prediction: <br> ";
			echo "Amount: " . $mAmt . "<br> ";
			echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
			if (mysqli_query($con, $sql_machine_call)) {
				echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}else{
			echo " case 3 face change now <br> "; 
		}
	}
	elseif($matched_values >= 5 && $dice_qty <= ($total_dice-3)){
		// definite raise
		echo "case four <br>";
		if ($matched_values >= 1){
			// simple raise
			$mAmt = $dice_amt;
			$mQTY = $dice_qty + 3;
			echo "Machine Prediction: <br> ";
			echo "Amount: " . $mAmt . "<br> ";
			echo "QTY: " . $mQTY . "<br>";
			// update the table 
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
			if (mysqli_query($con, $sql_machine_call)) {
				echo "Machine Called ! ";
				//header("Refresh:0");
			}else {
				echo "Error: " . $sql_machine_call . "<br>" . mysqli_error($conn);
			}
		}
		else{
			echo " case 4 face change now <br> "; 
		}
	}
	else{

		echo "case four <br>" ;
	}
}

// update refer here to make use of it 

// remove these while prod
mysqli_close($con);
}

// bot end -----------------------------------------

// get data from table 
$sql = "SELECT id,turn,playerDice,machineDice,pRoll,mRoll,mCall,mAmt,callAmt,callQty,refer,whoWon from game_data ";
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
    }
  } else {
    echo "assigning error here ";
  }
$playContin = true;

//check win / lose
if($playerDice <= 0){
	// move to loose screen 
	$playContin = false;
	header("Location: you-lose.php");
}elseif($machineDice <= 0){
	// move to win screen 
	header("Location: you-win.php");
	$playContin = false;
}else{
	$playContin = true;
}
$reveal = false;

function check_win($turn,$player_dice,$machine_dice,$player_roll,$machine_roll,$player_amt,$player_qty,$machine_amt,$machine_qty,$caller,$sql,$dec){
	// check the bid 
	// 2-> player 3-> machine 
	$conn = $sql;
	$total_dice_win = $player_dice + $machine_dice;
	if ($caller == 0){
		//echo "Machine challenge ";
		$correct_face_value = $player_amt;
		$correct_face_qty = $player_qty;
		$matchs = 0;
		echo "Matchs: " . $matchs . "<br>";
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
			$machine_dice--;
			$win_update = "UPDATE game_data SET whoWon=2,machineDice='$machine_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				echo "Hola - 1 and winner = " . $dec ;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}

		}else{
			$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				echo "Hola -2 and winner = " . $dec;
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
			echo "matched correct values = " . $matchs ;
			$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				echo "Hola -3 and winner = " . $dec;
				//header("Refresh:0");
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}

		}else{
			// player wins
			$machine_dice--;
			$win_update = "UPDATE game_data SET whoWon=2,machineDice='$machine_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				echo "Hola 4 and winner = " . $dec;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}
		}
	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Testing </title>
	<link rel="stylesheet" type="text/css" href="test.css">
</head>
<body>

	<div class="playground">
		<table class="split">
			<td>
				<div class="sidebar">
					<h1>CONTROLS </h1><br><br>
					<form method="POST">
						<button name="dice" type="submit" > ROLL </button> <br><br>
						<button name="challenge" type="submit"> CHALLENGE  </button> <br><br>
					</form>
					<button name="exit" type="submit"> <a href="index.php"> EXIT </a> </button> <br><br>
					<h3> Raise the stake </h3>
					<form method="POST">
						<table>
							<td>
								<h4> Enter Amount </h4> <br>
								<input type="text" name="amt"><br>
							</td>
							<td>
								<h4> Enter QTY </h4> <br>
								<input type="text" name="qty"><br>
							</td>
						</table>
						<br>
						<button name="raise" type="submit"> raise </button><br><br>
					</form>
					<hr>
					<h3> CHALLENGE  </h3>
					<?php
						// get all the information first 
						if($_SERVER['REQUEST_METHOD'] == 'POST'){
							if(isset($_POST['challenge'])){	
										$reveal = true;
										echo "<h5> MACHINE BID: </h5><br>"; 
										echo "<h5> MAmount: " . $mAmt . "</h5><br>"; 
										echo "<h5> MQTY: " . $mQTY . "</h5></td>";
										// check here if player won or machine won 
										$caller = 1;
										$sql = $conn;
										check_win($turn,$playerDice,$machineDice,$pRoll,$mRoll,$pAmt,$pQTY,$mAmt,$mQTY,$caller,$sql,$dec);

								  } else {
								    echo "challenge error ";
								  }
									
							}

						if($refer == 2){
							// machine call 
							echo "<h4> Machine has called ! </h4><br>";
							$reveal = true;
							echo "<h4> MACHINE BID: </h4><br>"; 
							echo "<h4> MAmount: " . $mAmt . "</h4><br>"; 
							echo "<h4> MQTY: " . $mQTY . "</h4></td>";
							$caller = 0;
							$sql = $conn;
							check_win($turn,$playerDice,$machineDice,$pRoll,$mRoll,$pAmt,$pQTY,$mAmt,$mQTY,$caller,$sql,$dec);

						}
					?>
				</div>
			</td>
			<td>
				<div class="gameplay">
					<h1 align="text-center"> GAMEPLAY </h1>
					<h2>TURN HERE: <?php echo "<h5> " . $turn . "</h5>" ; ?>  </h2>
					<?php
						$roll_sql = "SELECT pRoll,mRoll,playerDice,machineDice,refer FROM game_data";
						$roll_sql_result = mysqli_query($conn, $roll_sql);
						if (mysqli_num_rows($roll_sql_result) > 0) {
				    while($row = mysqli_fetch_assoc($roll_sql_result)) {
							$playerDice = $row['playerDice'];
							$machineDice = $row['machineDice'];
							$pRoll = $row['pRoll'];
							$mRoll = $row['mRoll'];
				    }
				  } else {
				    echo "assigning error here  1";
				  }

					?>
					<h2>YOURE ROLL:  <?php echo "<h5> " . $pRoll . "</h5>" ; ?> </h2>
					<h2>MACHINE ROLL: 
						<?php
							if ($reveal == true){
								// show dice 
								echo "<h5> " . $mRoll . "</h5>" ;
							}else{
								// do not show
								echo "<h5>  Currently Hidden ! </h5>" ;
							}
						?> 
					</h2>
					<h2>PLAYER DICE: <?php echo "<h5> " . $playerDice . "</h5>" ?>  </h2>
					<h2>MACHINE DICE: <?php echo "<h5> " . $machineDice . "</h5>" ?> </h2>
					<hr>
					<table>
						<td>
							<div class="croll">
								<h3> Current Rollv</h3>
								<h4> <?php echo "<h5> " . $pRoll . "</h5>" ; ?> </h4>
							</div>
						</td>
						<td>
							<div class="cbid">
								<h3> Current Bid</h3>
								<h4>  
								<table>
									<?php 
									// player bid 
										echo "<td><h5> Player bid: </h5><br>" ;
										echo "<h5> PAmount: " . $pAmt . "</h5><br>"; 
										echo "<h5> PQTY: " . $pQTY . "</h5></td>"; 
									// machine bid
										echo "<td><h5> Machine bid: </h5><br>" ;
										echo "<h5> MAmount: " . $mAmt . "</h5><br>"; 
										echo "<h5> MQTY: " . $mQTY . "</h5></td>";
									?>
								</table>
								</h4>
							</div>
						</td>
					</table>
					<hr>
				</div>
			</td>
		</table>
		<br><br>
		<hr>
		<br><br>
		<form method="POST">
			<button type="submit" name="turn">Next Turn </button>
		</form>
		<br><br><br>
	</div>

	<h1> Commetary: </h1>
	<?php
	// functions 
	// heavy lifting after rolled 
	// get win_value 
			// get dec value 
	// get current dice values 
	$get_curr_dice = "SELECT playerDice,machineDice FROM game_data WHERE turn='$turn'";
	$get_curr_dice_sql = mysqli_query($conn, $get_curr_dice);
	if (mysqli_num_rows($get_curr_dice_sql) > 0) {
	    while($row = mysqli_fetch_assoc($result)) {
			$playerDice = $row['playerDice'];
			$machineDice = $row['machineDice'];
	    }
	 }else {
	    echo "assigning error here rolling side  ";
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
						echo "rolling  ";
						$dice_roll_player = rollDicePlayer($curr_player_dice);// array with roll 
						$dice_roll_machine = rollDiceMachine($curr_mach_dice);// array with roll 
						// convert back to string 
						$st_roll_player = implode("",$dice_roll_player);
						$st_roll_machine = implode("",$dice_roll_machine);
						$insert_roll = "UPDATE game_data SET pRoll = '$st_roll_player', mRoll='$st_roll_machine' WHERE turn='$turn'" ;
						//$insert_roll = "INSERT INTO game_data (turn,pRoll,mRoll,refer) VALUES ('$turn','$st_roll_player','$st_roll_machine','$turn')"; 
						echo "Dice has been rolled ! ";	
						if (mysqli_query($conn, $insert_roll)) {
								echo "Dice has been rolled ! ";
								header("Refresh:0");
						  	}else {
								echo "Error: " . $insert_roll . "<br>" . mysqli_error($conn);
						  	}
					}else{
						echo "Turn complete cannot roll " ;
					}
				}
				$partA = 1;
			}
		// amt raising 
		// only if dice is rolled 
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(isset($_POST['raise'])){	
					echo "raising amount <br>  ";
					$db_pAmt = $_POST['amt'];
					$db_pQty = $_POST['qty'];
					echo "Amount: " . $db_pAmt . "<br>";
					echo "QTY:  " . $db_pQty . "<br>";
					// insert into table 
					// first get the existing values so the data can be copied 
					$get_rolls_call = "SELECT pRoll,mRoll FROM game_data ";
					$rolls_call_result = mysqli_query($conn, $get_rolls_call);
					if (mysqli_num_rows($rolls_call_result) > 0) {
			    while($row = mysqli_fetch_assoc($rolls_call_result)) {
			    	// if needed add more here
					$pRoll = $row['pRoll'];
					$mRoll = $row['mRoll'];
			    }
			  } else {
			    echo "assigning error here ";
			  }
			  	$insert_call = "UPDATE game_data SET callAmt='$db_pAmt', callQty='$db_pQty' WHERE turn='$turn'";
					//$insert_call = "INSERT INTO game_data (turn,callAmt,callQty,pRoll,mRoll,refer) VALUES ('$turn','$db_pAmt','$db_pQty','$pRoll','$mRoll','$turn')"; 
						if (mysqli_query($conn, $insert_call)) {
						echo "Amount called  ! ";
						header("Refresh:0");
					  }else {
						echo "Error: " . $insert_call . "<br>" . mysqli_error($conn);
					  }
				}
				$partC = 1;
			}

			// call the bot 
			bot_player();

		// turn changing 
		// stuff here only if the turn button is presed 
			$get_winner = "SELECT whoWon FROM game_data WHERE turn='$turn'";
			$get_winner_sql = mysqli_query($conn, $get_winner);
			if (mysqli_num_rows($get_winner_sql) > 0) {
	    		while($row = mysqli_fetch_assoc($result)) {
					$dec = $row['whoWon'];
	    		}
	 		}else {
	    		echo "assigning error here decide side  ";
	 		}
	 		echo "The winner is ..." . $dec . "<br>";
		echo "curr player dice: " . $playerDice . "<br>";
		echo "curr mach dice: " . $machineDice . "<br>";
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(isset($_POST['turn'])){	
					// call the next turn by raising the turn number 
					$turn++;
					$turn_update = "INSERT INTO game_data (turn,playerDice,machineDice) VALUES ('$turn','$playerDice','$machineDice')"; 
					echo "curr player dice: " . $playerDice . "<br>";
					echo "curr mach dice: " . $machineDice . "<br>";
					if (mysqli_query($conn, $turn_update)) {
								echo "Moved to next turn  ! ";
								header("Refresh:0");
					}else{
								echo "Error: " . $turn_update . "<br>" . mysqli_error($conn);
					}
				}else{
						echo "Turn complete cannot roll " ;
				}
				if ($partA == 0 || $partC == 0){
					echo "<h1> Not all option complete </h1>" ;
				}else{
					// move the next turn 
				}
			}
		}
		
	?>

<?php
mysqli_close($conn);
ob_end_flush();
?>


</body>
</html>