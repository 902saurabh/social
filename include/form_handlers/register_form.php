<?php

$fname="";
$lname="";
$em="";
$em2="";
$password="";
$password2="";
$date="";
$error_array=array();



if(isset($_POST['reg_button'])){

	// first name stripping
	$fname=strip_tags($_POST['reg_fname']);
	$fname=str_replace(' ','',$fname);
	$fname=ucfirst(strtolower($fname));
	$_SESSION['reg_fname']=$fname;

	//last name
	$lname=strip_tags($_POST['reg_lname']);
	$lname=str_replace(' ','',$lname);
	$lname=ucfirst(strtolower($lname));
	$_SESSION['reg_lname']=$lname;

	//email
	$em=strip_tags($_POST['reg_email']);
	$em=str_replace(' ','',$em);
	$_SESSION['reg_email']=$em;

	$em2=strip_tags($_POST['reg_email2']);
	$em2=str_replace(' ','',$em2);
	$_SESSION['reg_email2']=$em2;



	$password=strip_tags($_POST['reg_password']);
	
	$password2=strip_tags($_POST['reg_password2']);
	


	$date=date("Y-m-d");

	if($em==$em2){

		if(filter_var($em,FILTER_VALIDATE_EMAIL)){
			$em=filter_var($em,FILTER_VALIDATE_EMAIL);

			$check = mysqli_query($con,"SELECT email FROM users WHERE email='$em'");

		$num_email=mysqli_num_rows($check);

		if($num_email>0){
			array_push($error_array,"Email is already taken <br>");
		}

		}else{
			array_push($error_array,"Invalid format<br>");
		}
	}else{
		array_push($error_array,"Email don't match<br>");
	}

	if(strlen($fname)<2 || strlen($fname)>25){
		array_push($error_array,"first name must be between 2 and 25 characters long<br>");
	}
	if(strlen($lname)<2 || strlen($lname)>25){
		array_push($error_array,"last name must be between 2 and 25 characters long<br>");
	}

	if($password!=$password2){
		array_push($error_array,"passwords don't match<br>");
	}else{
		if(preg_match('/[^A-Za-z0-9]/',$password)){
			array_push($error_array,"Your password must contain only characters or digits<br>");
		}

		
	}
	
		if(strlen($password)<5 || strlen($password)>30){
				array_push($error_array,"Your password must be in between 5 to 30 charater long<br>");
			}


			if(empty($error_array)){
				$password=md5($password);

				$username=strtolower($fname. "_" .$lname);
				
				$check_username=mysqli_query($con,"SELECT username from users where username='$username'");
				$i=0;
				$user=$username;
				
				while(mysqli_num_rows($check_username)!=0){
					$i++;
					$user=$username. "_" . $i;
					$check_username=mysqli_query($con,"SELECT username from users where username='$user'");
					
				}
				$username=$user;


			

			$rand=rand(1,2);

			if($rand==1)
				$profile_pic="assets/images/profile_pics/default/head_deep_blue.png";
			else if($rand==2)
				$profile_pic="assets/images/profile_pics/default/head_emerald.png";

	
			$insert_query=mysqli_query($con,"INSERT INTO users VALUES ('','$fname','$lname','$username','$date','$profile_pic',0,0,'no',',','$em','$password')");

			array_push($error_array,"<span style='color: #14C800;'>Registered successfully</span><br>");

			$_SESSION['reg_fname']="";
			$_SESSION['reg_lname']="";
			$_SESSION['reg_email']="";
			$_SESSION['reg_email2']="";
}

}



?>