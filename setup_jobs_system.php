<?php
// Database setup and test script
include 'admin/db_connect.php';

echo "<h1>Jobs System Setup & Test</h1>";

try {
    // Test database connection
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit();
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
    echo "<p style='color: green;'>✅ Careers table ready</p>";

    // Create users table if it doesn't exist
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT(30) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(250) NOT NULL,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        type TINYINT(1) NOT NULL DEFAULT 2
    )");
    echo "<p style='color: green;'>✅ Users table ready</p>";

    // Check if we have any users in the users table
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $userCount = $result->fetch_assoc()['count'];
    echo "<p>👥 Users in system: $userCount</p>";

    // Check if we have any jobs
    $result = $conn->query("SELECT COUNT(*) as count FROM careers");
    $jobCount = $result->fetch_assoc()['count'];
    echo "<p>💼 Jobs in system: $jobCount</p>";

    // Insert a test user if none exists
    if ($userCount == 0) {
        $conn->query("INSERT INTO users (name, username, password, type) VALUES ('Admin', 'admin', 'admin123', 1)");
        echo "<p style='color: green;'>✅ Test admin user created</p>";
    }

    // Insert some sample jobs if none exist
    if ($jobCount == 0) {
        $sampleJobs = [
            [
                'company' => 'Tech Solutions Inc.',
                'location' => 'Remote / New York',
                'job_title' => 'Senior Software Developer',
                'description' => 'We are looking for an experienced software developer to join our team. You will be responsible for developing and maintaining web applications using modern technologies.',
                'user_id' => 1
            ],
            [
                'company' => 'Digital Marketing Agency',
                'location' => 'San Francisco, CA',
                'job_title' => 'Marketing Specialist',
                'description' => 'Join our dynamic marketing team! We need a creative marketing specialist to help develop and execute marketing campaigns for our clients.',
                'user_id' => 1
            ],
            [
                'company' => 'Financial Services Corp',
                'location' => 'Chicago, IL',
                'job_title' => 'Financial Analyst',
                'description' => 'We are seeking a detail-oriented financial analyst to join our finance team. You will analyze financial data and prepare reports for management.',
                'user_id' => 1
            ]
        ];

        foreach ($sampleJobs as $job) {
            $stmt = $conn->prepare("INSERT INTO careers (company, location, job_title, description, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $job['company'], $job['location'], $job['job_title'], $job['description'], $job['user_id']);
            $stmt->execute();
        }
        echo "<p style='color: green;'>✅ Sample jobs added</p>";
    }

    // Test the exact query used in fetch_jobs
    $stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as posted_by FROM careers c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.id DESC LIMIT 10 OFFSET 0");
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<p style='color: green;'>✅ Jobs query successful! Found {$res->num_rows} jobs</p>";

        echo "<h3>Sample Jobs:</h3>";
        echo "<div style='border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        while ($row = $res->fetch_assoc()) {
            echo "<div style='margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;'>";
            echo "<h4 style='color: #800000; margin: 0 0 5px 0;'>{$row['job_title']}</h4>";
            echo "<p style='margin: 0 0 5px 0; font-weight: bold;'>{$row['company']}</p>";
            echo "<p style='margin: 0 0 5px 0; color: #666;'>📍 {$row['location']}</p>";
            echo "<p style='margin: 0 0 5px 0;'>👤 Posted by: {$row['posted_by']}</p>";
            echo "<p style='margin: 0; color: #888; font-size: 0.9em;'>" . substr($row['description'], 0, 100) . "...</p>";
            echo "</div>";
        }
        echo "</div>";

    } else {
        echo "<p style='color: red;'>❌ No jobs found</p>";
    }

    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p>The jobs system should now be working. Try refreshing the Jobs tab in your application.</p>";

    // Test the fetch_jobs AJAX endpoint
    echo "<h3>Testing AJAX Endpoint:</h3>";
    echo "<p>Click the button below to test the fetch_jobs endpoint:</p>";
    echo "<button onclick='testFetchJobs()' style='background: #800000; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 0;'>Test Jobs Endpoint</button>";
    echo "<div id='testResult' style='margin-top: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; min-height: 50px; background: #f9f9f9;'></div>";

    echo "<script>
    function testFetchJobs() {
        document.getElementById('testResult').innerHTML = '<p style=\"color: #666;\">Testing...</p>';

        fetch(window.location.origin + '/alumni/home.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'ajax_action=fetch_jobs&page=1'
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(text => {
            console.log('Raw response length:', text.length);
            try {
                const data = JSON.parse(text);
                if (data.ok) {
                    const jobCount = data.data.html.split('job-card').length - 1; // -1 to exclude split artifacts
                    document.getElementById('testResult').innerHTML = '<p style=\"color: green;\">✅ Success! Found ' + jobCount + ' jobs</p><p>Response: ' + JSON.stringify(data, null, 2).substring(0, 300) + '...</p>';
                } else {
                    document.getElementById('testResult').innerHTML = '<p style=\"color: red;\">❌ Error: ' + data.msg + '</p>';
                }
            } catch (e) {
                document.getElementById('testResult').innerHTML = '<p style=\"color: red;\">❌ JSON Parse Error: ' + e.message + '</p><p>Raw response (first 500 chars): ' + text.substring(0, 500) + '...</p>';
            }
        })
        .catch(error => {
            document.getElementById('testResult').innerHTML = '<p style=\"color: red;\">❌ Network Error: ' + error.message + '</p>';
        });
    }
    </script>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>Stack trace: " . $e->getTraceAsString() . "</p>";
}

$conn->close();
?>
