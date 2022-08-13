<?php
session_start();
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Timer test</title>
</head>
<body>
	<h1> Timer Test </h1>
	<div id="reactionTime"></div>
	<form method="POST">
		<button onclick="onFirstClick()" name="test" type="submit">first button</button>
		<button onclick="onSecondClick()" name="test2" type="submit">second button</button>
	</form>
	<script type="text/javascript">
		var firstClick = null;
		var secondClick = null;

		function onFirstClick() {
		    firstClick = Date.now();
		}

		function onSecondClick() {
		    secondClick = Date.now();
		    showReactionTime();
		}

		function showReactionTime() {
		    if (firstClick && secondClick) { document.getElementById('reactionTime').innerHTML = `<h1>Reaction time: ${secondClick - firstClick}  milliseconds</h1>`;
		    }
		}
	</script>
</body>
</html>

<?php 
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['test'])){
		$_SESSION['time1'] = floor(microtime(true) * 1000);
	}
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(isset($_POST['test2'])){
		$_SESSION['time2'] = floor(microtime(true) * 1000);
	}
}

if($_SESSION['time1'] && $_SESSION['time2']){
	echo $_SESSION['time2'] - $_SESSION['time1'];
	$reset = true;

}else{
	echo "waiting   ";
}


?>