<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		
			extract($_POST);		
			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				if($_SESSION['login_type'] != 1){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
					return 1;
			}else{
				return 3;
			}
	}
	function login2(){
		try {
			extract($_POST);
			if(isset($email))
				$username = $email;

			// Sanitize inputs
			$username = $this->db->real_escape_string($username);
			$password = $this->db->real_escape_string($password);

			// Use prepared statement for security
			$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
			$stmt->bind_param("ss", $username, $username);
			$stmt->execute();
			$result = $stmt->get_result();

			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				
				// Verify password
				if(md5($password) === $row['password']){
					// Set user session data
					foreach ($row as $key => $value) {
						if($key != 'password' && !is_numeric($key))
							$_SESSION['login_'.$key] = $value;
					}

					// If user is an alumnus, get their bio
					if(isset($row['alumnus_id']) && $row['alumnus_id'] > 0){
						$bio_stmt = $this->db->prepare("SELECT * FROM alumnus_bio WHERE id = ?");
						$bio_stmt->bind_param("i", $row['alumnus_id']);
						$bio_stmt->execute();
						$bio = $bio_stmt->get_result();

						if($bio->num_rows > 0){
							$bio_data = $bio->fetch_assoc();
							foreach ($bio_data as $key => $value) {
								if(!is_numeric($key))
									$_SESSION['bio'][$key] = $value;
							}
						}
					}

					// Check user type and status
					if($row['type'] == 1) { // Admin
						return json_encode(['status' => 'success', 'message' => 'Login successful']);
					} else {
						// For regular users, check status
						if(isset($_SESSION['bio']['status']) && $_SESSION['bio']['status'] != 1){
							// Account not approved
							foreach ($_SESSION as $key => $value) {
								unset($_SESSION[$key]);
							}
							return json_encode([
								'status' => 'error',
								'code' => 2,
								'message' => 'Your account is pending approval'
							]);
						}
						return json_encode(['status' => 'success', 'message' => 'Login successful']);
					}
				} else {
					return json_encode([
						'status' => 'error',
						'code' => 3,
						'message' => 'Invalid password'
					]);
				}
			} else {
				return json_encode([
					'status' => 'error',
					'code' => 4,
					'message' => 'User not found'
				]);
			}
		} catch (Exception $e) {
			error_log("Login Error: " . $e->getMessage());
			return json_encode([
				'status' => 'error',
				'code' => 500,
				'message' => 'A server error occurred. Please try again later.'
			]);
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		
		// Sanitize inputs
		$name = $this->db->real_escape_string(trim($name));
		$username = $this->db->real_escape_string(trim($username));
		$type = (int)$type;
		$id = isset($id) ? (int)$id : 0;
		
		// Validate required fields
		if(empty($name) || empty($username) || !in_array($type, [1, 4])){
			return 0; // Invalid input
		}
		
		// Build data string
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		
		// Handle password
		if(!empty($password)){
			$password_hash = md5($password);
			$data .= ", password = '$password_hash' ";
		}
		
		$data .= ", type = '$type' ";
		
		// Set establishment_id (0 for admin and registrar users)
		$establishment_id = 0;
		$data .= ", establishment_id = '$establishment_id' ";
		
		// Check for duplicate username
		$chk = $this->db->query("SELECT * FROM users WHERE username = '$username' AND id != '$id'")->num_rows;
		if($chk > 0){
			return 2; // Username already exists
		}
		
		// Save or update user
		if(empty($id)){
			// Create new user
			$save = $this->db->query("INSERT INTO users SET ".$data);
		} else {
			// Update existing user
			$save = $this->db->query("UPDATE users SET ".$data." WHERE id = ".$id);
		}
		
		if($save){
			return 1; // Success
		} else {
			return 0; // Database error
		}
	}
	function delete_user(){
		extract($_POST);
		$id = (int)$id;
		
		// Validate input
		if($id <= 0) {
			return 0;
		}
		
		// Check if user exists and is admin/registrar type
		$check = $this->db->query("SELECT type FROM users WHERE id = $id");
		if($check->num_rows == 0) {
			return 0; // User not found
		}
		
		$user_data = $check->fetch_assoc();
		if(!in_array($user_data['type'], [1, 4])) {
			return 0; // Not admin or registrar user
		}
		
		// Delete the user (simple deletion for admin/registrar users)
		$delete = $this->db->query("DELETE FROM users WHERE id = $id AND type IN (1, 4)");
		
		if($delete && $this->db->affected_rows > 0) {
			return 1; // Success
		} else {
			return 0; // Failed to delete
		}
	}
	function signup(){
    try {
        extract($_POST);
        
        // Basic validation
        if(empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            return json_encode([
                'status' => 'error', 
                'message' => 'Please fill all required fields'
            ]);
        }

        // Begin transaction
        $this->db->begin_transaction();

        try {
            // First verify if the alumni ID is valid and not used
            $alumni_id_clean = $this->db->real_escape_string($alumni_id);
            $check_alumni = $this->db->query("SELECT * FROM alumni_ids WHERE alumni_id = '$alumni_id_clean' AND is_used = 0");
            if($check_alumni->num_rows == 0) {
                throw new Exception('Invalid or already used Alumni ID');
            }

            // Check if email exists
            $email_clean = $this->db->real_escape_string($email);
            $check_email = $this->db->query("SELECT * FROM users WHERE username = '$email_clean'");
            if($check_email->num_rows > 0) {
                throw new Exception('Email already exists');
            }

            // Create user account first
            $fullname = $this->db->real_escape_string($firstname . ' ' . $lastname);
            $hashed_password = md5($password);
            
            $save_user = $this->db->query("INSERT INTO users (name, username, password, type) 
                VALUES ('$fullname', '$email_clean', '$hashed_password', 3)");
                
            if(!$save_user) {
                throw new Exception("Error creating user: " . $this->db->error);
            }
            
            $user_id = $this->db->insert_id;

            // Handle image upload
            $avatar = '';
            if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
                $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
                if(!move_uploaded_file($_FILES['img']['tmp_name'],'../assets/uploads/'. $fname)) {
                    throw new Exception("Error uploading file");
                }
                $avatar = $fname;
            } elseif(isset($_POST['profileCapture'])) {
                $img_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['profileCapture']));
                $fname = strtotime(date('y-m-d H:i')).'_captured.png';
                if(!file_put_contents('../assets/uploads/'. $fname, $img_data)) {
                    throw new Exception("Error saving captured image");
                }
                $avatar = $fname;
            }

            // Insert alumnus bio
            $bio_sql = "INSERT INTO alumnus_bio SET 
                alumni_id = '".$this->db->real_escape_string($alumni_id)."',
                firstname = '".$this->db->real_escape_string($firstname)."',
                middlename = '".$this->db->real_escape_string($middlename)."',
                lastname = '".$this->db->real_escape_string($lastname)."',
                suffixname = '".$this->db->real_escape_string($suffixname)."',
                gender = '".$this->db->real_escape_string($gender)."',
                batch = '".$this->db->real_escape_string($batch)."',
                course_id = '".$this->db->real_escape_string($course_id)."',
                email = '".$this->db->real_escape_string($email)."',
                connected_to = '".$this->db->real_escape_string($connected_to)."',
                contact_no = '".$this->db->real_escape_string($contact_no)."',
                company_address = '".$this->db->real_escape_string($company_address)."',
                company_email = '".$this->db->real_escape_string($company_email)."',
                birthdate = '".$this->db->real_escape_string($birthdate)."',
                address = '".$this->db->real_escape_string($address)."',
                avatar = '".$this->db->real_escape_string($avatar)."',
                status = 1";

            $save_bio = $this->db->query($bio_sql);
            
            if(!$save_bio) {
                throw new Exception("Error saving alumni info: " . $this->db->error);
            }

            $alumnus_id = $this->db->insert_id;

            // Update user with alumnus_id
            $update_user = $this->db->query("UPDATE users SET alumnus_id = '$alumnus_id' WHERE id = '$user_id'");
            if(!$update_user) {
                throw new Exception("Error updating user: " . $this->db->error);
            }

            // Mark alumni ID as used
            $update_alumni_id = $this->db->query("UPDATE alumni_ids SET is_used = 1 WHERE alumni_id = '$alumni_id_clean'");
            if(!$update_alumni_id) {
                throw new Exception("Error updating alumni ID status");
            }

            $this->db->commit();
            return json_encode(['status' => 'success', 'message' => 'Account created successfully']);

        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } catch (Exception $e) {
        return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
		 		else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['settings'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_course(){
		extract($_POST);
		$data = " course = '$course' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO courses set $data");
			}else{
				$save = $this->db->query("UPDATE courses set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_course(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function update_alumni_acc(){
		extract($_POST);
		$update = $this->db->query("UPDATE alumnus_bio set status = $status where id = $id");
		if($update)
			return 1;
	}
	function save_gallery(){
		extract($_POST);
		$img = array();
  		$fpath = 'assets/uploads/gallery';
		$files= is_dir($fpath) ? scandir($fpath) : array();
		foreach($files as $val){
			if(!in_array($val, array('.','..'))){
				$n = explode('_',$val);
				$img[$n[0]] = $val;
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO gallery set about = '$about' ");
			if($save){
				$id = $this->db->insert_id;
				$folder = "assets/uploads/gallery/";
				$file = explode('.',$_FILES['img']['name']);
				$file = end($file);
				if(is_file($folder.$id.'/_img'.'.'.$file))
					unlink($folder.$id.'/_img'.'.'.$file);
				if(isset($img[$id]))
						unlink($folder.$img[$id]);
				if($_FILES['img']['tmp_name'] != ''){
					$fname = $id.'_img'.'.'.$file;
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/gallery/'. $fname);
				}
			}
		}else{
			$save = $this->db->query("UPDATE gallery set about = '$about' where id=".$id);
			if($save){
				if($_FILES['img']['tmp_name'] != ''){
					$folder = "assets/uploads/gallery/";
					$file = explode('.',$_FILES['img']['name']);
					$file = end($file);
					if(is_file($folder.$id.'/_img'.'.'.$file))
						unlink($folder.$id.'/_img'.'.'.$file);
					if(isset($img[$id]))
						unlink($folder.$img[$id]);
					$fname = $id.'_img'.'.'.$file;
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/gallery/'. $fname);
				}
			}
		}
		if($save)
			return 1;
	}
	function delete_gallery(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM gallery where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_career(){
		extract($_POST);
		$data = " company = '$company' ";
		$data .= ", job_title = '$title' ";
		$data .= ", location = '$location' ";
		$data .= ", description = '".htmlentities(str_replace("'","&#x2019;",$description))."' ";

		if(empty($id)){
			// echo "INSERT INTO careers set ".$data;
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO careers set ".$data);
		}else{
			$save = $this->db->query("UPDATE careers set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_career(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM careers where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_forum(){
		extract($_POST);
		$data = " title = '$title' ";
		$data .= ", description = '".htmlentities(str_replace("'","&#x2019;",$description))."' ";

		if(empty($id)){
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO forum_topics set ".$data);
		}else{
			$save = $this->db->query("UPDATE forum_topics set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_forum(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_topics where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_comment(){
		extract($_POST);
		$data = " comment = '".htmlentities(str_replace("'","&#x2019;",$comment))."' ";

		if(empty($id)){
			$data .= ", topic_id = '$topic_id' ";
			$data .= ", user_id = '{$_SESSION['login_id']}' ";
			$save = $this->db->query("INSERT INTO forum_comments set ".$data);
		}else{
			$save = $this->db->query("UPDATE forum_comments set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_comment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM forum_comments where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_event(){
		extract($_POST);
		// sanitize inputs
		$title = isset($title) ? $this->db->real_escape_string($title) : '';
		$schedule = isset($schedule) ? $this->db->real_escape_string($schedule) : '';
		$content = isset($content) ? $content : '';
		$data = " title = '". $title ."' ";
		$data .= ", schedule = '". $schedule ."' ";
		$data .= ", content = '". htmlentities(str_replace("'","&#x2019;", $content)) ."' ";
		if(isset($_FILES['banner']) && isset($_FILES['banner']['tmp_name']) && $_FILES['banner']['tmp_name'] != ''){
			$_FILES['banner']['name'] = str_replace(array("(",")"," "), '', $_FILES['banner']['name']);
			$ext = pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION);
			$fname = strtotime(date('y-m-d H:i')).'_'.rand(1,9).'_event.'.$ext;
			
			// Create uploads directory if it doesn't exist
			$upload_dir = '../uploads/';
			if (!file_exists($upload_dir)) {
				mkdir($upload_dir, 0755, true);
			}
			
			$move = move_uploaded_file($_FILES['banner']['tmp_name'], $upload_dir . $fname);
			if($move) {
				$data .= ", banner = '".$fname."' ";
			}
		}
		if(empty($id)){
			// Ensure 'approved' column exists (best-effort)
			$col = $this->db->query("SHOW COLUMNS FROM events LIKE 'approved'");
			if($col->num_rows == 0){
				$this->db->query("ALTER TABLE events ADD approved TINYINT(1) NOT NULL DEFAULT 0");
			}
			// If admin is creating the event, auto-approve; otherwise default to pending
			$approved = 0;
			if(isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1){
				$approved = 1;
			}
			$data .= ", approved = ".intval($approved).
" ";
			// Attach user_id if available
			if(isset($_SESSION['login_id'])){
				$data .= ", user_id = '".intval($_SESSION['login_id'])."' ";
			}
			$save = $this->db->query("INSERT INTO events set ".$data);
		}else{
			$save = $this->db->query("UPDATE events set ".$data." where id=".$id);
		}
		if($save)
			return 1;
		return 0;
	}
	function delete_event(){
		extract($_POST);
		$id = isset($id) ? intval($id) : 0;
		if($id <= 0) return 0;
		// Fetch event to check owner and banner
		$stmt = $this->db->prepare("SELECT user_id, banner FROM events WHERE id = ?");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$res = $stmt->get_result();
		if(!$res || $res->num_rows == 0) return 0;
		$erow = $res->fetch_assoc();
		$owner = isset($erow['user_id']) ? intval($erow['user_id']) : 0;
		$banner = isset($erow['banner']) ? $erow['banner'] : '';
		// Permission: allow if admin (login_type==1) or owner of the event
		if(!(isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1) && !(isset($_SESSION['login_id']) && $_SESSION['login_id'] == $owner)){
			return 0;
		}
		// Remove related records (commits, likes)
		$this->db->query("DELETE FROM event_commits WHERE event_id = " . $id);
		$this->db->query("DELETE FROM event_likes WHERE event_id = " . $id);
		// Attempt to remove banner file from likely locations
		if(!empty($banner)){
			$paths = [__DIR__ . '/../uploads/' . $banner, __DIR__ . '/assets/uploads/' . $banner, __DIR__ . '/uploads/' . $banner];
			foreach($paths as $p){
				if(file_exists($p)){
					@unlink($p);
					break;
				}
			}
		}
		$delete = $this->db->query("DELETE FROM events where id = " . $id);
		if($delete){
			return 1;
		}
		return 0;
	}

	/**
	 * Approve / Unapprove an event
	 * Expects POST: id (int), approved (0|1)
	 */
	function approve_event(){
		extract($_POST);
		$id = intval($id);
		$approved = intval($approved) ? 1 : 0;
		if($id <= 0) return 0;
		// Ensure column exists (best-effort)
		$col = $this->db->query("SHOW COLUMNS FROM events LIKE 'approved'");
		if($col->num_rows == 0){
			$this->db->query("ALTER TABLE events ADD approved TINYINT(1) NOT NULL DEFAULT 0");
		}
		$upd = $this->db->query("UPDATE events SET approved = $approved WHERE id = $id");
		if($upd) return 1;
		return 0;
	}
	
	function participate(){
		extract($_POST);
		$data = " event_id = '$event_id' ";
		$data .= ", user_id = '{$_SESSION['login_id']}' ";
		$commit = $this->db->query("INSERT INTO event_commits set $data ");
		if($commit)
			return 1;

	}
}