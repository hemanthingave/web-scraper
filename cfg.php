<?php	
	//date_default_timezone_set('Asia/Calcutta');
	//define('ADMIN_URL', 'sd');
	set_time_limit(0); //no limit
	if($_SERVER['SERVER_NAME']=='localhost'){
		$conn = new mysqli('localhost', 'root','','hemant_dte');
	} else {
		$conn = new mysqli('localhost', 'dailyjoninfo','hemant@123','dailyjobinfo');
	}
	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	
?>