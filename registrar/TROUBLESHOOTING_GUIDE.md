# Troubleshooting Guide - Messaging System

## Quick Fixes for Common Issues

### 1. "Failed to connect to server" Error

**Symptoms**: Error message when trying to load alumni, templates, or send messages.

**Solutions**:

#### A. Run Connection Test
1. Open your browser
2. Navigate to: `http://localhost/alumni/registrar/test_connection.html`
3. Review the test results
4. Follow the recommendations provided

#### B. Check Database Setup
1. Navigate to: `http://localhost/alumni/registrar/setup_messaging_db.php`
2. Wait for "Database setup completed successfully!" message
3. If you see errors, check your database credentials

#### C. Verify File Paths
Make sure these files exist:
- `c:\xampp\htdocs\alumni\registrar\send_message.php`
- `c:\xampp\htdocs\alumni\admin\db_connect.php`
- `c:\xampp\htdocs\alumni\PHPMailer\src\PHPMailer.php`

#### D. Check XAMPP Services
1. Open XAMPP Control Panel
2. Make sure **Apache** and **MySQL** are running (green)
3. If not, click "Start" for each service

#### E. Browser Console Check
1. Press F12 in your browser
2. Go to "Console" tab
3. Look for red error messages
4. Check the error details for file paths or connection issues

---

### 2. Cannot Type in "Template Name" Field

**Fixed!** The template save dialog now has:
- Properly styled input fields
- Auto-focus on the template name field
- Better validation
- Clear labels

**How to use**:
1. Click "Save as Template"
2. The input field should automatically be focused
3. Type your template name
4. Select a category
5. Click "Save Template"

---

### 3. Date and Time Fields Not Working

**Fixed!** New features added:
- Event Date picker (HTML5 date input)
- Start Time picker
- End Time picker
- Automatic formatting (12-hour format with AM/PM)
- Real-time preview updates

**Available Placeholders**:
- `{{event_date}}` - Formatted date (e.g., "November 7, 2025")
- `{{event_time}}` - Time range (e.g., "2:00 PM - 4:00 PM")
- `{{event_end_time}}` - End time only

**Example Usage**:
```
Dear {{name}},

You are invited to our event on {{event_date}} at {{event_time}}.

Location: MOIST Auditorium
```

---

### 4. Email Not Sending

**Check These**:

#### A. PHPMailer Configuration
Edit `send_message.php` and verify:
```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your-email@gmail.com';  // Your Gmail
$mail->Password = 'your-app-password';      // Gmail App Password
$mail->Port = 587;
```

#### B. Gmail App Password
1. Go to Google Account Settings
2. Security → 2-Step Verification → App Passwords
3. Generate new app password
4. Use this password in `send_message.php`

#### C. Test Email First
1. In Compose Message modal
2. Go to Step 2
3. Enter a test email address
4. Click "Send Test"
5. Check if test email arrives

---

### 5. Alumni Not Loading

**Solutions**:

#### A. Check Database
```sql
-- Run this in phpMyAdmin
SELECT COUNT(*) FROM alumnus_bio WHERE status = 1 AND email IS NOT NULL AND email != '';
```
If count is 0, you need to add alumni with valid emails.

#### B. Check Search Function
1. Open Compose Message
2. Click "Load All Alumni" button
3. Check browser console (F12) for errors

#### C. Verify Database Connection
1. Open `admin/db_connect.php`
2. Check credentials:
```php
$host = 'localhost';
$username = 'root';
$password = '';  // Usually empty for XAMPP
$database = 'alumni_db';  // Your database name
```

---

### 6. Templates Not Saving

**Check**:
1. Database table exists: Run `setup_messaging_db.php`
2. User is logged in as Registrar (type = 4)
3. Subject and message are filled before saving
4. Check browser console for errors

**Manual Check**:
```sql
-- In phpMyAdmin
SELECT * FROM email_templates;
```

---

### 7. Real-time Preview Not Updating

**Solutions**:
1. Clear browser cache (Ctrl + Shift + Delete)
2. Hard refresh page (Ctrl + F5)
3. Check if jQuery is loaded (F12 → Console → type `jQuery` and press Enter)

---

### 8. Progress Bar Stuck

**If sending gets stuck**:
1. Check browser console for errors
2. Verify internet connection
3. Check SMTP settings
4. Try sending to fewer recipients first

---

## Step-by-Step Testing

### Test 1: Basic Connection
```
1. Open: http://localhost/alumni/registrar/test_connection.html
2. All tests should be green
3. If any red, follow recommendations
```

