<?php
include("include/header.php");


if(isset($_POST['post'])){
	$post=new Post($con,$userLoggedIn);
	$post->submitPost($_POST['post_text'],'none');
}
//session_destroy();
?>
 	
 	<div class="user_details colomn">
 		
 		<a href='<?php echo "$userLoggedIn"; ?>'><img src="<?php 
 			echo $user['profile_pic'];
 		?>">	
 		</a>
 		<div class="user_details_left_right">
 		<a href='<?php echo"$userLoggedIn"; ?>'>
 			<?php
 				echo $user['first_name']. " " .$user['last_name']."<br>";
 			?>
 		</a>

 		<?php
 		echo "Posts: ". $user['num_posts']. "<br>" ."Likes: ". $user['num_likes'];

 		?>
 		</div>
 	</div>

 	<div class="main_colomn colomn">
 		<form class="post_form" action="index.php" method="POST">
 			<textarea name="post_text" id="post_text" placeholder="want to say something? "></textarea>
 			<input type="submit" id="post_button" name="post" value="post">
 			<hr>
 		</form>

 		<div class="posts_area"></div>
 		<img id="loading" src="assets/images/icons/loading.gif">

	</div>

	<!-- load all posts -->
 	<script>
 		var userLoggedIn='<?php echo $userLoggedIn; ?>';
 		$(document).ready(function(){

 			$('#loading').show();


 			$.ajax({
 				url: "include/handlers/ajax_load_posts.php",
 				type: "POST",
 				data:"page=1&userLoggedIn="+ userLoggedIn,
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
	 					url:"include/handlers/ajax_load_posts.php",
	 					type: 'POST',
	 					data: "page="+page+"&userLoggedIn="+userLoggedIn,
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