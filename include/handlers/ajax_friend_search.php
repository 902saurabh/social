<?php 
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn=$_POST['userLoggedIn'];

$name=explode(" ", $query);

if(strpos($query,"_") !== false){
	$usersReturned = mysqli_query($con , "SELECT * from users where (username like '$query%' and user_closed='no') limit 8");
}
else if(count($name)==2){
	$usersReturned = mysqli_query($con, "SELECT * FROM users where( first_name like '%$name[0]%' and last_name like '%$name[1]%') and user_closed='no' limit 8");
}else{
	$usersReturned = mysqli_query($con, "SELECT * FROM users where( first_name like '%$name[0]%' or last_name like '%$name[0]%') and user_closed='no' limit 8");
}

if($query!= ""){
	while($row=mysqli_fetch_array($usersReturned)){
		$user=new User($con,$userLoggedIn);

		if($row['username'] != $userLoggedIn){
			$mutual_friends = $user->getMutualFriends($row['username']) . " Friends in common";
		}else{
			$mutual_friends="";
		}

		if($user->isFriend($row['username'])){
			echo "<div class='resultDisplay'>

					<a href='messages.php?u='". $row['username']." style='color: #000;'>
					<div class='liveSearchProfilePic'>
						<img src='". $row['profile_pic'] . "'>
						</div>

						<div class='liveSearchText'>
							".$row['first_name']." ".$row['last_name'].
							"<p id='grey' style='margin:0px;'>".$row['username']."</p>
							<p id='grey'>".$mutual_friends."</p>
						</div>
					</a>






			</div>";
		}
	}
}



 ?>