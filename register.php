<?php

require 'config/config.php';
require 'include/form_handlers/register_form.php';
require 'include/form_handlers/login_form.php'; 



?>



<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<title>Welcome</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/register.js"></script>
</head>
<body>


	

<div class="wrapper"> 
	<div class="inner-box">

		<div class="login_header">
			<h1>Socialbook</h1>
			Login or Signup below
			
		</div>



	<div id="first">
		<form action="" method="POST">
		<input name="log_email" type="text" placeholder="Email" value="<?php
		 if(isset($_SESSION['log_email'])){
		 	echo $_SESSION['log_email'];
		 }
		?>" required>
		<br>

		<input type="password" name="log_password" placeholder="password" required>
		<br>
		<?php
			if(in_array("something went wrong with either Email or Password<br>", $error_array))
				echo "something went wrong with either Email or Password<br>";
		?>
		<input type="submit" name="log_button" value="Login!">
		<br>
		<a href="#" id="signup">Don't have Account? Register here!</a>
		
		</form>
	</div>

		
	<div id="second">
		<form action="register.php" method="POST">
			<input type="text" name="reg_fname" placeholder="First Name" value="<?php 
			if(isset($_SESSION['reg_fname'])){
				echo $_SESSION['reg_fname'];
			}
			?>" required>
		 	<br>
		 	<?php if(in_array("first name must be between 2 and 25 characters long<br>", $error_array)){
				echo "first name must be between 2 and 25 characters long<br>";
		 	}
			?>
		 	
		 	<input type="text" name="reg_lname" placeholder="Last Name" value="<?php 
			if(isset($_SESSION['reg_lname'])){
				echo $_SESSION['reg_lname'];
			}
			?>" required>
		 	<br>
			<?php if(in_array("first name must be between 2 and 25 characters long<br>", $error_array)){
				echo "last name must be between 2 and 25 characters long<br>";
			}
		 	
		 	?>

		 	<input type="email" name="reg_email" placeholder="Email" value="<?php 
			if(isset($_SESSION['reg_email'])){
				echo $_SESSION['reg_email'];
			}
			?>
			" required>
		 	<br>



		 	<input type="email" name="reg_email2" placeholder="re-enter email" value="<?php 
			if(isset($_SESSION['reg_email2'])){
				echo $_SESSION['reg_email2'];
			}
			?>
			" required>
		 	<br>

		 	<?php if(in_array("Email don't match<br>", $error_array)){
		 		echo "Email don't match<br>";
		 	}else if(in_array("Invalid format<br>", $error_array)){
		 		echo "Invalid format<br>";
		 	}else if(in_array("Email is already taken <br>", $error_array)){
		 		echo "Email is already taken <br>";
		 	}

		 	?>

		 	<input type="password" name="reg_password" placeholder="password" required>
		 	<br>


		 

		 	<input type="password" name="reg_password2" placeholder="confirm password" required>
		 	<br>

		 	<?php if(in_array("passwords don't match<br>", $error_array)){
		 		echo "passwords don't match<br>";
		 	}else if(in_array("Your password must be in between 5 to 30 charater long<br>", $error_array)){
		 		echo "Your password must be in between 5 to 30 charater long<br>";
		 	}else if(in_array("Your password must contain only characters or digits<br>", $error_array)){
		 		echo "Your password must contain only characters or digits<br>";
		 	}

		 	?>

		 	<?php if(in_array("<span style='color: #14C800;'>Registered successfully</span><br>",$error_array)){
		 		
		 		echo '<span style="color: #14C800;">Registered successfully</span><br>';
		 		

		 		}?>
		 	<input type="submit" name="reg_button" value="Register">
		 	<br>
		 	<a href="#" class="signin">Have an account? signin here!</a>
		 </form>
		</div>
	</div>

</div>


<?php
		if(isset($_POST['reg_button'])){
			echo '
			<script type="text/javascript">
			$(document).ready(function(){
				$("#first").hide();
				$("#second").show();
				});
			</script>
			';
		}

	?>


</body>
</html>