<?php

	require_once ('Connect.php'); 
			
	//connection to database	
	$db = new Connect();
	$Conn = $db->connect();
	
	if(isset($_GET['username']) && !empty($_GET['username']) AND isset($_GET['token']) && !empty($_GET['token'])){
		$username = $_GET['username']; $token = $_GET['token'];
		// Verify username
		($search = mysqli_query($Conn,"SELECT User_ID FROM Users WHERE User_Reg_No='".$username."' AND User_Token='".$token."' AND User_Is_Verified='0'")) or trigger_error($search->error,E_USER_ERROR); 
		$User_ID = mysqli_fetch_array($search,MYSQLI_ASSOC)["User_ID"];	
		if($User_ID){
			$update_query = sprintf("UPDATE Users SET User_Is_Verified = '1', User_Token = null WHERE User_ID = '%s'",$User_ID);
			echo "Successfully Verified";
			mysqli_query($Conn, $update_query);
		}else{
			//account has already been activated.
			echo "Either account is not valid or your account has been verified already.";
		}	

	}else{
		// Invalid approach
		echo "Invalid approach";
	}

?>