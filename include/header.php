<?php
require 'config/config.php';
include("include/classes/User.php");
include("include/classes/Post.php");
include("include/classes/Message.php");

if(isset($_SESSION['username'])){
	$userLoggedIn=$_SESSION['username'];
	$user_info_query=mysqli_query($con,"SELECT * FROM users WHERE username='$userLoggedIn'");
	$user=mysqli_fetch_array($user_info_query);
	

}else{
	header("Location: register.php");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>social</title>

	<!-- javascript -->
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.js"></script>
	<script src="assets/js/social.js"></script>
	<script src="assets/js/bootbox.js"></script>
	<script src="assets/js/jquery.Jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>
	<script src="assets/js/upload.js"></script>
	

	<!-- css -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	
	<link rel="stylesheet" type="text/css" href="assets/css/jquery.Jcrop.css">
	

</head>
<body>

	<div class="top_bar">
		<div class="logo">
			<a href="index.php">Socialbook!</a>
		</div>

		<nav>

			<a href='<?php echo"$userLoggedIn"; ?>'>
				<?php
				echo $user['first_name'];
				?>
			</a>

			<a href="index.php">
				<i class="fa fa-home fa-lg"></i>
			</a>
			<a href="#">
				<i class="fa fa-envelope fa-lg"></i>
			</a>
			<a href="#">
				<i class="fa fa-bell fa-lg"></i>
			</a>
			<a href="request.php">
				<i class="fa fa-users fa-lg"></i>
			</a>
			<a href="#">
				<i class="fa fa-cog fa-lg"></i>
			</a>
			<a href="include/handlers/logout.php">
				<i class="fa fa-sign-out fa-lg"></i>
			</a>
		</nav>


	</div>


<div class="wrapper">
	