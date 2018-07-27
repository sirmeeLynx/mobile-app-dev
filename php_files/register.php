<?php

	require_once ('DB_Functions.php');
	
	//connection to db and constructor of function class
	$db = new DB_Functions();
	
	$response = array("error" => FALSE); //response
	
	//isset function is applicable for variable is set or not
	var_dump($_POST);
	if(isset($_POST['ObjUser'])) {
		
		//got details
		$objUser = json_decode($_POST['ObjUser']); //convert json string to object
		$username = $objUser['User_Reg_No'];
		$email = $objUser['User_Email'];
		$password = $objUser['User_Password'];
		$isValid = $db->isValidEmail($email);

		//check user exists or not
		if($db->checkifuserexisted($username)){
			
			//already existed username in db
			$response["error"] = TRUE;
			$response["error_msg"] = "User already existed with " . $email;
			echo json_encode($response);
			
		}else{
			if($isValid){
				$user = $db->storeUsersData(array($fullname, $username, $email, $password, $confirmpassword); //create new user
				if($user != false){
					   	$verify = $db->sendemailverify($email, $username, $password); //send email for verification
						//show response in json format (JAVASCRIPT OBJECT NOTATION)
						$response["error"] = FALSE;
						$response["result"] = $user;
						echo json_encode($response);
					}else{
						// failed to register account
						$response["error"] = TRUE;
						$response["error_msg"] = "failed to register account";
						echo json_encode($response);
					}
			}else {
				   // email match failed
					$response["error"] = TRUE;
					$response["error_msg"] = "enter valid email";
					echo json_encode($response);
			}
			
		}
	}else{
		//required parameters missing
		$response["error"] = TRUE;
		$response["error_msg"] = "Required parameters is missing!";
		echo json_encode($response);
	}

?>