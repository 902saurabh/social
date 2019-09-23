<?php

if(isset($_POST['log_button'])){
	
	$email=filter_var($_POST['log_email'],FILTER_SANITIZE_EMAIL);
	$_SESSION['log_email']=$email;

	$password=md5($_POST['log_password']);
	$check_query=mysqli_query($con,"SELECT * FROM users WHERE email='$email' and password='$password'");

	$count_rows=mysqli_num_rows($check_query);

	if($count_rows==1){
		$row=mysqli_fetch_array($check_query);

		$check_close=mysqli_query($con,"SELECT * FROM users WHERE email='$email' and user_closed='yes'");
		$close_num=mysqli_num_rows($check_close);

		if($close_num==1){
			$reopen = mysqli_query($con,"UPDATE users set user_closed='no' where email='$email'");
		}

		$_SESSION['username']=$row['username'];

		header("Location:index.php");
		exit();
	}else{
		array_push($error_array,"something went wrong with either Email or Password<br>");
	}


}




?>