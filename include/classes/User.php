<?php

class User{
	private $con;
	private $user;

	public function __construct($con,$username){
		$this->con=$con;

		$user_detail_query = mysqli_query($this->con,"SELECT * FROM users WHERE username='$username'");
		$this->user=mysqli_fetch_array($user_detail_query);
		

	}

	public function getUsername(){
		return $this->user['username'];
	}

	public function getNumOfPosts(){
		$username=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT first_name , last_name FROM users WHERE username='$username'");
		$row=mysqli_fetch_array($query);
		return $this->user['num_posts'];

	}


	public function getProfilePic(){
		$username=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT first_name , last_name FROM users WHERE username='$username'");
		$row=mysqli_fetch_array($query);
		return $this->user['profile_pic'];
	}

	public function getFriendArray(){
		$username=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT first_name , last_name FROM users WHERE username='$username'");
		$row=mysqli_fetch_array($query);
		return $this->user['friend_array'];
	}


	public function isFriend($sentFriendName){
		$friendName = ",".$sentFriendName.",";

		if(strstr($this->user['friend_array'],$friendName) || $sentFriendName==$this->user['username']){
			return true;
		}else{
			return false;
		}
	}

	public function isClosed(){
		if($this->user['user_closed']=="yes")
			return true;
		else
			return false;
	}
	public function getFirstAndLastName(){
		$username=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT first_name , last_name FROM users WHERE username='$username'");
		$row=mysqli_fetch_array($query);
		return $row['first_name']. " " .$row['last_name'];
		

		//echo $this->user['first_name']." ".$this->user['last_name'];
	}

	public function didReceiveReq($user_from){
		$user_to=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT user_to , user_from from friend_request where user_to='$user_to' and user_from='$user_from'");
		if(mysqli_num_rows($query)!=0){
			return true;
		}else{
			return false;
		}
	}

	public function didSendReq($user_to){
		$user_from=$this->user['username'];
		$query = mysqli_query($this->con,"SELECT user_to,user_from from friend_request where user_to='$user_to' and user_from='$user_from'");
		if(mysqli_num_rows($query)!=0){
			return true;
		}else{
			return false;
		}
	}

	public function removeFriend($user_to_remove){
		$user_logged_in=$this->user['username'];

		$query = mysqli_query($this->con,"SELECT friend_array from users where username='$user_to_remove'");
		$row=mysqli_fetch_array($query);
		$friends_of_removed_user=$row['friend_array'];

		$new_array=str_replace($user_to_remove.",","", $this->user['friend_array']);
		$update_query=mysqli_query($this->con,"UPDATE users set friend_array='$new_array' where username='$user_logged_in'");

		$new_array=str_replace($user_logged_in.",","",$friends_of_removed_user);
		$update_query=mysqli_query($this->con,"UPDATE users set friend_array='$new_array' where username='$user_to_remove'");

	}


	public function sendFriendRequest($user_to){
		$user_from=$this->user['username'];
		$query=mysqli_query($this->con,"INSERT INTO friend_request values('','$user_to','$user_from')");

	}

	public function getMutualFriends($user_to_check){
		$mutualCount=0;
		$user_array = $this->user['friend_array'];
		$user_array_explode =explode(",",$user_array);

		$friend_query = mysqli_query($this->con, "SELECT friend_array FROM users where username='$user_to_check'");
		$row = mysqli_fetch_array($friend_query);
		$friend_array = $row['friend_array'];
		$friend_array_explode = explode(",",$friend_array);

		foreach($user_array_explode as $i){
			foreach ($friend_array_explode as $j) {
				if($i == $j && $i != ""){
					$mutualCount++;
				}
			}
		}

		return $mutualCount;
	}




}




?>