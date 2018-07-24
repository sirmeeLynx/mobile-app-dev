<?php

	class DB_Functions {
		
		private $Conn;
		
		//constructor
		function __construct(){
			
			require_once ('Connect.php'); 
			
			//connection to database	
			$db = new Connect();
			$this->conn = $db->connect();
		}
		
		// destructor
		function __destruct() {
         
		}
		
		///////////////////////////////// LOGIN SYSTEM /////////////////////////////
		
		/*
		*
		* STUDENT SECTION 
		*
		*
		*/
		
		//store student data in db with encrypted_password
		public function storeUsersData($objUser){
			$email = $objUser["User_Email"];
			$password == $objUser["User_Password"];
			$hash = $this->hashSSHA($password); //hashing pashword for encryption
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$token = mt_rand(1000,10000); // generate random no between 0-1000 in md5
			$sql = "INSERT INTO users(User_Reg_No, User_Email, User_Password, User_Salt, User_FirstName, User_LastName, User_OtherName,
			 User_Token, User_Is_Admin, User_Is_Student, User_Is_Lecturer) 
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //insert data into user table
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("sssssssssss", $objUser["User_Name"], $email, $encrypted_password, $salt, $objUser["User_FirstName"], $objUser["User_LastName"], $objUser["User_OtherName"],
				$token, $objUser["User_Is_Admin"],$objUser["User_Is_Student"],$objUser["User_Is_Lecturer"]); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close, 
				//check if data is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM Users WHERE email = ?");
					$push->bind_param("s", $email);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
			
		}
		
		// return username and password from db for student
		public function getuserData($username, $password){
			$push = $this->conn->prepare("SELECT * FROM Users WHERE User_Reg_No = ? ");
			$push->bind_param("ss", $username);
			if ($push->execute()) {
				$user = $push->get_result()->fetch_assoc();
				$push->close();
				// verifying user password
				$salt = $user['User_Salt'];
				$encrypted_password = $user['User_Password'];
				$hash = $this->checkhashSSHA($salt, $password);
				// check for password equality
				if ($encrypted_password == $hash) {
					// user authentication details are correct
					return $user;
				}else{
					return false;
				}
            }else {
				return NULL;
			}
		}
		
		//check if user's data present in db using student
		public function checkifuserexisted($username){
			$check = $this->conn->prepare("SELECT User_Reg_No FROM Users WHERE User_Reg_No = ?");
			$check->bind_param("s", $username);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close(); // user is existed
				return true;
            }else {
				return false; // user is not existed
			}
		}
		
		//check if user's grno present in db student 
		public function checkifusersgrexisted($grno){
			$check = $this->conn->prepare("SELECT grno FROM student WHERE grno = ?");
			$check->bind_param("s", $grno);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		//send email verification for student
		public function sendemailverify($username){		
			//Fetch result 
			$query = $this->conn->prepare("SELECT User_Email FROM Users WHERE User_Reg_No = ?"); 		
			$query = bind_param("s",$username);
			if($query->execute()){
				$user = $query->get_result()->fetch_assoc();
				$query->close();
				$to = $email;
				$subject = 'Signup | Verification';
				$message = 'Thanks for signing up! Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below. 
				------------------------
					Username: '.$username.'
					Password: '.$user["User_Password"].'
					FullName: '.implode(" ", array($user["User_LastName"],$User["User_FirstName"],$User["User_OtherName"])). '
				------------------------
				Please click this link to activate your account:
				http://slrtceapp.000webhostapp.com/verify.php?email='.$user["User_Email"].'&token='.$user["User_Token"].'';
						
				$headers = 'From:noreply@FPICMS pocket app' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
				
			}else{
				echo "error";
			}
		}
		
		//check email verfied or not student
		public function checkuseractived($username){
			$search = mysqli_query($this->conn, "SELECT User_Reg_No, User_Is_Verified FROM Users WHERE User_Reg_No='".$username."' AND User_Is_Verified='1'"); 
			$match  = mysqli_num_rows($search);
			if($match > 0){
				return $search;
			}else{
				return false;
			}
		}
		
		//Forgot password student
		public function forgotPassword($password, $confirmpassword, $username){
			$password == $confirmpassword;
			$hash = $this->hashSSHA($password); //hashing pashword for encryption
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$sql = "UPDATE Users SET User_Password= ?, salt= ? WHERE User_Reg_No = ?"; //update query
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("sss", $encrypted_password, $salt, $username); // bind query
				$result = $push->execute(); //finally execute.
				$result = $push->get_result()->fetch_assoc();
				$push->close(); //close
				return $result;
			}else{

			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		//send email notification if password is changed
		public function sendemailnotify($username, $password){
			//Fetch result 
			$getemail = mysqli_query($this->conn,"SELECT User_Email FROM Users WHERE User_Reg_No='".$username."'");  //query for email from user		
			$match  = mysqli_fetch_row($getemail);
			$email = $match[0]; 
			if($email > 0){
				$to = $email;
				$subject = 'Password Changed';
				$message = 'Your new password is...  
				------------------------
					Username: '.$username.'
					Password: '.$password.'
				------------------------
				You can manage your credential anytime in student pocket app';
				$headers = 'From:noreply@FPICMS pocket app' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
			}else{
				echo "error";
			}
		}
		
		/*
		*
		* STUDENT SECTION ENDS
		*
		*
		*/
		
		
		/*
		*
		* TEACHER SECTION 
		*
		*
		*/
		
		//store teacher data in db with encrypted_password
		public function storeTeachersData($fullname, $username, $email, $password, $confirmpassword){
			$uuid = uniqid('', true);
			$password == $confirmpassword;
			$hash = $this->hashSSHA($password);
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$verify = md5(mt_rand(0,1000)); // generate random no between 0-1000 in md5
			$teacherid = mt_rand(10000,100000); //generate random no between 1000-10000 (such that user will get 4 to 5 digit)
			$sql = "INSERT INTO teacher(unique_id, username, email, encrypted_password, salt, created_at, updated_at, teacherid, hash, active, fullname) VALUES(?, ?, ?, ?, ?, NOW(), null, ?, ?, 0, ?)"; //insert data into student table
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ssssssss", $uuid, $username, $email, $encrypted_password, $salt, $teacherid, $verify, $fullname); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if data is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM teacher WHERE email = ?");
					$push->bind_param("s", $email);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
			
		}

		
		// return username and password from db teacher
		public function getteacherData($username, $password, $teacherid){
			$push = $this->conn->prepare("SELECT * FROM teacher WHERE username = ? AND teacherid = ? ");
			$push->bind_param("ss", $username, $teacherid);
			if ($push->execute()) {
				$user = $push->get_result()->fetch_assoc();
				$push->close();
				// verifying user password
				$salt = $user['salt'];
				$encrypted_password = $user['encrypted_password'];
				$hash = $this->checkhashSSHA($salt, $password);
				// check for password equality
				if ($encrypted_password == $hash) {
					// user authentication details are correct
					return $user;
				}else{
					return false;
				}
            }else {
				return NULL;
			}
		}
		
	
		//check if user's data present in db using username in teacher
		public function checkifteacherexisted($username){
			$check = $this->conn->prepare("SELECT * FROM teacher WHERE username = ?");
			$check->bind_param("s", $username);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close(); // user is existed
				return true;
            }else {
				return false; // user is not existed
			}
		}
		
		//check if user's grno present in db teacher
		public function checkifteacheridgrexisted($teacherid){
			$check = $this->conn->prepare("SELECT teacherid FROM teacher WHERE teacherid =? "); 
			$check->bind_param("s", $teacherid);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close(); // teacherid is existed
				return true;
            }else {
				return false; // teacherid is not existed
			}
		}
		
		//send email verification for teacher
		public function sendemailverifyforteacher($email, $username, $password){
			
			//Fetch result 
			$result = mysqli_query($this->conn,"SELECT teacherid FROM teacher WHERE email='".$email."'"); 
			$hashresult = mysqli_query($this->conn,"SELECT hash FROM teacher WHERE email='".$email."'"); 
		
			$match  = mysqli_fetch_row($result);
			$matchs  = mysqli_fetch_row($hashresult);
			$teacherid = $match[0]; 
			$hash = $matchs[0];
			if($teacherid > 0 && $hash >0){
				$to = $email;
				$subject = 'Signup | Verification';
				$message = 'Thanks for signing up! Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below. 
				------------------------
					Username: '.$username.'
					Password: '.$password.'
					TeacherID: '.$teacherid.'
				------------------------
				Please click this link to activate your account:
				http://slrtceapp.000webhostapp.com/otherverify.php?email='.$email.'&hash='.$hash.'';
						
				$headers = 'From:noreply@192.168.1.14' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
			}else{
				return false;
			}
		}
		
		//check email verfied or not teacher
		public function checkteacheractived($username){
			$search = mysqli_query($this->conn, "SELECT username, active FROM teacher WHERE username='".$username."' AND active='1'"); 
			$match  = mysqli_num_rows($search);
			if($match > 0){
				$search->close();
				return true;
			}else{
				return false;
			}
		}
		
		/**
		*
		* Forgot password Teacher
		**/
		public function forgotPasswordT($password, $confirmpassword, $teacherid, $username){
			$password == $confirmpassword;
			$hash = $this->hashSSHA($password); //hashing pashword for encryption
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$sql = "UPDATE teacher SET encrypted_password= ?, salt= ? WHERE teacherid = ? AND username = ?"; //update query
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ssss", $encrypted_password, $salt, $teacherid, $username); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
			}else{

			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		//send email notification if password is changed
		public function sendemailnotifyT($username, $password){
			
			//Fetch result 
			$getemail = mysqli_query($this->conn,"SELECT email FROM teacher WHERE username='".$username."'");  //query for email from user
		
			$match  = mysqli_fetch_row($getemail);
			$email = $match[0]; 
			if($email > 0){
				$to = $getemail;
				$subject = 'Password Changed';
				$message = 'Your new password is...  
				------------------------
					Username: '.$username.'
					Password: '.$password.'
				------------------------
				You can manage your credential anytime in student pocket app';
				$headers = 'From:noreply@Slrtce pocket app' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
			}else{
				echo "error";
			}
		}
		
		/*
		*
		* TEACHER SECTION ENDS
		*
		*
		*/
		
		/*
		*
		* NON-TEACHING SECTION START
		*
		*
		*/
		
		//store non-teaching data in db with encrypted_password
		public function storeUsersDataNonT($fullname, $username, $email, $password, $confirmpassword){
			$uuid = uniqid('', true);
			$password == $confirmpassword;
			$hash = $this->hashSSHA($password);
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$verify = md5(mt_rand(0,1000)); // generate random no between 0-1000 in md5
			$nonteachid = mt_rand(100,1000); //generate random no between 1000-10000 (such that user will get 4 to 5 digit)
			$sql = "INSERT INTO nonteacher(unique_id, username, email, encrypted_password, salt, created_at, updated_at, nonteachid, hash, active, fullname) VALUES(?, ?, ?, ?, ?, NOW(), null, ?, ?, 0, ?)"; //insert data into student table
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ssssssss", $uuid, $username, $email, $encrypted_password, $salt, $nonteachid, $verify, $fullname); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if data is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM nonteacher WHERE email = ?");
					$push->bind_param("s", $email);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
			
		}
		
		// return username and password from db for non-teaching
		public function getuserDataNonT($username, $password, $nonteachid){
			$push = $this->conn->prepare("SELECT * FROM nonteacher WHERE username = ? AND nonteachid = ? ");
			$push->bind_param("ss", $username, $nonteachid);
			if ($push->execute()) {
				$user = $push->get_result()->fetch_assoc();
				$push->close();
				// verifying user password
				$salt = $user['salt'];
				$encrypted_password = $user['encrypted_password'];
				$hash = $this->checkhashSSHA($salt, $password);
				// check for password equality
				if ($encrypted_password == $hash) {
					// user authentication details are correct
					return $user;
				}else{
					return false;
				}
            }else {
				return NULL;
			}
		}
		
		//check if user's data present in db using non-teaching
		public function checkifuserexistedNonT($username){
			$check = $this->conn->prepare("SELECT username FROM nonteacher WHERE username = ?");
			$check->bind_param("s", $username);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close(); // user is existed
				return true;
            }else {
				return false; // user is not existed
			}
		}
		
		//check if user's nonteachid present in db non-teaching 
		public function checkifuserexistedNonid($nonteachid){
			$check = $this->conn->prepare("SELECT nonteachid FROM nonteacher WHERE nonteachid = ?");
			$check->bind_param("s", $nonteachid);
			$check->execute();
			$check->store_result();
			if ($check->num_rows>0) {
				$check->close();
				return true; // nonteachid is existed
            }else {
				return false; // nonteachid is not existed
			}
		}
		
		//send email verification for non-teaching 
		public function sendemailverifynonT($email, $username, $password){
			
			//Fetch result 
			$result = mysqli_query($this->conn,"SELECT nonteachid FROM nonteacher WHERE email='".$email."'"); 
			$hashresult = mysqli_query($this->conn,"SELECT hash FROM nonteacher WHERE email='".$email."'"); 
		
			$match  = mysqli_fetch_row($result);
			$matchs  = mysqli_fetch_row($hashresult);
			$nonteachid = $match[0]; 
			$hash = $matchs[0];
			if($nonteachid > 0 && $hash >0){
				$to = $email;
				$subject = 'Signup | Verification';
				$message = 'Thanks for signing up! Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below. 
				------------------------
					Username: '.$username.'
					Password: '.$password.'
					NTID: '.$nonteachid. '
				------------------------
				Please click this link to activate your account:
				http://192.168.0.112/demo/nontverify.php?email='.$email.'&hash='.$hash.'';	
				$headers = 'From:noreply@Slrtce pocket app' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
				
			}else{
				echo "error";
			}
		}
		
		//check email verfied or not non-teaching
		public function checkuseractivedNont($username){
			$search = mysqli_query($this->conn, "SELECT username, active FROM nonteacher WHERE username='".$username."' AND active='1'"); 
			$match  = mysqli_num_rows($search);
			if($match > 0){
				return $search;
			}else{
				return false;
			}
		}
		
		/**
		*
		* Forgot password student
		**/
		public function forgotPasswordNT($password, $confirmpassword, $nonteachid, $username){
			$password == $confirmpassword;
			$hash = $this->hashSSHA($password); //hashing pashword for encryption
			$encrypted_password = $hash["encrypted"]; //encrypted password is stored
			$salt = $hash["salt"]; 
			$sql = "UPDATE nonteacher SET encrypted_password= ?, salt= ? WHERE nonteachid = ? AND username = ?"; //update query
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ssss", $encrypted_password, $salt, $nonteachid, $username); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
			}else{

			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		//send email notification if password is changed
		public function sendemailnotifyNT($username, $password){
			
			//Fetch result 
			$getemail = mysqli_query($this->conn,"SELECT email FROM nonteacher WHERE username='".$username."'");  //query for email from user
		
			$match  = mysqli_fetch_row($getemail);
			$email = $match[0]; 
			if($email > 0){
				$to = $getemail;
				$subject = 'Password Changed';
				$message = 'Your new password is...  
				------------------------
					Username: '.$username.'
					Password: '.$password.'
				------------------------
				You can manage your credential anytime in student pocket app';
				$headers = 'From:noreply@Slrtce pocket app' . "\r\n"; //setup header for mail
				mail($to, $subject, $message, $headers); // Send our email
			}else{
				echo "error";
			}
		}
		/*
		*
		* NON-TEACHING SECTION ENDS
		*
		*
		*/
		
		/*
		*
		* COMMON STUFF
		*
		*
		*/
		
		//check valid email
		public function isValidEmail($email){
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }
		
		//encrypted password 
		public function hashSSHA($password) {
			$salt = sha1(rand()); //generate random sha1
			$salt = substr($salt, 0, 10);
			$encrypted = base64_encode(sha1($password . $salt, true) . $salt); //using php's default encryption method for password
			$hash = array("salt" => $salt, "encrypted" => $encrypted);
			return $hash;
		}
		
		//check passowrd
		public function checkhashSSHA($salt, $password) {
			$hash = base64_encode(sha1($password . $salt, true) . $salt);
			return $hash;
		}
	
		//////////////////////////// Upload Work /////////////////////////////
		
		/**
		*
		* Store nTeacher and Teacher upload data in db (Notices)
		*/
		public function storeUploadData($name, $url){
			$sql = "INSERT into upload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM upload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		/**
		*
		* Store Teacher upload data in Fedb (Notices)
		*/
		public function storeCmpnUploadData($name, $url){
			$sql = "INSERT into cmpnupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM cmpnupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		public function storeItUploadData($name, $url){
			$sql = "INSERT into itupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM itupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		public function storeEtrxUploadData($name, $url){
			$sql = "INSERT into extrxupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM etrxupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		public function storeExtcUploadData($name, $url){
			$sql = "INSERT into extcupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM extcupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
			
		public function storeMechUploadData($name, $url){
			$sql = "INSERT into mechupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM mechupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
			
		public function storeCivilUploadData($name, $url){
			$sql = "INSERT into civilupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM civilupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		/**
		*
		* check if file exists in db or not (Notices)
		**/
		public function checkFileExists($name, $url){
			$sql = "SELECT * FROM upload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		public function checkFileCmpnExists($name, $url){
			$sql = "SELECT * FROM cmpnupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		
		public function checkFileExtcExists($name, $url){
			$sql = "SELECT * FROM extcupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		
		public function checkFileEtrxExists($name, $url){
			$sql = "SELECT * FROM Etrxupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		
		public function checkFileMechExists($name, $url){
			$sql = "SELECT * FROM mechupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		public function checkFileCivilExists($name, $url){
			$sql = "SELECT * FROM civilupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		public function checkFileItExists($name, $url){
			$sql = "SELECT * FROM itupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
		/**
		*
		* Store nTeacher and Teacher upload data in db (General)
		*/
		public function storeGUploadData($name, $url){
			$sql = "INSERT into gupload(name, url) VALUES (?,?)";
			//prepare query
			if($push = $this->conn->prepare($sql)){
				$push->bind_param("ss", $name, $url); // bind query
				$result = $push->execute(); //finally execute.
				$push->close(); //close
				//check if file url is stored successfully in database or not
				if($result){
					$push = $this->conn->prepare("SELECT * FROM gupload WHERE url = ?");
					$push->bind_param("s", $url);
					$push->execute();
					$user = $push->get_result()->fetch_assoc();
					$push->close();
					return $user;
				}else {
					return false;
				}
			}else{
			   //error !! don't go further
			   var_dump($this->conn->error);
			}
		}
		
		/**
		*
		* check if file exists in db or not (General)
		**/
		public function checkGFileExists($name, $url){
			$sql = "SELECT * FROM gupload WHERE name = ? AND url = ?";
			$query = $this->conn->prepare($sql);
			$query->bind_param("ss", $name, $url);
			$query->execute();
			$query->store_result();
			if ($query->num_rows>0) {
				$query->close();
				return true;
            }else {
				return false; // grno is not existed
			}
		}
		
	}
?>