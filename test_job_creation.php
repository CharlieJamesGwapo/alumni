<?php
session_start();
include 'admin/db_connect.php';

// Set admin session for testing
$_SESSION['login_id'] = 1;
$_SESSION['login_name'] = 'Admin';

echo "<h2>Testing Job Creation</h2>";

// Simulate POST data for creating a job
$_POST = [
    'company' => 'Test Company',
    'title' => 'Test Developer Position',
    'location' => 'Remote',
    'description' => 'This is a test job posting to verify the system is working correctly.'
];

// Include the save_career logic
$action = 'save_career';
extract($_POST);

// Sanitize inputs
$company = $conn->real_escape_string(trim($company ?? ''));
$title = $conn->real_escape_string(trim($title ?? ''));
$location = $conn->real_escape_string(trim($location ?? ''));
$description = $conn->real_escape_string(trim($description ?? ''));
$id = isset($id) ? (int)$id : 0;
$user_id = $_SESSION['login_id'] ?? 1; // Default to admin user

// Validate required fields
if(empty($company) || empty($title)){
    echo "<p style='color: red;'>❌ Validation failed - missing required fields</p>";
    exit;
}

// Create careers table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS careers (
    id INT(30) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(250) NOT NULL,
    location TEXT NOT NULL,
    job_title VARCHAR(250) NOT NULL,
    description TEXT NOT NULL,
    user_id INT(30) NOT NULL,
    date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if(empty($id)){
    // Create new job
    $save = $conn->query("INSERT INTO careers SET 
        company = '$company',
        job_title = '$title',
        location = '$location',
        description = '$description',
        user_id = '$user_id',
        date_created = NOW()
    ");
} else {
    // Update existing job
    $save = $conn->query("UPDATE careers SET 
        company = '$company',
        job_title = '$title',
        location = '$location',
        description = '$description'
        WHERE id = '$id'
    ");
}

if($save) {
    echo "<p style='color: green;'>✅ Job created successfully!</p>";
    
    // Get the inserted job ID
    $job_id = $conn->insert_id;
    echo "<p>Job ID: $job_id</p>";
    
    // Verify it was saved
    $check = $conn->query("SELECT * FROM careers WHERE id = $job_id");
    if($check && $check->num_rows > 0) {
        $job = $check->fetch_assoc();
        echo "<h3>Saved Job Details:</h3>";
        echo "<ul>";
        echo "<li><strong>Company:</strong> {$job['company']}</li>";
        echo "<li><strong>Job Title:</strong> {$job['job_title']}</li>";
        echo "<li><strong>Location:</strong> {$job['location']}</li>";
        echo "<li><strong>Description:</strong> {$job['description']}</li>";
        echo "<li><strong>User ID:</strong> {$job['user_id']}</li>";
        echo "<li><strong>Date Created:</strong> {$job['date_created']}</li>";
        echo "</ul>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Failed to create job</p>";
    echo "<p>Error: " . $conn->error . "</p>";
}

// Now test the home.php fetch logic
echo "<h3>Testing Home.php Job Fetching:</h3>";
$stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as posted_by FROM careers c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.id DESC LIMIT 10 OFFSET 0");
$stmt->execute();
$res = $stmt->get_result();

$jobs = [];
while ($r = $res->fetch_assoc()) {
    $jobs[] = $r;
}

if(!empty($jobs)) {
    echo "<p style='color: green;'>✅ Found " . count($jobs) . " jobs</p>";
    foreach($jobs as $job) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h4>{$job['job_title']} at {$job['company']}</h4>";
        echo "<p><strong>Location:</strong> {$job['location']}</p>";
        echo "<p><strong>Posted by:</strong> {$job['posted_by']}</p>";
        echo "<p><strong>Description:</strong> " . substr($job['description'], 0, 100) . "...</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ No jobs found</p>";
}

$conn->close();
?>

<p><a href="debug_jobs.php">View Debug Jobs Page</a></p>
<p><a href="home.php">Go to Home Page</a></p>
