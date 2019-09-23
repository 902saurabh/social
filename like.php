<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>

	<style type="text/css">
	* {
		font-family: Arial, Helvetica, Sans-serif;
	}
	body {
		background-color: #fff;
	}

	form {
		position: absolute;
		top: 0;
	}

	</style>

<?php	

require 'config/config.php';
include("include/classes/User.php");
include("include/classes/Post.php");

if(isset($_SESSION['username'])){

	$userLoggedIn=$_SESSION['username'];
	$user_info_query=mysqli_query($con,"SELECT * FROM users WHERE username='$userLoggedIn'");
	$user=mysqli_fetch_array($user_info_query);
	

}else{
	header("Location: register.php");
}


if(isset($_GET['post_id'])){
	$post_id=$_GET['post_id'];
}

$get_likes= mysqli_query($con,"SELECT added_by,likes FROM posts WHERE id='$post_id'");

$row=mysqli_fetch_array($get_likes);
$num_likes=$row['likes'];
$posted_by =$row['added_by'];

$user_query=mysqli_query($con,"SELECT * from users where username='$posted_by'");
$row2=mysqli_fetch_array($user_query);
$likes=$row2['num_likes'];



if(isset($_POST['like_button'])){
	$num_likes++;
	$post_update=mysqli_query($con,"update posts set likes='$num_likes' where id='$post_id'");
	$likes++;
	$user_update=mysqli_query($con,"update users set num_likes='$likes' where username='$posted_by'");
	$insert_like=mysqli_query($con,"insert into likes values('','$userLoggedIn','$post_id')");

}
if(isset($_POST['unlike_button'])){
	$num_likes--;
	$post_update=mysqli_query($con,"update posts set likes='$num_likes' where id='$post_id'");
	$likes--;
	$user_update=mysqli_query($con,"update users set num_likes='$likes' where username='$posted_by'");
	$delete_like=mysqli_query($con,"delete from likes where username='$userLoggedIn' and post='$post_id'");
	
}



$check_query = mysqli_query($con,"select * from likes where username='$userLoggedIn' and post='$post_id'");

$num_rows=mysqli_num_rows($check_query);


if($num_rows>0){
	echo '<form action="like.php?post_id='.$post_id.'" method="POST" style="margin-top: 19px;">
		<input type="submit" name="unlike_button" class="comment_like" value="unlike">
		<div class="like_value">('
			.$num_likes.
			')likes
		</div>
	</form>';

}else{
	
	echo '<form action="like.php?post_id='.$post_id.'" method="POST" style="margin-top: 19px;">
		<input type="submit" name="like_button" class="comment_like"  value="like">
		<div class="like_value">('
			.$num_likes.
			')likes
		</div>
	</form>';
	
}




?>

</body>

</html>