<?php
include("include/header.php");

$message_obj = new Message($con,$userLoggedIn);
//session_destroy();

if(isset($_GET['profile_username'])){
	$username = $_GET['profile_username'];
	$get_user = mysqli_query($con,"SELECT * FROM users WHERE username='$username'");
	$fetch_user= mysqli_fetch_array($get_user);
	$num_friends= substr_count($fetch_user['friend_array'],',')-1;
}

if(isset($_POST['remove_friend'])){
	$remove = new User($con,$userLoggedIn);
	$remove->removeFriend($username);
}
if(isset($_POST['respond_request'])){
	header("Location:requests.php");
}
if(isset($_POST['add_friend'])){
	$respond = new User($con,$userLoggedIn);
	$respond->sendFriendRequest($username);
}

if(isset($_POST['post_message'])){
	if(isset($_POST['message_body'])){
		$body=mysqli_real_escape_string($con,$_POST['message_body']);
		$date=date("Y-m-d H:i:s");
		$message_obj->sendMessage($username,$body,$date);
	}
	$link='#profileTabs a[href="#messages_div"]';
	echo	"<script>
			$(function(){
				$('".$link."').tab('show');
				});
		</script>";
}
?>


	<style type="text/css">
		.wrapper{
			top: 0;
			margin-left: 0;
			margin-top: 10px;			
			padding-left: 0;
			
		}
		
	</style> 

	<div class="profile_left">
 		<img src="<?php echo $fetch_user['profile_pic']; ?>">
 		<div class="profile_info">
	 		
	 		<p>Posts: <?php echo $fetch_user['num_posts']; ?></p>
	 		<p>Likes: <?php echo $fetch_user['num_likes']; ?></p>
	 		<p>friends: <?php echo $num_friends; ?></p>
	 	</div>
	 		
	 	<form action="<?php echo $username; ?>" method="POST">
	 		
	 	<?php

	 		$profile_user_obj = new User($con,$username);
	 		$login_user_obj = new User($con,$userLoggedIn);
	 		
		 		if($profile_user_obj->isClosed()){
		 			header("location: user_closed.php");
		 		}

		 	if($username != $userLoggedIn){
		 		if($login_user_obj->isFriend($username)){
		 			echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend" style="cursor:pointer">';
		 		}

		 		else if($login_user_obj->didReceiveReq($username)){
		 			echo "<input type='submit' name='respond_request' class='warning' value='Respond Request'>";
		 		}
		 		else if($login_user_obj->didSendReq($username)){
		 			echo "<input type='submit' name='send_request' class='default' value='Request Sent'>";
		 		}
		 		else{
		 			echo "<input type='submit' name='add_friend' class='success' value='Add Friend'>";
		 		}
	 		}
	 	?>
	 	</form>
	 		<input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something!">
 	    	<?php
 	    	if($username != $userLoggedIn){ 
				echo "<div class='profile_mutual_friends'>";
				echo $login_user_obj->getMutualFriends($username)." Mutual Friends";
				echo "</div>";
			}
			?>
 	</div>
   
 	<div class="profile_main_colomn main_colomn colomn">
 		<ul class="nav nav-tabs" role="tablist" id="profileTabs">
		  <li class="nav-item">
		    <a class="nav-link active" href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a>
		  </li>
		 
		  <li class="nav-item">
		    <a class="nav-link" href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a>
		  </li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade show" id="newsfeed_div">
				<div class="posts_area"></div>
 				<img id="loading" src="assets/images/icons/loading.gif">
			</div>

			<div role="tabpanel" class="tab-pane fade" id="messages_div">

				<?php 

						
			 			echo "<h4>You and <a href='$username'>".$profile_user_obj->getFirstAndLastName()."</a></h4><hr><br>";
			 			echo "<div class='loaded_messages' id='scroll_messages'>";
			 			echo $message_obj->getMessages($username);
			 			echo "</div>";

				?>
			 	<div class="message_post">
			 		<form action="" method="POST">
			 			<textarea name='message_body' class='message_textarea' placeholder='Write a message ...'></textarea>
			 			<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
			 			
			 		</form>




			 	</div>

			 	

				
			</div>


		</div>

		<script>
				var div = document.getElementById('scroll_messages');
					div.scrollTop = div.scrollHeight;
				</script>


 	</div>

<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="postModalLabel">Post Something!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
       <p> This will appear on user's profile page and also their newsfeed for your friends to see</p>
       <form class="profile_post" action="" method="POST">
       	<div class="form-group">
       		<textarea class="form-control" name="post_body"></textarea>
       		<input type="hidden" name="user_from" value="<?php echo $userLoggedIn;?>">
       		<input type="hidden" name="user_to" value="<?php echo $username;?>">
       	</div>
       </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="submit_profile_post_button" name="post_button">Post</button>
      </div>
    </div>
  </div>
</div>



 	<!-- load profile post -->
 	<script>

 		var userLoggedIn='<?php echo $userLoggedIn; ?>';
 		var profileUsername ='<?php echo $username; ?>';
 		$(document).ready(function(){

 			$('#loading').show();


 			$.ajax({
 				url: "include/handlers/ajax_load_profile_post.php",
 				type: "POST",
 				data:"page=1&userLoggedIn="+ userLoggedIn +"&profileUsername="+profileUsername,
 				cache: false,

 				success: function(data){
 					$('#loading').hide();
 					$('.posts_area').html(data);
 				}

 			});


 			$(window).scroll(function(){

 				var height=$('.posts_area').height();
 				var scroll_top=$(this).scrollTop();
 				var page = $('.posts_area').find('.nextPage').val();
 				var noMorePosts =$('.posts_area').find('.noMorePosts').val();


 				if((window.innerHeight + window.scrollY) >= document.body.offsetHeight && noMorePosts=='false'){
 					
 					$('#loading').show();

 				

	 				var ajaxReq = $.ajax({
	 					url:"include/handlers/ajax_load_profile_post.php",
	 					type: 'POST',
	 					
	 					data: "page="+page+"&userLoggedIn="+userLoggedIn+"&profileUsername="+profileUsername,
	 					cache:false,

	 					success: function(response){
	 						$('.posts_area').find('.nextPage').remove();
	 						$('.posts_area').find('.noMorePosts').remove();
	 						$('#loading').hide();
	 						$('.posts_area').append(response);

	 					}


	 				});

 				}

 				return false;
 			});




 		});



 	</script>
 	







</div>
</body>
</html>