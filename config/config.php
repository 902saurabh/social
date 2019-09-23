<?php

ob_start();        // output buffer start 
session_start();  //session start

$timezone = date_default_timezone_set("Indian/Antananarivo");

$con=mysqli_connect("localhost","root","","social");

if(mysqli_connect_errno()){
	echo "failed to connect" . mysqli_connect_errno;
}



?>