### Test 2: Database Setup
```
1. Open: http://localhost/alumni/registrar/setup_messaging_db.php
2. Should see: "✓ Messages table created successfully"
3. Should see: "✓ Default email templates inserted successfully"
```

### Test 3: Load Alumni
```
1. Open: http://localhost/alumni/registrar/alumni.php
2. Click "Compose Message"
3. Click "Load All Alumni"
4. Should see list of alumni with checkboxes
```

### Test 4: Template System
```
1. In Compose Message modal
2. Go to Step 2
3. Click template dropdown
4. Should see categories: EVENTS, ANNOUNCEMENTS, etc.
5. Select a template
6. Subject and message should auto-fill
```

### Test 5: Send Test Email
```
1. Fill in subject and message
2. Enter your email in "Send Test Email" field
3. Click "Send Test"
4. Check your email inbox (and spam folder)
```

---

## Error Messages Explained

### "No action specified"
- **Cause**: POST data not reaching server
- **Fix**: Check if `send_message.php` exists and is accessible

### "Unauthorized"
- **Cause**: Not logged in or wrong user type
- **Fix**: Log in as Registrar (user_type = 4)

### "Invalid email address"
- **Cause**: Alumni email is missing or invalid
- **Fix**: Update alumni records with valid emails

### "Failed to send invitation"
- **Cause**: SMTP error or network issue
- **Fix**: Check PHPMailer settings and internet connection

### "Template not found"
- **Cause**: Template ID doesn't exist
- **Fix**: Run `setup_messaging_db.php` to create default templates

---

## Quick Diagnostics Commands

### Check Apache/MySQL Status
```
Open XAMPP Control Panel
Apache should be green
MySQL should be green
```

### Check Database Tables
```sql
-- In phpMyAdmin
SHOW TABLES LIKE 'messages';
SHOW TABLES LIKE 'email_templates';
SHOW TABLES LIKE 'message_recipients';
```

### Check Alumni Count
```sql
SELECT COUNT(*) as total,
       SUM(CASE WHEN email IS NOT NULL AND email != '' THEN 1 ELSE 0 END) as with_email
FROM alumnus_bio
WHERE status = 1;
```

### Check Templates
```sql
SELECT id, template_name, category, is_active
FROM email_templates
WHERE is_active = 1;
```

---

## Browser Console Commands

Open browser console (F12) and try:

```javascript
// Test jQuery
jQuery.fn.jquery

// Test AJAX connection
$.post('send_message.php', {action: 'get_courses'}, function(data) {
    console.log(data);
}, 'json');

// Check if modal exists
$('#composeMessageModal').length

// Check selected alumni
console.log(messageData.selectedAlumni);
```

---

## File Permissions (Windows)

Make sure these folders are writable:
- `c:\xampp\htdocs\alumni\registrar\`
- `c:\xampp\htdocs\alumni\admin\`

Right-click folder → Properties → Security → Edit → Allow "Full Control"

---

## Still Having Issues?

### Collect This Information:
1. Error message (exact text)
2. Browser console errors (F12 → Console)
3. PHP error log (`c:\xampp\apache\logs\error.log`)
4. Test connection results
5. Which step fails (1, 2, or 3)

### Check These Logs:
- **Apache Error Log**: `c:\xampp\apache\logs\error.log`
- **PHP Error Log**: Check `php.ini` for `error_log` location
- **Browser Console**: F12 → Console tab

### Common Log Locations:
```
c:\xampp\apache\logs\error.log
c:\xampp\mysql\data\*.err
c:\xampp\php\logs\php_error_log
```

---

## Contact Support Checklist

Before asking for help, please:
- [ ] Run connection test
- [ ] Check browser console for errors
- [ ] Verify XAMPP services are running
- [ ] Run database setup script
- [ ] Try in different browser
- [ ] Clear browser cache
- [ ] Check PHP error logs

---

## Quick Reference

### URLs
- **Test Connection**: `http://localhost/alumni/registrar/test_connection.html`
- **Setup Database**: `http://localhost/alumni/registrar/setup_messaging_db.php`
- **Compose Message**: `http://localhost/alumni/registrar/alumni.php`
- **View Messages**: `http://localhost/alumni/registrar/view_messages.php`
- **Alumni Messages**: `http://localhost/alumni/alumni_messages.php`

### Default Credentials
- **Database Host**: localhost
- **Database User**: root
- **Database Password**: (empty)
- **SMTP Host**: smtp.gmail.com
- **SMTP Port**: 587

---

**Last Updated**: 2024  
**Version**: 1.0.0
