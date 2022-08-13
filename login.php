<?php
error_reporting(E_ALL ^ E_WARNING);
include "connect.php";
ob_start();
session_start();
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
  include "css/admin-login.css";
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
       			 	<a class="nav-link" href="about.php">About</a>
      			</li>
     		    <li class="nav-item">
        			<a class="nav-link" href="signup.php"> signup </a>
      			</li>
      			 <li class="nav-item">
        			<a class="nav-link" href="https://github.com/pawanhk/liars-dice-0.0.2">Version Number: 3.0.0 </a>
      			</li>
    		</ul>
    		</span>
  		</div>
	</nav>

	<div class="main-tab">	 
		<div class="com"> <h2> Login </h2><br> 
			<form method="POST"> 
				<div class="form-group"> 
					<label
				for="exampleInputEmail1">Username
			</label> 
			<input name="username" type="text" class="form-control" placeholder="Enter Username"> 
				<small
				id="emailHelp" class="form-text text-muted">We'll never share your email
				with anyone else.</small> 
			</div> 
			<div class="form-group"> 
				<label
				for="exampleInputPassword1">Password</label> 
				<input name="pass"
				type="password" class="form-control" id="exampleInputPassword1"
				placeholder="Enter Password"> 
			</div><br> 
			<button name="login"
				type="submit" class="btn btn-primary">Login</button> 
		</form> 
		</div>
	 </div>

	<?php 
		//login 
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			if(isset($_POST['login'])){
				$username = $_POST['username'];
				$password = $_POST['pass'];
				$sql = "SELECT * FROM accounts WHERE user='$username' AND password='$password'";
				$result = mysqli_query($conn,$sql);
				if(mysqli_num_rows($result)>0){
						echo "Success !";
						//session stuff
						$_SESSION['username'] = $username;
						$_SESSION['password'] = $password;
						header("Location:index.php?login=success");
				}else{
					echo "Incorrect credentials ..." ;
				}
			}
		}
	?>

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