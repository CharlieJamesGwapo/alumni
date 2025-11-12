<?php
/**
 * Database Setup for Gmail-like Messaging System
 * Run this file once to create all necessary tables
 */

require_once '../admin/db_connect.php';

// Create messages table
$messages_table = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    sender_type ENUM('registrar', 'alumni') NOT NULL,
    recipient_id INT DEFAULT NULL,
    recipient_type ENUM('registrar', 'alumni', 'multiple') DEFAULT NULL,
    subject VARCHAR(500) NOT NULL,
    message_body LONGTEXT NOT NULL,
    template_id INT DEFAULT NULL,
    parent_message_id INT DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    is_starred TINYINT(1) DEFAULT 0,
    is_archived TINYINT(1) DEFAULT 0,
    is_deleted TINYINT(1) DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_sender (sender_id, sender_type),
    INDEX idx_recipient (recipient_id, recipient_type),
    INDEX idx_sent_at (sent_at),
    INDEX idx_parent (parent_message_id),
    FOREIGN KEY (parent_message_id) REFERENCES messages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Create message_recipients table (for multiple recipients)
$recipients_table = "CREATE TABLE IF NOT EXISTS message_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    recipient_id INT NOT NULL,
    recipient_type ENUM('alumni', 'registrar') DEFAULT 'alumni',
    recipient_email VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME NULL,
    rsvp_status ENUM('pending', 'accept', 'decline', 'maybe') DEFAULT 'pending',
    rsvp_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    INDEX idx_message (message_id),
    INDEX idx_recipient (recipient_id, recipient_type),
    INDEX idx_read_status (is_read),
    INDEX idx_rsvp_status (rsvp_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Create email_templates table
$templates_table = "CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(255) NOT NULL UNIQUE,
    template_subject VARCHAR(500) NOT NULL,
    template_body LONGTEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Create message_attachments table (for future use)
$attachments_table = "CREATE TABLE IF NOT EXISTS message_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message (message_id),
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Create conversation_threads table
$threads_table = "CREATE TABLE IF NOT EXISTS conversation_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_subject VARCHAR(500) NOT NULL,
    started_by_id INT NOT NULL,
    started_by_type ENUM('registrar', 'alumni') NOT NULL,
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_started_by (started_by_id, started_by_type),
    INDEX idx_last_message (last_message_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Create message_replies table for email replies
$replies_table = "CREATE TABLE IF NOT EXISTS message_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    recipient_id INT NOT NULL,
    reply_content TEXT NOT NULL,
    replied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES alumnus_bio(id) ON DELETE CASCADE,
    INDEX idx_message_id (message_id),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_replied_at (replied_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    // Execute table creation
    $conn->query($messages_table);
    echo "✓ Messages table created successfully<br>";
    
    $conn->query($recipients_table);
    echo "✓ Message recipients table created successfully<br>";
    
    $conn->query($templates_table);
    echo "✓ Email templates table created successfully<br>";
    
    $conn->query($attachments_table);
    echo "✓ Message attachments table created successfully<br>";
    
    $conn->query($threads_table);
    echo "✓ Conversation threads table created successfully<br>";
    
    $conn->query($replies_table);
    echo "✓ Message replies table created successfully<br>";
    
    // Insert default email templates
    $templates = [
        [
            'name' => 'General Announcement',
            'subject' => 'Important Announcement from MOIST',
            'body' => 'Dear {{name}},

Greetings from the Misamis Oriental Institute of Science and Technology (MOIST)!

We are pleased to share some important updates and upcoming activities from the MOIST Administration that all students, faculty, staff, and alumni should take note of.

🗓️ Announcement Details:
Date Issued: {{event_date}}
Subject: [Insert Announcement Title or Summary Here]
Details: [Insert short description — e.g., new policies, upcoming events, class schedules, or maintenance notices]

✨ Reminders and Highlights:
• [First key point or reminder]
• [Second key point or update]
• [Optional: Third key point or additional note]

We encourage everyone to stay informed and actively participate in all upcoming institutional activities. Your continued engagement contributes greatly to the success and unity of our MOIST community.

For further information or clarification, please contact the Office of Student Affairs or visit our official website.

Thank you for your attention and cooperation.

Warm regards,
MOIST Administration
Misamis Oriental Institute of Science and Technology (MOIST)',
            'category' => 'announcements'
        ],
        [
            'name' => 'Event Invitation',
            'subject' => 'You\'re Invited: MOIST Event',
            'body' => 'Dear {{name}},

Greetings from the Misamis Oriental Institute of Science and Technology (MOIST)!

We are delighted to announce an upcoming school event that aims to bring our community together in celebration, learning, and fellowship.

🗓️ Event Details:
Event: [Insert Event Title]
Date: {{event_date}}
Time: {{event_time}}
Venue: [Insert Venue]
Theme: [Insert Theme, if any]

✨ Highlights of the Event:
• [Highlight 1 — e.g., Inspiring talks and presentations]
• [Highlight 2 — e.g., Performances and student showcases]
• [Highlight 3 — e.g., Awards, games, or exhibits]

This event is open to all students, faculty, staff, and alumni. We encourage everyone to take part and make this occasion a memorable experience for the entire MOIST family.

To confirm your attendance or view more details, please visit our official school portal.

For any questions or special arrangements, you may reach out to the Office of Student Affairs.

We look forward to your participation and support in making this event a success!

Warm regards,
MOIST Administration
Office of Student Affairs
Misamis Oriental Institute of Science and Technology (MOIST)',
            'category' => 'events'
        ],
        [
            'name' => 'Alumni Homecoming Invitation',
            'subject' => 'MOIST Alumni Homecoming {{event_date}}',
            'body' => 'Dear {{name}},

Greetings from MOIST Alumni Office!

We are thrilled to invite you to our Annual Alumni Homecoming celebration!

🎉 Event Details:
Date: {{event_date}}
Time: {{event_time}}
Venue: MOIST Campus Grounds
Batch: {{batch}} and All Alumni Welcome!

📋 Program Highlights:
• Registration and Fellowship
• Campus Tour and Reminiscing
• Alumni Recognition Awards
• Entertainment and Games
• Dinner and Socials

As a valued member of the {{course}} program, your presence would make this celebration even more special.

Please confirm your attendance by replying to this email or contacting us at the Alumni Office.

We look forward to reconnecting with you!

Best regards,
MOIST Alumni Office
Misamis Oriental Institute of Science and Technology',
            'category' => 'events'
        ],
        [
            'name' => 'Survey Request',
            'subject' => 'MOIST Alumni Tracer Study - We Need Your Input',
            'body' => 'Dear {{firstname}} {{lastname}},

Greetings from MOIST!

As part of our continuous improvement and accreditation requirements, we are conducting an Alumni Tracer Study to gather valuable feedback from our graduates.

📊 Survey Details:
Purpose: Track alumni career progress and gather feedback
Duration: 10-15 minutes
Deadline: [Insert Deadline]
Course: {{course}}
Batch: {{batch}}

Your responses will help us:
✓ Improve our curriculum and programs
✓ Better prepare future students
✓ Maintain our institutional accreditation
✓ Strengthen alumni relations

Please take a few minutes to complete the survey at: [Insert Survey Link]

All responses are confidential and will be used solely for institutional improvement purposes.

Thank you for your continued support and cooperation!

Warm regards,
MOIST Research and Planning Office
Misamis Oriental Institute of Science and Technology',
            'category' => 'surveys'
        ],
        [
            'name' => 'Job Opportunity Announcement',
            'subject' => 'Job Opportunity for MOIST Alumni',
            'body' => 'Dear {{name}},

Greetings from MOIST Alumni Office!

We are pleased to share an exciting job opportunity that may be of interest to you and fellow alumni.

💼 Position Details:
Job Title: [Insert Position]
Company: [Insert Company Name]
Location: [Insert Location]
Employment Type: [Full-time/Part-time/Contract]
Qualifications: [Insert Key Requirements]

📝 Job Description:
[Insert brief description of responsibilities and requirements]

Preferred Course: {{course}} or related fields
Experience Level: [Entry/Mid/Senior Level]

How to Apply:
Interested applicants may send their resume and application letter to: [Insert Email/Link]
Deadline: [Insert Deadline]

For more information, please contact:
[Insert Contact Person and Details]

We encourage qualified alumni to apply and share this opportunity with others who might be interested.

Best of luck!

MOIST Alumni Office
Career Services and Placement Office
Misamis Oriental Institute of Science and Technology',
            'category' => 'opportunities'
        ],
        [
            'name' => 'Seminar/Workshop Invitation',
            'subject' => 'Invitation: Professional Development Seminar',
            'body' => 'Dear {{name}},

Greetings from MOIST!

We are excited to invite you to an upcoming professional development seminar designed specifically for our alumni.

📚 Seminar Details:
Title: [Insert Seminar Title]
Date: {{event_date}}
Time: {{event_time}}
Venue: [Insert Venue/Online Platform]
Speaker: [Insert Speaker Name and Credentials]

🎯 Topics to be Covered:
• [Topic 1]
• [Topic 2]
• [Topic 3]

This seminar is FREE for all MOIST alumni and will provide:
✓ Certificate of Participation
✓ Networking opportunities
✓ Continuing Professional Development (CPD) units (if applicable)
✓ Learning materials

Registration:
Please confirm your attendance by [Insert Deadline] through: [Insert Registration Link/Email]
Limited slots available!

For inquiries, contact the Alumni Office at [Insert Contact Details].

We look forward to your participation!

Best regards,
MOIST Alumni Office
Professional Development Committee
Misamis Oriental Institute of Science and Technology',
            'category' => 'events'
        ],
        [
            'name' => 'Donation/Fundraising Appeal',
            'subject' => 'Support MOIST: Alumni Giving Campaign',
            'body' => 'Dear {{name}},

Warm greetings from your Alma Mater, MOIST!

As a proud graduate of {{course}} (Batch {{batch}}), you are part of the MOIST legacy that continues to shape future generations.

🎓 Alumni Giving Campaign
We are reaching out to invite you to support our institution through the MOIST Alumni Giving Campaign.

💡 Your donation will help:
• Provide scholarships for deserving students
• Upgrade facilities and laboratories
• Support faculty development programs
• Enhance student services and activities

Every contribution, big or small, makes a difference in the lives of our students.

💳 Ways to Donate:
Bank Transfer: [Insert Bank Details]
GCash/PayMaya: [Insert Number]
Direct Donation: Visit the Alumni Office

For donations of ₱1,000 and above, you will receive:
✓ Official receipt for tax purposes
✓ Recognition in our Donor Wall
✓ Alumni newsletter subscription

Your generosity today will create opportunities for tomorrow\'s leaders.

For more information, please contact the Alumni Office at [Insert Contact Details].

Thank you for your continued support!

With gratitude,
MOIST Alumni Office
Development and Fundraising Committee
Misamis Oriental Institute of Science and Technology',
            'category' => 'general'
        ],
        [
            'name' => 'Account Validation Reminder',
            'subject' => 'Action Required: Validate Your MOIST Alumni Account',
            'body' => 'Dear {{name}},

Greetings from MOIST Alumni Office!

We noticed that your alumni account requires validation to ensure you receive all updates and benefits.

👤 Your Account Information:
Name: {{firstname}} {{lastname}}
Email: {{email}}
Course: {{course}}
Batch: {{batch}}

✅ Please validate your account to:
• Receive important announcements and invitations
• Access alumni portal and services
• Participate in alumni events and activities
• Connect with fellow alumni
• Receive job opportunities and career updates

🔗 How to Validate:
1. Click the validation link: [Insert Link]
2. Update your profile information
3. Verify your email address

This will only take 2-3 minutes of your time.

If you have already validated your account, please disregard this message.

For assistance, contact the Alumni Office at [Insert Contact Details].

Thank you for staying connected with MOIST!

Best regards,
MOIST Alumni Office
Database Management Team
Misamis Oriental Institute of Science and Technology',
            'category' => 'general'
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO email_templates (template_name, template_subject, template_body, category, created_by) 
                           VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE template_subject = VALUES(template_subject)");
    
    foreach ($templates as $template) {
        $stmt->bind_param("ssss", $template['name'], $template['subject'], $template['body'], $template['category']);
        $stmt->execute();
    }
    
    echo "✓ Default email templates inserted successfully<br>";
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "<a href='alumni.php'>Go to Alumni Management</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
