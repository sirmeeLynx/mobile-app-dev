<?php

	require_once ('DB_Functions.php');
	
	//connection to db and constructor of function class
	$db = new DB_Functions();
	$response = array("error" => false);
	if(isset($_POST['username']) && isset($_POST['password'])) {	
		//got details
		$username = $_POST['username'];
		$password = $_POST['password'];
		//get the username and password.
		$user = $db->getuserData($username, $password); // get username & password
		if($user != false){
			//successfully found user data with gr no.
			if ($user["User_Is_Verified"]){
				$response["error"] = FALSE;
				$response["user"] = $user;
			}else{
				// user is not found with email verification
				$response["error"] = TRUE;
				$response["error_msg"] = "You are yet to verify your account!";
			}
		}else{
				// user is not found with the credentials
				$response["error"] = TRUE;
				$response["error_msg"] = "Invalid Credentials!!!";
		}	
	}else{
		// required post params is missing
		$response["error"] = TRUE;
		$response["error_msg"] = "Required parameters is missing!";
	}
	echo json_encode($response);
?>