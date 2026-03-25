<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/security.php';

class Action {
    private $db;

    public function __construct() {
        ob_start();
        include __DIR__ . '/db_connect.php';
        $this->db = $conn;
    }

    public function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    // ── Authentication ──────────────────────────────────────────────

    function login() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return 3;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND type = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return 3;
        }

        $row = $result->fetch_assoc();

        if (!$this->verifyPassword($password, $row['password'], $row['id'])) {
            return 3;
        }

        foreach ($row as $key => $value) {
            if ($key !== 'password' && !is_numeric($key)) {
                $_SESSION['login_' . $key] = $value;
            }
        }

        if ($_SESSION['login_type'] != 1) {
            $_SESSION = [];
            return 2;
        }

        return 1;
    }

    function login2() {
        try {
            $username = trim($_POST['email'] ?? $_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                return json_encode(['status' => 'error', 'code' => 4, 'message' => 'Please enter username and password']);
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows === 0) {
                return json_encode(['status' => 'error', 'code' => 4, 'message' => 'User not found']);
            }

            $row = $result->fetch_assoc();

            if (!$this->verifyPassword($password, $row['password'], $row['id'])) {
                return json_encode(['status' => 'error', 'code' => 3, 'message' => 'Invalid password']);
            }

            // Set session
            foreach ($row as $key => $value) {
                if ($key !== 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }

            // Load alumni bio if applicable
            if (!empty($row['alumnus_id'])) {
                $bio_stmt = $this->db->prepare("SELECT * FROM alumnus_bio WHERE id = ?");
                $bio_stmt->bind_param("i", $row['alumnus_id']);
                $bio_stmt->execute();
                $bio = $bio_stmt->get_result();
                $bio_stmt->close();

                if ($bio->num_rows > 0) {
                    $bio_data = $bio->fetch_assoc();
                    foreach ($bio_data as $key => $value) {
                        if (!is_numeric($key)) {
                            $_SESSION['bio'][$key] = $value;
                        }
                    }
                }
            }

            // Check admin access
            if ($row['type'] == 1) {
                return json_encode(['status' => 'success', 'message' => 'Login successful']);
            }

            // Check alumni approval status
            if (isset($_SESSION['bio']['status']) && $_SESSION['bio']['status'] != 1) {
                $_SESSION = [];
                return json_encode(['status' => 'error', 'code' => 2, 'message' => 'Your account is pending approval']);
            }

            return json_encode(['status' => 'success', 'message' => 'Login successful']);

        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return json_encode(['status' => 'error', 'code' => 500, 'message' => 'A server error occurred. Please try again later.']);
        }
    }

    function logout() {
        session_destroy();
        $_SESSION = [];
        header("location:login.php");
    }

    function logout2() {
        session_destroy();
        $_SESSION = [];
        header("location:../index.php");
    }

    // ── User Management ─────────────────────────────────────────────

    function save_user() {
        $name = sanitize_string($_POST['name'] ?? '');
        $username = sanitize_string($_POST['username'] ?? '');
        $type = sanitize_int($_POST['type'] ?? 0);
        $id = sanitize_int($_POST['id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($username) || !in_array($type, [1, 4])) {
            return 0;
        }

        // Check duplicate username
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return 2;
        }
        $stmt->close();

        if (empty($id)) {
            // Create new user
            if (empty($password)) return 0;
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (name, username, password, type, establishment_id) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("sssi", $name, $username, $hashed, $type);
        } else {
            // Update existing user
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET name = ?, username = ?, password = ?, type = ? WHERE id = ?");
                $stmt->bind_param("sssii", $name, $username, $hashed, $type, $id);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET name = ?, username = ?, type = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $username, $type, $id);
            }
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_user() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND type IN (1, 4)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0 ? 1 : 0;
    }

    // ── Alumni Signup ───────────────────────────────────────────────

    function signup() {
        try {
            $firstname = sanitize_string($_POST['firstname'] ?? '');
            $lastname = sanitize_string($_POST['lastname'] ?? '');
            $middlename = sanitize_string($_POST['middlename'] ?? '');
            $suffixname = sanitize_string($_POST['suffixname'] ?? '');
            $email = sanitize_email($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $alumni_id = sanitize_string($_POST['alumni_id'] ?? '');
            $gender = sanitize_string($_POST['gender'] ?? '');
            $batch = sanitize_string($_POST['batch'] ?? '');
            $course_id = sanitize_int($_POST['course_id'] ?? 0);
            $connected_to = sanitize_string($_POST['connected_to'] ?? '');
            $contact_no = sanitize_string($_POST['contact_no'] ?? '');
            $company_address = sanitize_string($_POST['company_address'] ?? '');
            $company_email = sanitize_email($_POST['company_email'] ?? '');
            $birthdate = sanitize_string($_POST['birthdate'] ?? '');
            $address = sanitize_string($_POST['address'] ?? '');

            if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
                return json_encode(['status' => 'error', 'message' => 'Please fill all required fields']);
            }

            $this->db->begin_transaction();

            try {
                // Verify alumni ID
                $stmt = $this->db->prepare("SELECT id FROM alumni_ids WHERE alumni_id = ? AND is_used = 0");
                $stmt->bind_param("s", $alumni_id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    $stmt->close();
                    throw new Exception('Invalid or already used Alumni ID');
                }
                $stmt->close();

                // Check duplicate email
                $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $stmt->close();
                    throw new Exception('Email already exists');
                }
                $stmt->close();

                // Create user account
                $fullname = $firstname . ' ' . $lastname;
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare("INSERT INTO users (name, username, password, type) VALUES (?, ?, ?, 3)");
                $stmt->bind_param("sss", $fullname, $email, $hashed_password);
                if (!$stmt->execute()) {
                    throw new Exception("Error creating user account");
                }
                $user_id = $this->db->insert_id;
                $stmt->close();

                // Handle image upload
                $avatar = '';
                if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
                    $check = validate_image_upload($_FILES['img']);
                    if ($check['valid']) {
                        $fname = safe_filename('avatar', $check['ext']);
                        ensure_upload_dir('../assets/uploads/');
                        move_uploaded_file($_FILES['img']['tmp_name'], '../assets/uploads/' . $fname);
                        $avatar = $fname;
                    }
                } elseif (!empty($_POST['profileCapture'])) {
                    $img_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['profileCapture']));
                    if ($img_data !== false) {
                        $fname = safe_filename('capture', 'png');
                        ensure_upload_dir('../assets/uploads/');
                        file_put_contents('../assets/uploads/' . $fname, $img_data);
                        $avatar = $fname;
                    }
                }

                // Insert alumnus bio
                $stmt = $this->db->prepare("INSERT INTO alumnus_bio (alumni_id, firstname, middlename, lastname, suffixname, gender, batch, course_id, email, connected_to, contact_no, company_address, company_email, birthdate, address, avatar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("sssssssisssssssss",
                    $alumni_id, $firstname, $middlename, $lastname, $suffixname,
                    $gender, $batch, $course_id, $email, $connected_to,
                    $contact_no, $company_address, $company_email, $birthdate, $address, $avatar
                );
                if (!$stmt->execute()) {
                    throw new Exception("Error saving alumni info");
                }
                $alumnus_id = $this->db->insert_id;
                $stmt->close();

                // Link user to alumni bio
                $stmt = $this->db->prepare("UPDATE users SET alumnus_id = ? WHERE id = ?");
                $stmt->bind_param("ii", $alumnus_id, $user_id);
                $stmt->execute();
                $stmt->close();

                // Mark alumni ID as used
                $stmt = $this->db->prepare("UPDATE alumni_ids SET is_used = 1 WHERE alumni_id = ?");
                $stmt->bind_param("s", $alumni_id);
                $stmt->execute();
                $stmt->close();

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

    // ── Account Update ──────────────────────────────────────────────

    function update_account() {
        $firstname = sanitize_string($_POST['firstname'] ?? '');
        $lastname = sanitize_string($_POST['lastname'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $login_id = sanitize_int($_SESSION['login_id'] ?? 0);
        $bio_id = sanitize_int($_SESSION['bio']['id'] ?? 0);

        if (empty($firstname) || empty($lastname) || empty($email) || $login_id <= 0) {
            return 0;
        }

        // Check duplicate email
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $email, $login_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return 2;
        }
        $stmt->close();

        // Update users table
        $fullname = $firstname . ' ' . $lastname;
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET name = ?, username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $fullname, $email, $hashed, $login_id);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, username = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fullname, $email, $login_id);
        }
        $stmt->execute();
        $stmt->close();

        // Update alumnus_bio
        $gender = sanitize_string($_POST['gender'] ?? '');
        $batch = sanitize_string($_POST['batch'] ?? '');
        $course_id = sanitize_int($_POST['course_id'] ?? 0);
        $connected_to = sanitize_string($_POST['connected_to'] ?? '');
        $contact_no = sanitize_string($_POST['contact_no'] ?? '');
        $company_address = sanitize_string($_POST['company_address'] ?? '');
        $company_email = sanitize_email($_POST['company_email'] ?? '');
        $birthdate = sanitize_string($_POST['birthdate'] ?? '');
        $address = sanitize_string($_POST['address'] ?? '');
        $middlename = sanitize_string($_POST['middlename'] ?? '');
        $suffixname = sanitize_string($_POST['suffixname'] ?? '');

        // Handle avatar upload
        $avatar_sql = '';
        $avatar = '';
        $has_avatar = false;
        if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
            $check = validate_image_upload($_FILES['img']);
            if ($check['valid']) {
                $avatar = safe_filename('avatar', $check['ext']);
                ensure_upload_dir('assets/uploads/');
                move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $avatar);
                $has_avatar = true;
            }
        }

        if ($has_avatar) {
            $stmt = $this->db->prepare("UPDATE alumnus_bio SET firstname=?, middlename=?, lastname=?, suffixname=?, email=?, gender=?, batch=?, course_id=?, connected_to=?, contact_no=?, company_address=?, company_email=?, birthdate=?, address=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssssssississsssi",
                $firstname, $middlename, $lastname, $suffixname, $email,
                $gender, $batch, $course_id, $connected_to, $contact_no,
                $company_address, $company_email, $birthdate, $address, $avatar, $bio_id
            );
        } else {
            $stmt = $this->db->prepare("UPDATE alumnus_bio SET firstname=?, middlename=?, lastname=?, suffixname=?, email=?, gender=?, batch=?, course_id=?, connected_to=?, contact_no=?, company_address=?, company_email=?, birthdate=?, address=? WHERE id=?");
            $stmt->bind_param("sssssssissssssi",
                $firstname, $middlename, $lastname, $suffixname, $email,
                $gender, $batch, $course_id, $connected_to, $contact_no,
                $company_address, $company_email, $birthdate, $address, $bio_id
            );
        }
        $stmt->execute();
        $stmt->close();

        // Refresh session
        $_SESSION = [];
        $this->login2();
        return 1;
    }

    // ── System Settings ─────────────────────────────────────────────

    function save_settings() {
        $name = sanitize_string($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $contact = sanitize_string($_POST['contact'] ?? '');
        $about = trim($_POST['about'] ?? '');
        $site_url = sanitize_string($_POST['site_url'] ?? '');

        $cover_img = null;
        if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
            $check = validate_image_upload($_FILES['img']);
            if ($check['valid']) {
                $fname = safe_filename('cover', $check['ext']);
                ensure_upload_dir('assets/uploads/');
                move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
                $cover_img = $fname;
            }
        }

        $chk = $this->db->query("SELECT id FROM system_settings");
        if ($chk && $chk->num_rows > 0) {
            if ($cover_img) {
                $stmt = $this->db->prepare("UPDATE system_settings SET name=?, email=?, contact=?, about_content=?, site_url=?, cover_img=?");
                $stmt->bind_param("ssssss", $name, $email, $contact, $about, $site_url, $cover_img);
            } else {
                $stmt = $this->db->prepare("UPDATE system_settings SET name=?, email=?, contact=?, about_content=?, site_url=?");
                $stmt->bind_param("sssss", $name, $email, $contact, $about, $site_url);
            }
        } else {
            $cover_img = $cover_img ?? '';
            $stmt = $this->db->prepare("INSERT INTO system_settings (name, email, contact, about_content, site_url, cover_img) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $contact, $about, $site_url, $cover_img);
        }

        $save = $stmt->execute();
        $stmt->close();

        if ($save) {
            $query = $this->db->query("SELECT * FROM system_settings LIMIT 1");
            if ($query) {
                $row = $query->fetch_assoc();
                foreach ($row as $key => $value) {
                    $_SESSION['system'][$key] = $value;
                }
            }
            return 1;
        }
        return 0;
    }

    // ── Courses ─────────────────────────────────────────────────────

    function save_course() {
        $course = sanitize_string($_POST['course'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);

        if (empty($course)) return 0;

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO courses (course) VALUES (?)");
            $stmt->bind_param("s", $course);
        } else {
            $stmt = $this->db->prepare("UPDATE courses SET course = ? WHERE id = ?");
            $stmt->bind_param("si", $course, $id);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_course() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Alumni Status ───────────────────────────────────────────────

    function update_alumni_acc() {
        $id = sanitize_int($_POST['id'] ?? 0);
        $status = sanitize_int($_POST['status'] ?? 0);

        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("UPDATE alumnus_bio SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Gallery ─────────────────────────────────────────────────────

    function save_gallery() {
        $about = trim($_POST['about'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);
        $folder = "assets/uploads/gallery/";
        ensure_upload_dir($folder);

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO gallery (about) VALUES (?)");
            $stmt->bind_param("s", $about);
            $save = $stmt->execute();
            if ($save) $id = $this->db->insert_id;
            $stmt->close();
        } else {
            $stmt = $this->db->prepare("UPDATE gallery SET about = ? WHERE id = ?");
            $stmt->bind_param("si", $about, $id);
            $save = $stmt->execute();
            $stmt->close();
        }

        if ($save && isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
            $check = validate_image_upload($_FILES['img']);
            if ($check['valid']) {
                // Remove old image
                if (is_dir($folder)) {
                    foreach (scandir($folder) as $f) {
                        if (strpos($f, $id . '_') === 0) @unlink($folder . $f);
                    }
                }
                $fname = $id . '_img.' . $check['ext'];
                move_uploaded_file($_FILES['img']['tmp_name'], $folder . $fname);
            }
        }

        return $save ? 1 : 0;
    }

    function delete_gallery() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $folder = "assets/uploads/gallery/";
        if (is_dir($folder)) {
            foreach (scandir($folder) as $f) {
                if (strpos($f, $id . '_') === 0) @unlink($folder . $f);
            }
        }

        $stmt = $this->db->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Careers ─────────────────────────────────────────────────────

    function save_career() {
        $company = sanitize_string($_POST['company'] ?? '');
        $title = sanitize_string($_POST['title'] ?? '');
        $location = sanitize_string($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);
        $user_id = sanitize_int($_SESSION['login_id'] ?? 0);

        if (empty($company) || empty($title)) return 0;

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO careers (company, job_title, location, description, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $company, $title, $location, $description, $user_id);
        } else {
            $stmt = $this->db->prepare("UPDATE careers SET company = ?, job_title = ?, location = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $company, $title, $location, $description, $id);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_career() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("DELETE FROM careers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Forums ──────────────────────────────────────────────────────

    function save_forum() {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);
        $user_id = sanitize_int($_SESSION['login_id'] ?? 0);

        if (empty($title)) return 0;

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO forum_topics (title, description, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $title, $description, $user_id);
        } else {
            $stmt = $this->db->prepare("UPDATE forum_topics SET title = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $id);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_forum() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("DELETE FROM forum_topics WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function save_comment() {
        $comment = trim($_POST['comment'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);
        $topic_id = sanitize_int($_POST['topic_id'] ?? 0);
        $user_id = sanitize_int($_SESSION['login_id'] ?? 0);

        if (empty($comment)) return 0;

        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO forum_comments (comment, topic_id, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $comment, $topic_id, $user_id);
        } else {
            $stmt = $this->db->prepare("UPDATE forum_comments SET comment = ? WHERE id = ?");
            $stmt->bind_param("si", $comment, $id);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_comment() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("DELETE FROM forum_comments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Events ──────────────────────────────────────────────────────

    function save_event() {
        $title = sanitize_string($_POST['title'] ?? '');
        $schedule = sanitize_string($_POST['schedule'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $id = sanitize_int($_POST['id'] ?? 0);

        if (empty($title)) return 0;

        // Handle banner upload
        $banner = null;
        if (isset($_FILES['banner']) && !empty($_FILES['banner']['tmp_name'])) {
            $check = validate_image_upload($_FILES['banner']);
            if ($check['valid']) {
                $banner = safe_filename('event', $check['ext']);
                ensure_upload_dir('../uploads/');
                move_uploaded_file($_FILES['banner']['tmp_name'], '../uploads/' . $banner);
            }
        }

        if (empty($id)) {
            $approved = (isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1) ? 1 : 0;
            $user_id = sanitize_int($_SESSION['login_id'] ?? 0);

            if ($banner) {
                $stmt = $this->db->prepare("INSERT INTO events (title, schedule, content, banner, approved, user_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssii", $title, $schedule, $content, $banner, $approved, $user_id);
            } else {
                $stmt = $this->db->prepare("INSERT INTO events (title, schedule, content, approved, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $title, $schedule, $content, $approved, $user_id);
            }
        } else {
            if ($banner) {
                $stmt = $this->db->prepare("UPDATE events SET title = ?, schedule = ?, content = ?, banner = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $title, $schedule, $content, $banner, $id);
            } else {
                $stmt = $this->db->prepare("UPDATE events SET title = ?, schedule = ?, content = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $schedule, $content, $id);
            }
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function delete_event() {
        $id = sanitize_int($_POST['id'] ?? 0);
        if ($id <= 0) return 0;

        // Check permissions
        $stmt = $this->db->prepare("SELECT user_id, banner FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res->num_rows === 0) return 0;

        $event = $res->fetch_assoc();
        $owner = (int)($event['user_id'] ?? 0);
        $login_type = (int)($_SESSION['login_type'] ?? 0);
        $login_id = (int)($_SESSION['login_id'] ?? 0);

        if (!in_array($login_type, [1, 4]) && $login_id !== $owner) {
            return 0;
        }

        // Clean up related records
        $stmt = $this->db->prepare("DELETE FROM event_commits WHERE event_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->db->prepare("DELETE FROM event_likes WHERE event_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove banner file
        if (!empty($event['banner'])) {
            $paths = [
                __DIR__ . '/../uploads/' . $event['banner'],
                __DIR__ . '/assets/uploads/' . $event['banner'],
            ];
            foreach ($paths as $p) {
                if (file_exists($p)) {
                    @unlink($p);
                    break;
                }
            }
        }

        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function approve_event() {
        $id = sanitize_int($_POST['id'] ?? 0);
        $approved = sanitize_int($_POST['approved'] ?? 0) ? 1 : 0;

        if ($id <= 0) return 0;

        $stmt = $this->db->prepare("UPDATE events SET approved = ? WHERE id = ?");
        $stmt->bind_param("ii", $approved, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    function participate() {
        $event_id = sanitize_int($_POST['event_id'] ?? 0);
        $user_id = sanitize_int($_SESSION['login_id'] ?? 0);

        if ($event_id <= 0 || $user_id <= 0) return 0;

        $stmt = $this->db->prepare("INSERT INTO event_commits (event_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $event_id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? 1 : 0;
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Verify password with bcrypt, with auto-upgrade from MD5 legacy hashes.
     */
    private function verifyPassword($password, $stored_hash, $user_id) {
        if (password_verify($password, $stored_hash)) {
            return true;
        }

        // Legacy MD5 check with auto-upgrade
        if (md5($password) === $stored_hash) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            $stmt->execute();
            $stmt->close();
            return true;
        }

        return false;
    }
}
