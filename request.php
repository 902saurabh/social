<?php 
	include("include/header.php");

?>
	<div class="main_colomn colomn">
		<h4>Friend Requests</h4>
	<?php
	$query = mysqli_query($con,"SELECT * FROM friend_request WHERE user_to='$userLoggedIn'");
	if(mysqli_num_rows($query)>0){

		while($row = mysqli_fetch_array($query)){

			$user_from = $row['user_from'];
			$user_from_obj = new User($con,$user_from);
			
			echo $user_from_obj->getFirstAndLastName() . " has sent you a friend request.";

			$user_from_friend_array = $user_from_obj->getFriendArray();
			//$user_to_query = mysqli_query($con,"SELECT friend_array from users where username='$user_from'");
			//$user_from_query = mysqli_query($con,"SELECT friend_array from users where username='$userLoggedIn'");

			//$user_to_array = mysqli_fetch_array($user_to_query)['friend_array'];
			//$user_from_array = mysqli_fetch_array($user_from_query)['friend_array'];

			if(isset($_POST['respond' . $user_from])){
				$query_to = mysqli_query($con,"UPDATE users set friend_array=CONCAT(friend_array,'$userLoggedIn,') where username='$user_from'");
				$query_from = mysqli_query($con,"UPDATE users set friend_array=CONCAT(friend_array,'$user_from,') where username='$userLoggedIn'");

				$query_delete = mysqli_query($con,"DELETE FROM friend_request where user_from='$user_from' and user_to='$userLoggedIn'");
				echo "You are now friends!";
				header("Location: request.php");
			}
			if(isset($_POST['reject' . $user_from])){
				$query_delete = mysqli_query($con,"DELETE FROM friend_request where user_from='$user_from' and user_to='$userLoggedIn'");
				echo "Request Ignored!";
				header("Location: request.php");
			}


			?>

			<form action="request.php" method="POST">
				<input type="submit" name="respond<?php echo $user_from; ?>" id="accept_button" value="Accept">
				<input type="submit" name="reject<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
			</form>
			<?php
		}
	}else{
		echo "You have no friend requests! ";
	}


 ?>