<?php	
	//date_default_timezone_set('Asia/Calcutta');
	set_time_limit(0); //no limit
	$conn = new mysqli('localhost', 'root','','database_name');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
?>
