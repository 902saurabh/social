<?php 


	class Message{

		private $user_obj;
		private $con;

		public function __construct($con,$user){
			$this->con=$con;
			$this->user_obj=new User($this->con,$user);
		}

		public function getMostRecentUser(){

			$userLoggedIn=$this->user_obj->getUsername();

			$query = mysqli_query($this->con,"SELECT user_to , user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' order by id DESC LIMIT 1");
			if(mysqli_num_rows($query)==0){
				$user_to=false;
			}
			$row=mysqli_fetch_array($query);
			$user_to=$row['user_to'];
			$user_from=$row['user_from'];
			//$user_to=$row['username'];
			
			if($userLoggedIn != $user_to){
				return $user_to;
			}else
				return $user_from;

		}

		public function sendMessage($user_to,$body,$date){

			if($body!=""){
			$userLoggedIn = $this->user_obj->getUsername();
			$query = mysqli_query($this->con,"INSERT into messages VALUES('','$user_to','$userLoggedIn','$body','$date','no','no','no')");

			}

		}

		public function getMessages($otherUser){
			$userLoggedIn = $this->user_obj->getUsername();
			$data="";

			$update = mysqli_query($this->con,"UPDATE messages SET opened='yes' WHERE (user_to='$userLoggedIn' AND user_from='$otherUser'");
			$query = mysqli_query($this->con,"SELECT * FROM messages WHERE (user_to='$userLoggedIn' And user_from='$otherUser') OR (user_from='$userLoggedIn' And user_to='$otherUser')");
			while($row = mysqli_fetch_array($query)){
				$user_to=$row['user_to'];
				$user_from=$row['user_from'];
				$body=$row['body'];

				$div_top=($user_to == $userLoggedIn)? "<div class='messages' id='green'>" : "<div class='messages' id='blue'>";
				$data = $data . $div_top . $body . "</div><br><br>";
			}
			return $data;
		}


		public function getLatestMessage($userLoggedIn,$otherUser){
			$details_array = array();

			$query = mysqli_query($this->con,"SELECT user_to , body , date FROM messages where (user_to = '$userLoggedIn' AND user_from = '$otherUser') OR (user_to = '$otherUser' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");
			$row = mysqli_fetch_array($query);

			$mess = ($row['user_to'] != $userLoggedIn) ? "You Said: ": "They Said: "; 

			//time frame
			$time_date_now=date("Y-m-d H:i:s");
			$start_time=new DateTime($row['date']);
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

			array_push($details_array, $mess);
			array_push($details_array, $row['body']);
			array_push($details_array, $time_message);

			return $details_array;

		}





		public function getConversations(){
			$userLoggedIn = $this->user_obj->getUsername();
			$return_string = "";
			$convers_array = array();

			$query = mysqli_query($this->con,"SELECT user_to , user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' order by id desc");

			while($row=mysqli_fetch_array($query)){
				$otherUser = ($row['user_to']!= $userLoggedIn)? $row['user_to'] : $row['user_from'];

				if(!in_array($otherUser, $convers_array)){
					array_push($convers_array, $otherUser);
				}

			}

			foreach($convers_array as $username){
				$otherUser_obj = new User($this->con,$username);
				$latest_message = $this->getLatestMessage($userLoggedIn,$username);
			
				$dots = (strlen($latest_message[1]>12)) ? "..." : "";
				$split = str_split($latest_message[1],12);
				$split = $split[0] . $dots;

				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages'>
									<img src='". $otherUser_obj->getProfilePic() . "' style='border-radius:5px; margin-right:5px'>"
									. $otherUser_obj->getFirstAndLastName() ."  ".
									"<span class='timestamp' id ='grey'>".$latest_message[2]."</span>".
									"<p id='grey' style='margin: 0;'>".$latest_message[0] . $split . "</p>".
									"</div>".
									"</a>";

			}
			return $return_string;

		}

	}
 ?>