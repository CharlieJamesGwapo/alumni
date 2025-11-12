# Gmail-like Messaging System for MOIST Alumni Management

## Overview
A comprehensive, real-time messaging system that replaces the Guest Speaker Invitation feature with a flexible Gmail-like interface for communication between the registrar and alumni.

## Features Implemented

### 1. **Compose Message Interface** (Registrar)
- **Location**: `alumni.php` - "Compose Message" button
- **Features**:
  - 3-step wizard interface (Select Recipients → Compose → Send)
  - Real-time alumni search with course filtering
  - Multiple recipient selection with checkboxes
  - Email template management system
  - Live message preview with placeholder support
  - Test email functionality
  - Option to send email notifications or system messages only
  - Real-time progress tracking during sending
  - Save custom templates

### 2. **Message Inbox/Outbox** (Registrar)
- **Location**: `view_messages.php`
- **Features**:
  - View all sent messages
  - See recipient count and read statistics
  - Search messages by subject or content
  - View detailed message information
  - See which recipients have read the message
  - Responsive design for mobile devices

### 3. **Alumni Messaging Interface**
- **Location**: `alumni_messages.php`
- **Features**:
  - Inbox for received messages from registrar
  - Compose new messages to registrar
  - Reply to messages
  - Mark messages as read automatically
  - Unread message counter
  - Clean, user-friendly interface

### 4. **Email Templates**
- **Pre-loaded Templates**:
  - Event Invitation
  - General Announcement
  - Survey Request
  - Job Opportunity
- **Custom Templates**: Save frequently used messages as templates
- **Categories**: Organize templates by type (events, announcements, surveys, opportunities, general)

### 5. **Database Structure**
- **Tables Created**:
  - `messages` - Main message storage
  - `message_recipients` - Track multiple recipients and read status
  - `email_templates` - Store reusable templates
  - `message_attachments` - For future file attachment support
  - `conversation_threads` - For threaded conversations

## Installation Instructions

### Step 1: Setup Database
Run the database setup script once:
```
http://your-domain/alumni/registrar/setup_messaging_db.php
```

This will create all necessary tables and insert default templates.

### Step 2: Update Navigation (Optional)
Add a link to the alumni messaging interface in your alumni navigation menu:
```html
<a href="alumni_messages.php">
    <i class="fas fa-envelope"></i> Messages
</a>
```

### Step 3: Test the System
1. **As Registrar**:
   - Go to `alumni.php`
   - Click "Compose Message"
   - Select recipients
   - Compose and send a message
   - Click "View Messages" to see sent messages

2. **As Alumni**:
   - Go to `alumni_messages.php`
   - View received messages
   - Click "New Message" to send to registrar
   - Reply to messages

## File Structure

```
/alumni/
├── registrar/
│   ├── alumni.php                    # Main alumni management (updated)
│   ├── send_message.php              # Message handling backend
│   ├── view_messages.php             # Message inbox for registrar
│   ├── setup_messaging_db.php        # Database setup script
│   └── MESSAGING_SYSTEM_README.md    # This file
└── alumni_messages.php               # Alumni messaging interface
```

## Key Features Explained

### Placeholder System
Messages support dynamic placeholders that are automatically replaced:
- `{{name}}` - Full name (firstname + lastname)
- `{{firstname}}` - First name only
- `{{lastname}}` - Last name only
- `{{email}}` - Email address
- `{{course}}` - Course/program
- `{{batch}}` - Graduation batch year

### Real-time Functionality
- **Search**: Results update as you type (300ms debounce)
- **Progress Tracking**: Live progress bar shows sending status
- **Preview**: Message preview updates in real-time as you type
- **Template Loading**: Instant template application

### Email Notifications
- **Optional**: Choose whether to send email notifications
- **System Messages**: Messages are always saved in the database
- **Email Copy**: If enabled, sends a formatted HTML email via PHPMailer
- **Beautiful Templates**: Professional HTML email templates with MOIST branding

### Responsive Design
- **Mobile-friendly**: Works on all screen sizes
- **Touch-optimized**: Easy to use on tablets and phones
- **Bootstrap 5**: Modern, clean interface
- **Font Awesome Icons**: Clear visual indicators

## API Endpoints

### `send_message.php` Actions:

1. **get_alumni** - Fetch alumni list with search/filter
2. **get_courses** - Get course list for filtering
3. **get_templates** - Load email templates
4. **save_template** - Save new template
5. **send_message** - Send to multiple recipients (batch)
6. **send_single** - Send to single recipient (real-time progress)
7. **get_sent_messages** - Get registrar's sent messages
8. **get_received_messages** - Get alumni's received messages
9. **mark_read** - Mark message as read
10. **get_message_details** - Get full message with recipients
11. **send_reply** - Send reply to a message

## Security Features

- **Session-based Authentication**: Only authenticated users can access
- **User Type Checking**: Registrar (type 4) and Alumni (type 3) have different permissions
- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Protection**: HTML escaping on output
- **CSRF Protection**: Session validation on all actions

## Customization

### Adding New Templates
1. Go to compose message
2. Write your message
3. Click "Save as Template"
4. Enter name and select category

### Modifying Email Design
Edit the `createEmailTemplate()` function in `send_message.php` to customize the HTML email layout.

### Changing Colors
Update the CSS variables in the respective files:
```css
:root {
    --primary: #800000;  /* Maroon */
    --primary-dark: #600000;
}
```

## Troubleshooting

### Messages not sending
1. Check PHPMailer configuration in `send_message.php`
2. Verify SMTP credentials are correct
3. Check error logs in browser console
4. Ensure alumni have valid email addresses

### Templates not loading
1. Run `setup_messaging_db.php` again
2. Check database connection
3. Verify `email_templates` table exists

### Real-time search not working
1. Clear browser cache
2. Check jQuery is loaded
3. Verify `send_message.php` is accessible
4. Check browser console for errors

## Future Enhancements

- [ ] File attachments support
- [ ] Rich text editor (WYSIWYG)
- [ ] Message threading/conversations
- [ ] Email scheduling
- [ ] Message templates with custom fields
- [ ] Bulk actions (delete, archive)
- [ ] Message categories/labels
- [ ] Search and filter improvements
- [ ] Export messages to PDF
- [ ] Push notifications

## Support

For issues or questions:
1. Check the browser console for errors
2. Review the database setup
3. Verify PHPMailer configuration
4. Check file permissions

## Credits

- **Bootstrap 5**: UI Framework
- **Font Awesome**: Icons
- **SweetAlert2**: Beautiful alerts
- **PHPMailer**: Email sending
- **jQuery**: DOM manipulation and AJAX

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Author**: MOIST Alumni Management System
