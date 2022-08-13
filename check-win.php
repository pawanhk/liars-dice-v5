<?php

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
			$machine_dice--;
			$win_update = "UPDATE game_data SET whoWon=2,machineDice='$machine_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				//header("Refresh:0");
				//echo "Hola - 1 and winner = " . $dec ;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}

		}else{
			$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice' WHERE turn='$turn'";
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
			$player_dice--;
			$win_update = "UPDATE game_data SET whoWon=3,playerDice='$player_dice' WHERE turn='$turn'";
			if (mysqli_query($conn, $win_update)) {
				//echo "Hola -3 and winner = " . $dec;
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
				//echo "Hola 4 and winner = " . $dec;
			}else {
				echo "Error: " . $win_update . "<br>" . mysqli_error($conn);
			}
		}
	}
}