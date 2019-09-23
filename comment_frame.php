<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
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

?>


<script>
	function toggle(){
		$target =$(event.target);
		if(!target.is('a')){
			var element = document.getElementById("comment_section");
		if(element.style.display=='block')
			element.style.display='none';
		else
			element.style.display='block';
		}
	
	}
</script>
<?php
	if(isset($_GET['post_id'])){
		$post_id=$_GET['post_id'];

		$query = mysqli_query($con,"SELECT added_by , user_to FROM posts WHERE id='$post_id'");
		$fetch_row = mysqli_fetch_array($query);
		$added_by = $fetch_row['added_by'];
	}
	if(isset($_POST['postComment'.$post_id])){
		$comment_body = $_POST['comment_body'];
		$comment_body = mysqli_escape_string($con,$comment_body);
		$date_time_now = date("Y-m-d H:i:s");
		
		$insert_comment=mysqli_query($con,"INSERT INTO comments VALUES('','$comment_body','$userLoggedIn','$added_by','$date_time_now','no','$post_id')");
		echo 'comment posted';
	}

?>

	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method='POST'>
		<textarea name="comment_body" placeholder="Add new comment!"></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value='Post'>
	</form>


<?php
	$select_query=mysqli_query($con,"SELECT * FROM comments WHERE post_id=$post_id");
	$rows =mysqli_num_rows($select_query);

	if($rows !=0){
		while($comment=mysqli_fetch_array($select_query)){
			$post_body=$comment['post_body'];
			$posted_by=$comment['posted_by'];
			$posted_to=$comment['posted_to'];
			$date_added=$comment['date_added'];
			$removed =$comment['removed'];
			$post_id=$comment['post_id'];



				$time_date_now=date("Y-m-d H:i:s");
				$start_time=new DateTime($date_added);
				$end_time=new DateTime($time_date_now);
				$interval=$start_time->diff($end_time);

				if($interval->y >=1){
					if($interval==1){
						$time_message = $interval->y ." year ago";
					}else{
						$time_message = $interval->y . " years ago";
					}
				
				}else if($interval->m>=1){
					if($interval->d==0){
						$days=" ago";
					
					}else if($interval->d>=1){
						if($interval->d==1){
							$days = $interval->d . " day ago";
						}else{
							$days = $interval->d . " days ago";
						}
					}

					if($interval->m==1){
						$months = $interval->m . " month ago";
					}else{
						$months = $interval->m . " months ago";
					}

					$time_message = $months. " " .$days;
				
				}else if($interval->d>=1){
					if($interval->d==1){
						$time_message="Yesterday";
					}else{
						$time_message=$interval->d . " days ago";
					}
					
				}else if($interval->h >= 1){
					if($interval->h==1){
						$time_message = $interval->h . " hour ago";
					}else{
						$time_message = $interval->h . " hours ago";
					}
				}else if($interval->i >= 1){
					if($interval->i==1){
						$time_message = $interval->i . " minute ago";
					}else{
						$time_message = $interval->i . " minutes ago";
					}

				}else{
					if($interval->s < 30){
						$time_message ="just now";
					}else{
						$time_message = $interval->s . " seconds ago";
					}

				}
				$user_obj=new User($con,$posted_by);
				?>

				<div class="comment_section">
					<a href="<?php echo $posted_by ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $user_obj->getFirstAndLastName()?>" style="float:left" height='30'></a>
					<a href="<?php echo $posted_by ?>" target="_parent"><b><?php echo $user_obj->getFirstAndLastName();?></b></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<?php echo $time_message . "<br>" . $post_body; ?>
					

				</div>
				<hr>
				<?php


		}
	}else{
		echo "<br><br>
		<p style='text-align: center;'>No comments to show!</p>";
	}

?>


	
</body>
</html>