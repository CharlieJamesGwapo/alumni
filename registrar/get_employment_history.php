<?php
session_start();
include '../admin/db_connect.php';

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Get alumni ID from request
$alumni_id = isset($_GET['alumni_id']) ? intval($_GET['alumni_id']) : 0;

if ($alumni_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid alumni ID']);
    exit();
}

try {
    // Fetch employment history from alumnus_bio table
    $stmt = $conn->prepare("
        SELECT 
            ab.id,
            ab.employment_status,
            ab.employment_history,
            ab.connected_to as type_of_industry,
            ab.contact_no as company_contact_no,
            ab.company_address,
            ab.company_email,
            ab.current_company as company_name,
            ab.current_position
        FROM alumnus_bio ab
        WHERE ab.id = ?
    ");
    
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $employment_data = [];
    
    if ($row = $result->fetch_assoc()) {
        // Parse JSON employment_history field
        $employment_history_json = $row['employment_history'];
        
        if (!empty($employment_history_json)) {
            $employment_entries = json_decode($employment_history_json, true);
            
            if (is_array($employment_entries)) {
                foreach ($employment_entries as $entry) {
                    // Create a structured employment record
                    $employment_record = [
                        'id' => $row['id'],
                        'employment_status' => $row['employment_status'] ?: 'not_specified',
                        'company_name' => $row['company_name'] ?: 'Company Not Specified',
                        'current_position' => $row['current_position'] ?: '',
                        'type_of_industry' => $row['type_of_industry'] ?: '',
                        'company_contact_no' => $row['company_contact_no'] ?: '',
                        'company_address' => $row['company_address'] ?: '',
                        'company_email' => $row['company_email'] ?: '',
                        'date_started' => $entry['date_started'] ?? '',
                        'duration' => $entry['duration'] ?? '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $employment_data[] = $employment_record;
                }
            }
        }
        
        // If no employment history in JSON, but has current company info, create a record
        if (empty($employment_data) && (!empty($row['company_name']) || !empty($row['employment_status']))) {
            $employment_record = [
                'id' => $row['id'],
                'employment_status' => $row['employment_status'] ?: 'not_specified',
                'company_name' => $row['company_name'] ?: 'Company Not Specified',
                'current_position' => $row['current_position'] ?: '',
                'type_of_industry' => $row['type_of_industry'] ?: '',
                'company_contact_no' => $row['company_contact_no'] ?: '',
                'company_address' => $row['company_address'] ?: '',
                'company_email' => $row['company_email'] ?: '',
                'date_started' => '',
                'duration' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $employment_data[] = $employment_record;
        }
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $employment_data,
        'count' => count($employment_data)
    ]);

} catch (Exception $e) {
    error_log("Employment history fetch error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch employment history: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
