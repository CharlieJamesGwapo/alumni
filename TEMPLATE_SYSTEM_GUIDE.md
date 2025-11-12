# 📧 Template System Guide - MOIST Alumni Messaging

## ✅ What's New

### Template Selection is Now REQUIRED
- You **must** select a template before sending messages
- Choose from 8 professional MOIST templates
- OR select "Custom Message" to write your own

### 8 Professional Templates Included

#### 1. **General Announcement** 📢
Perfect for: Important updates, policy changes, schedules
```
Subject: Important Announcement from MOIST
Includes: Date issued, announcement details, reminders
```

#### 2. **Event Invitation** 🎉
Perfect for: School events, celebrations, gatherings
```
Subject: You're Invited: MOIST Event
Includes: Event details, highlights, RSVP info
Uses: {{event_date}}, {{event_time}} placeholders
```

#### 3. **Alumni Homecoming Invitation** 🏠
Perfect for: Annual homecoming, reunions
```
Subject: MOIST Alumni Homecoming
Includes: Program highlights, batch info, venue details
```

#### 4. **Survey Request** 📊
Perfect for: Tracer studies, feedback collection
```
Subject: MOIST Alumni Tracer Study
Includes: Survey purpose, duration, benefits
```

#### 5. **Job Opportunity Announcement** 💼
Perfect for: Sharing job openings with alumni
```
Subject: Job Opportunity for MOIST Alumni
Includes: Position details, qualifications, how to apply
```

#### 6. **Seminar/Workshop Invitation** 📚
Perfect for: Professional development events
```
Subject: Invitation: Professional Development Seminar
Includes: Topics, speaker, registration details
```

#### 7. **Donation/Fundraising Appeal** 🎓
Perfect for: Alumni giving campaigns
```
Subject: Support MOIST: Alumni Giving Campaign
Includes: Donation purposes, ways to donate, recognition
```

#### 8. **Account Validation Reminder** ✅
Perfect for: Reminding alumni to update profiles
```
Subject: Action Required: Validate Your MOIST Alumni Account
Includes: Account info, validation steps
```

---

## 🎯 How to Use

### Step 1: Select Template (REQUIRED)
```
1. Open Compose Message
2. Go to Step 2
3. Click "Select Message Template" dropdown
4. Choose one of:
   - General Announcement
   - Event Invitation
   - Alumni Homecoming
   - Survey Request
   - Job Opportunity
   - Seminar/Workshop
   - Donation Appeal
   - Account Validation
   - ✏️ Custom Message (Type Your Own)
```

### Step 2: Customize the Message
```
1. Template auto-fills subject and body
2. Edit the [Insert...] placeholders with your details
3. Add event date/time if needed
4. Preview updates in real-time
5. Send test email to verify
```

### Step 3: Use Placeholders
```
Available placeholders:
- {{name}} - Full name
- {{firstname}} - First name
- {{lastname}} - Last name
- {{email}} - Email address
- {{course}} - Course/Program
- {{batch}} - Graduation year
- {{event_date}} - Event date (formatted)
- {{event_time}} - Event time range
- {{event_end_time}} - End time only
```

---

## 📝 Template Examples

### Example 1: General Announcement
```
Subject: Important Announcement from MOIST

Dear {{name}},

Greetings from MOIST!

We are pleased to share some important updates...

🗓️ Announcement Details:
Date Issued: November 7, 2025
Subject: New Library Hours
Details: The library will now be open until 8 PM on weekdays

✨ Reminders:
• Bring your ID for entry
• Follow health protocols
• Respect quiet zones

Thank you!
MOIST Administration
```

### Example 2: Event Invitation
```
Subject: You're Invited: MOIST Alumni Night

Dear {{name}},

Greetings from MOIST!

🗓️ Event Details:
Event: Alumni Night 2025
Date: {{event_date}}
Time: {{event_time}}
Venue: MOIST Auditorium
Theme: "Reconnecting Roots, Building Futures"

✨ Highlights:
• Inspiring talks from successful alumni
• Networking opportunities
• Entertainment and games

See you there!
MOIST Alumni Office
```

### Example 3: Custom Message
```
Select: ✏️ Custom Message (Type Your Own)

Then type anything you want:

Subject: Quick Update for {{course}} Alumni

Hi {{firstname}},

Just wanted to reach out about...

[Your custom message here]

Thanks!
```

---

## 🔧 Setup Instructions

