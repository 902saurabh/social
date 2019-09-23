<?php

class Post{
   private	$con;
   private $user_obj;

	public function __construct($con,$username){
	   	$this->con=$con;
	   	$this->user_obj=new User($con,$username);
   }

	public function submitPost($body,$user_to){
	   	$body=strip_tags($body);
	   	$body=mysqli_real_escape_string($this->con,$body);
	   	$body=preg_replace('/\s+/','', $body);
   		if($body != ''){
   		$added_by=$this->user_obj->getUsername();
   		$date_added=date('Y-m-d H-i-s');
   		if($user_to==$added_by){
   			$user_to="none";

   		}

   		$insert_query=mysqli_query($this->con,"INSERT INTO posts VALUES('','$body','$added_by','$user_to','$date_added','no','no',0)");
   		$returned_id=mysqli_insert_id($this->con);
   		$num_posts=$this->user_obj->getNumOfPosts();
   		$num_posts++;
   		$update_user=mysqli_query($this->con,"UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

   		}
   
	}





	public function loadPostsFriend($data,$limit){

		$page = $data['page'];
		$userLoggedIn =$this->user_obj->getUsername();

		if($page==1)
			$start=0;
		else
			$start=($page-1) * $limit;


		$str="";
		$post_query=mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
		//echo mysqli_num_rows($post_query);

		if(mysqli_num_rows($post_query)>0){
		
			$num_iter=0;
			$count=1;

			while($row=mysqli_fetch_array($post_query)){



				$id=$row['id'];
				$body=$row['body'];
				$added_by=$row['added_by'];
				$date_added=$row['date_added'];

				$rows_query=mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
				$rows=mysqli_num_rows($rows_query);

				if($row['user_to']=='none'){
					$user_to="";
				}else{
					$user_to_obj=new User($this->con,$row['user_to']);
					$user_to_name=$user_to_obj->getFirstAndLastName();
					$user_to=" to <a href='".$row['user_to']."'>".$user_to_name."</a>";
					
				}

				$added_by_obj=new User($this->con,$added_by);
				$user = new User($this->con,$userLoggedIn);

				if($user->isFriend($added_by)){



					if($added_by_obj->isClosed()){
						continue;
					}


					if($num_iter++ < $start){
						continue;
					}

					if($count > $limit){
						break;
					}else{
						$count++;
					}

					if($userLoggedIn == $added_by){
						$delete_button="<button class='delete_post_button btn-danger' id='post$id'>X</button>";
					}else{
						$delete_button="";
					}

					$user_query=mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");

					$added_row=mysqli_fetch_array($user_query);
					$profile_pic =$added_row['profile_pic'];
					$first_name  = $added_row['first_name'];
					$last_name  =$added_row['last_name'];

					
					?>

					<script>
						function toggle<?php echo $id;?>(){
							var element = document.getElementById('toggleComment<?php echo $id; ?>')
							if(element.style.display=='block')
								element.style.display='none';
							else
								element.style.display='block';
						}
					</script>
					<?php

					//timeframe

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

					$str .= "<div class='status_post'>

							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#acacac'>
								<a href='$added_by' >$first_name $last_name</a>$user_to&nbsp;&nbsp;&nbsp;&nbsp;$time_message $delete_button
							</div>

							<div class='post_body'>
								$body
								<br>
							</div>
							<br><br>
							<div class='newsFeedPostOPtions'>
								<span onClick='javascript:toggle$id()' style='cursor:pointer;'>Comments($rows)&nbsp;&nbsp;&nbsp;&nbsp;<iframe src='like.php?post_id=$id' scrolling='no'></iframe></span>
							</div>

						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none'>
						<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' ></iframe> 
						</div>
						<hr>";		
				}

				?>

				<script>
					$(document).ready(function(){

						$("#post<?php echo $id;?>").on('click',function(){
							bootbox.confirm("Are you sure you want to delete this post?",function(result){
								$.post("include/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result:result});

								if(result)
									location.reload();
							});


						});
					
					});

				</script>




				<?php


				} // end of while loop

			if($count > $limit){
				
				$str .= "<input type='hidden' class='nextPage' value='" . ($page+1) ."'>
						<input type='hidden' class='noMorePosts' value='false'>";
			}else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'> <p style='text-align: centre;'> No more Posts to show ! </p>";
			}

		}
		echo $str;
	}
 

 	public function loadProfilePosts($data,$limit){

		$page = $data['page'];
		$userLoggedIn =$this->user_obj->getUsername();
		$profileUsername = $data['profileUsername'];

		if($page==1)
			$start=0;
		else
			$start=($page-1) * $limit;


		$str="";
		$post_query=mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' AND ((user_to='none' AND added_by='$profileUsername') OR user_to='$profileUsername') ORDER BY id DESC");
		

		if(mysqli_num_rows($post_query)>0){
		
			$num_iter=0;
			$count=1;

			while($row=mysqli_fetch_array($post_query)){



				$id=$row['id'];
				$body=$row['body'];
				$added_by=$row['added_by'];
				$date_added=$row['date_added'];

				$rows_query=mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
				$rows=mysqli_num_rows($rows_query);



					if($num_iter++ < $start){
						continue;
					}

					if($count > $limit){
						break;
					}else{
						$count++;
					}

					if($userLoggedIn == $added_by){
						$delete_button="<button class='delete_post_button btn-danger' id='post$id'>X</button>";
					}else{
						$delete_button="";
					}

					$user_query=mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");

					$added_row=mysqli_fetch_array($user_query);
					$profile_pic =$added_row['profile_pic'];
					$first_name  = $added_row['first_name'];
					$last_name  =$added_row['last_name'];

					
					?>

					<script>
						function toggle<?php echo $id;?>(){
							var element = document.getElementById('toggleComment<?php echo $id; ?>')
							if(element.style.display=='block')
								element.style.display='none';
							else
								element.style.display='block';
						}
					</script>
					<?php

					//timeframe

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

					$str .= "<div class='status_post'>

							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#acacac'>
								<a href='$added_by' >$first_name $last_name</a>&nbsp;&nbsp;&nbsp;&nbsp;$time_message $delete_button
							</div>

							<div class='post_body'>
								$body
								<br>
							</div>
							<br><br>
							<div class='newsFeedPostOPtions'>
								<span onClick='javascript:toggle$id()' style='cursor:pointer;'>Comments($rows)&nbsp;&nbsp;&nbsp;&nbsp;<iframe src='like.php?post_id=$id' scrolling='no'></iframe></span>
							</div>

						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none'>
						<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' ></iframe> 
						</div>
						<hr>";		
				

				?>

				<script>
					$(document).ready(function(){

						$("#post<?php echo $id;?>").on('click',function(){
							bootbox.confirm("Are you sure you want to delete this post?",function(result){
								$.post("include/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result:result});

								if(result)
									location.reload();
							});


						});
					
					});

				</script>




				<?php


				} // end of while loop

			if($count > $limit){
				
				$str .= "<input type='hidden' class='nextPage' value='" . ($page+1) ."'>
						<input type='hidden' class='noMorePosts' value='false'>";
			}else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'> <p style='text-align: centre;'> No more Posts to show ! </p>";
			}

		}
		echo $str;
	}






 }

?>