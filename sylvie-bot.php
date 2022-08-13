<?php

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
			$sql_machine = "UPDATE game_data SET refer=2 WHERE turn = '$turn' " ;
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
		$sql_machine = "UPDATE game_data SET refer=2 WHERE turn = '$turn' " ;
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
		$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
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
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
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
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
			if (mysqli_query($con, $sql_machine_call)) {
				echo "Machine Called ! ";
				////header("Refresh:0");
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
			$sql_machine_call = "UPDATE game_data SET mCall = '$mAmt',mAmt = '$mQTY' WHERE turn = '$turn' ";
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

// bot ends here  -------------------------------------

?>