### First Time Setup
```
1. Open: http://localhost/alumni/registrar/setup_messaging_db.php
2. Wait for: "✓ Default email templates inserted successfully"
3. You now have 8 templates ready to use!
```

### Verify Templates Loaded
```
1. Open Compose Message
2. Go to Step 2
3. Click template dropdown
4. You should see all 8 templates organized by category:
   - ANNOUNCEMENTS
   - EVENTS
   - SURVEYS
   - OPPORTUNITIES
   - GENERAL
```

---

## ✏️ Creating Custom Templates

### Save Your Own Template
```
1. Select "Custom Message" or edit an existing template
2. Write your subject and message
3. Click "Save as Template" button
4. Type template name (e.g., "Monthly Newsletter")
5. Select category
6. Click "Save Template"
7. Your template is now available in the dropdown!
```

### Template Categories
- **Announcements**: Official updates, policy changes
- **Events**: Invitations, celebrations, gatherings
- **Surveys**: Feedback requests, tracer studies
- **Opportunities**: Jobs, scholarships, programs
- **General**: Everything else

---

## 💡 Pro Tips

### Tip 1: Edit Templates
```
All templates have [Insert...] placeholders
Replace these with your actual details:
- [Insert Event Title] → "Alumni Homecoming 2025"
- [Insert Venue] → "MOIST Auditorium"
- [Insert Deadline] → "November 30, 2025"
```

### Tip 2: Use Event Fields
```
For any event template:
1. Fill in Event Date picker
2. Fill in Start Time
3. Fill in End Time
4. Use {{event_date}} and {{event_time}} in message
5. Automatic formatting!
```

### Tip 3: Test Before Sending
```
Always send test email first:
1. Fill in your email
2. Click "Send Test"
3. Check formatting
4. Verify placeholders work
5. Then send to all
```

### Tip 4: Save Common Messages
```
If you send similar messages often:
1. Write it once
2. Save as template
3. Use forever!
```

---

## 🎨 Customization Guide

### Editing Template Content
```
Each template has sections you can customize:

1. Greeting
   - Keep: "Dear {{name}},"
   - Or change to: "Hi {{firstname}},"

2. Body
   - Edit any text
   - Keep placeholders
   - Add your details

3. Closing
   - Keep: "MOIST Administration"
   - Or change to your office name
```

### Adding Your Details
```
Replace these placeholders:
[Insert Event Title] → Your event name
[Insert Venue] → Your location
[Insert Date] → Specific date
[Insert Time] → Specific time
[Insert Link] → Your URL
[Insert Contact Details] → Your contact info
```

---

## 📊 Template Usage Statistics

### Most Common Uses
1. **General Announcement** - Weekly updates
2. **Event Invitation** - Monthly events
3. **Survey Request** - Quarterly feedback
4. **Job Opportunity** - As needed
5. **Custom Message** - Specific communications

---

## ❓ FAQ

### Q: Can I skip selecting a template?
**A:** No, template selection is required. But you can choose "Custom Message" to write your own.

### Q: Can I edit a template after selecting it?
**A:** Yes! After selecting, you can edit both subject and message freely.

### Q: How do I add my own template?
**A:** Write your message, click "Save as Template", name it, and save!

### Q: Can I delete templates?
**A:** Default templates cannot be deleted, but you can create your own and manage them in the database.

### Q: Do placeholders work in all templates?
**A:** Yes! All templates support all placeholders.

---

## 🚀 Quick Start Checklist

```
□ Run setup_messaging_db.php
□ Open Compose Message
□ Select a template (required!)
□ Customize the content
□ Fill in event date/time (if applicable)
□ Preview your message
□ Send test email
□ Send to alumni!
```

---

## 📞 Support

### If Templates Don't Load:
1. Run: `setup_messaging_db.php`
2. Check: Database connection
3. Verify: `email_templates` table exists
4. Test: Connection test tool

### If Can't Select Template:
1. Clear browser cache
2. Refresh page
3. Check console for errors
4. Verify JavaScript is enabled

---

## 🎉 Summary

### What You Get:
✅ 8 professional MOIST-specific templates  
✅ Template selection is required (no more blank messages!)  
✅ "Custom Message" option for flexibility  
✅ Easy customization with placeholders  
✅ Save your own templates  
✅ Professional, consistent messaging  

### How It Works:
1. **Select** a template (required)
2. **Customize** the content
3. **Preview** in real-time
4. **Send** to alumni

**Start using professional templates today! 📧**

---

**Last Updated**: November 7, 2025  
**Version**: 2.0.0  
**Status**: Production Ready ✅
