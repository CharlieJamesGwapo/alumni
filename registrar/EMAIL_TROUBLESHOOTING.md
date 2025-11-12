# 📧 Email Sending Troubleshooting Guide

## 🔍 Quick Diagnosis

If you're seeing **"Successfully sent: 0, Failed: 1"**, follow these steps:

### Step 1: Run the Email Test Script

1. Open your browser and navigate to:
   ```
   http://localhost/alumni/registrar/test_email_config.php
   ```

2. Enter your email address and click "Run Email Test"

3. Check the results for each test:
   - ✅ PHPMailer Class
   - ✅ SMTP Connection
   - ✅ Send Test Email
   - ✅ Database Connection
   - ✅ Speaker RSVP Table

### Step 2: Check Browser Console

1. Open Developer Tools (F12)
2. Go to Console tab
3. Look for error messages starting with ❌
4. Common errors and solutions below

## 🚨 Common Issues & Solutions

### Issue 1: Gmail Authentication Failed

**Error:** `SMTP Error: Could not authenticate`

**Solutions:**

#### Option A: Use App Password (Recommended)
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable 2-Step Verification
3. Go to "App passwords"
4. Generate a new app password for "Mail"
5. Replace the password in `send_invite_speaker.php`:
   ```php
   $mail->Password = 'your-16-digit-app-password';
   ```

#### Option B: Enable Less Secure Apps (Not Recommended)
1. Go to [Less Secure Apps](https://myaccount.google.com/lesssecureapps)
2. Turn ON "Allow less secure apps"
3. Note: This option may not be available if 2FA is enabled

### Issue 2: SMTP Connection Timeout

**Error:** `SMTP connect() failed`

**Solutions:**

1. **Check Firewall:**
   - Ensure port 587 is not blocked
   - Try port 465 with SSL instead:
     ```php
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
     $mail->Port = 465;
     ```

2. **Check Internet Connection:**
   - Verify your server can reach smtp.gmail.com
   - Test with: `ping smtp.gmail.com`

3. **Increase Timeout:**
   ```php
   $mail->Timeout = 60; // Increase to 60 seconds
   ```

### Issue 3: Invalid Email Address

**Error:** `Invalid email address: empty`

**Solution:**
- Ensure the alumni record has a valid email address
- Check database: `SELECT id, firstname, lastname, email FROM alumnus_bio WHERE id = [alumni_id]`
- Update email if missing or invalid

### Issue 4: Missing PHPMailer Files

**Error:** `Class 'PHPMailer\PHPMailer\PHPMailer' not found`

**Solution:**
1. Verify PHPMailer files exist:
   - `/PHPMailer/src/PHPMailer.php`
   - `/PHPMailer/src/SMTP.php`
   - `/PHPMailer/src/Exception.php`

2. If missing, download from: https://github.com/PHPMailer/PHPMailer

### Issue 5: SSL Certificate Verification Failed

**Error:** `SSL operation failed with code 1`

**Solutions:**

1. **Disable SSL Verification (Development Only):**
   ```php
   $mail->SMTPOptions = array(
       'ssl' => array(
           'verify_peer' => false,
           'verify_peer_name' => false,
           'allow_self_signed' => true
       )
   );
   ```

2. **Update CA Certificates:**
   - Download: https://curl.se/ca/cacert.pem
   - Update php.ini: `openssl.cafile = "path/to/cacert.pem"`

### Issue 6: Email Content Too Large

**Error:** `Message body too large`

**Solution:**
- Reduce email content size
- Remove large images or attachments
- Use external image hosting

## 🔧 Configuration Check

### Current SMTP Settings

Location: `send_invite_speaker.php` (line 220-230)

```php
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'capstonee2@gmail.com';
$mail->Password = 'ewushfarghitfdkt';  // ⚠️ Update this if using App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Timeout = 30;
```

### Alternative SMTP Providers

If Gmail doesn't work, try these alternatives:

#### Outlook/Hotmail
```php
$mail->Host = 'smtp-mail.outlook.com';
$mail->Port = 587;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

#### Yahoo Mail
```php
$mail->Host = 'smtp.mail.yahoo.com';
$mail->Port = 587;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

#### SendGrid
```php
$mail->Host = 'smtp.sendgrid.net';
$mail->Port = 587;
$mail->Username = 'apikey';
$mail->Password = 'your-sendgrid-api-key';
```

## 📊 Enable Debug Mode

To see detailed SMTP communication:

1. Edit `send_invite_speaker.php` (line 217-218)
2. Uncomment these lines:
   ```php
   $mail->SMTPDebug = 2; // Enable verbose debug output
   $mail->Debugoutput = 'error_log';
   ```

3. Check PHP error log for detailed output

## 🧪 Test Email Manually

Use this PHP script to test email sending independently:

```php
<?php
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'capstonee2@gmail.com';
    $mail->Password = 'ewushfarghitfdkt';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('capstonee2@gmail.com', 'Test');
    $mail->addAddress('your-email@example.com');
    
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email';
    
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
```

## 📝 Check Error Logs

### PHP Error Log
- **Windows XAMPP:** `C:\xampp\php\logs\php_error_log`
- **Linux:** `/var/log/apache2/error.log`

### Apache Error Log
- **Windows XAMPP:** `C:\xampp\apache\logs\error.log`
- **Linux:** `/var/log/apache2/error.log`

### Browser Console
1. Press F12
2. Go to Console tab
3. Look for errors in red

## 🔐 Security Best Practices

1. **Never commit passwords to Git:**
   - Use environment variables
   - Create a `.env` file (add to .gitignore)

2. **Use App Passwords:**
   - More secure than account password
   - Can be revoked individually

3. **Rotate Credentials:**
   - Change passwords regularly
   - Update after team member changes

## 📞 Still Not Working?

If none of the above solutions work:

1. **Check PHP Extensions:**
   ```php
   <?php phpinfo(); ?>
   ```
   Ensure these are enabled:
   - openssl
   - sockets
   - mbstring

2. **Test with Different Email:**
   - Try sending to a different email provider
   - Gmail might have rate limits

3. **Check Server Restrictions:**
   - Some hosting providers block outgoing SMTP
   - Contact your hosting provider

4. **Use Alternative Method:**
   - Consider using PHP's `mail()` function
   - Use a third-party email service (SendGrid, Mailgun, etc.)

## ✅ Success Indicators

When everything is working correctly, you should see:

1. **Console Output:**
   ```
   ✅ Sent to: john@example.com
   ✅ Sent to: jane@example.com
   ```

2. **Success Dialog:**
   ```
   ✅ Successfully sent: 2
   ❌ Failed: 0
   📊 Total: 2
   ```

3. **Email Received:**
   - Check inbox (and spam folder)
   - Email should have proper formatting
   - RSVP buttons should work

## 🎯 Quick Fix Checklist

- [ ] Gmail App Password configured
- [ ] Port 587 not blocked by firewall
- [ ] PHPMailer files exist
- [ ] OpenSSL extension enabled
- [ ] Valid email address in database
- [ ] Internet connection working
- [ ] SMTP credentials correct
- [ ] Debug mode enabled (temporarily)
- [ ] Error logs checked
- [ ] Test email script run successfully

---

**Last Updated:** October 24, 2025
**Version:** 1.0
