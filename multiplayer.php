<?php
// do login verification here
error_reporting(E_ALL ^ E_WARNING);
include "connect.php";
session_start(); 
$username = $_SESSION['username'];
ob_start();
if($_GET['kicked'] == "yes"){
	echo "<script> alert('you have been kicked by the host !'); </script>";
}
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
  include "css/mult.css";
   ?>
</style>
<body class="d-flex flex-column min-vh-100" onload="table();">
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
	<div class="multiplayer-options">
		<table>
			<td><h2 class="header"> Liar's Dice </h2></td>
			<td><p class="headerp"> - Multiplayer 6.2.0</p></td>
			<td> <h4 class="headerm"> 
		</h4> 
		<?php 
		$host = $username;
		?>
	</td>
		</table>
		<div class="main-table">
			<table class="main-table-p">
				<td class="left">
					<div class="center">
						<h2 class="headerk"> Create a Game  </h2>
						<form method="POST">
							<h5> liarsdice/<input type="text" name="code" placeholder="enter a game code"> </h5><br>
					<?php 
						if($username){
							echo "<button type='submit' class='btn btn-success' name='create'>Create Game </button><br><br>" ;
						}else{
							echo "<button type='submit' class='btn btn-success btn-lg disabled' name='create'> Create Game </button><br><br>" ;
							echo "<span class='color'>you must be logged in to create games !</span>";
						}


					?>
						</form>
						<?php
						if($_SERVER['REQUEST_METHOD'] == "POST"){
							if(isset($_POST['create'])){
								$roomname = $_POST['code'];
								$creator = $host;
								// create a session var
								$session = rand(1000,9999);
								// first check if room name is avail 	
								$sql = "SELECT * FROM rooms WHERE name='$roomname'";
								$result = mysqli_query($conn,$sql);
								if(mysqli_num_rows($result) > 0){
									// room exists
									echo "roomname is taken, choose another name ... ";
								}else{
									// make that room
									$sql = "INSERT INTO rooms (session,name,creator,user) VALUES ('$session','$roomname','$creator','none') ";
								}
								if(mysqli_query($conn,$sql)){
									// make sessions 
									$_SESSION['creator'] = $creator;
									$_SESSION['session'] = $session;
										// show room 
										echo "room successfully created ! <br> ";
										echo "<span class='joine'><a class='rest' href='host.php'> Enter Room: <span class='linkit'>" . $roomname . "</span></a></span>";
									}else{
										echo "NAD";
									}
							}
						}



						?>
					</div>
				</td>
				<div class="sep-line"></div>
				<td class="right">
					<table>
						<div class="center">
						<h2 class="headerk"> Join a Game  </h2>
						<form method="POST">
							<h5> liarsdice/<input type="text" name="code" placeholder="enter a game code"> </h5><br>
					<?php 
						if($username){
							echo "<button type='submit' class='btn btn-success' name='join'>Join Game </button><br><br>" ;
						}else{
							echo "<button type='submit' class='btn btn-success btn-lg disabled' name='join'> Join Game </button><br><br>" ;
							echo "<span class='color'>you must be logged in to join games !</span>";
						}


					?>
						</form>
						<?php
						if($_SERVER['REQUEST_METHOD'] == "POST"){
							if(isset($_POST['join'])){
								$roomname = $_POST['code'];
								$user = $host;
								// first check if room name is avail 	
								$sql = "SELECT * FROM rooms WHERE name='$roomname'";
								$result = mysqli_query($conn,$sql);
								if(mysqli_num_rows($result) > 0){
									while($row = mysqli_fetch_assoc($result)){
										$session = $row['session'];
										$roomname = $row['name'];
										$created_by = $row['creator'];
									}
									if($user == $created_by){
										$sql = "UPDATE rooms SET creator='$user' WHERE session='$session'";
									if(mysqli_query($conn,$sql)){
										//echo "good";
									}else{
										//echo "no good";
									}
									}
									// insert into db
									$sql = "UPDATE rooms SET user='$user' WHERE session='$session'";
									if(mysqli_query($conn,$sql)){
										//echo "good";
									}else{
										//echo "no good";
									}
									//sessions 
									$_SESSION['user'] = $host;
									$_SESSION['session'] = $session;
									// room exists so join now
									if($username == $created_by){
										echo "<span class='joine'><a class='rest' href='host.php'> Enter Room: <span class='linkit'>" . $roomname . "</span></a></span>";	
									}else{
									echo "room successfully found ! <br> ";
										echo "<span class='joine'><a class='rest' href='player.php'> Enter Room: <span class='linkit'>" . $roomname . "</span></a></span>";
									}
								}else{
									
								}
							}
						}



						?>
					</table>
				</td>
			</table>
		</div>
	</div>


	<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
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

<?php
ob_end_flush();
?>