# 🔍 SYSTEM TEST GUIDE

## Issue 1: RSVP Section Not Showing

### Why It's Not Showing:
The RSVP section only appears when:
1. ✅ Message has RSVP enabled (checkbox checked when sending)
2. ✅ Someone has responded (clicked Accept or Decline)

### How to Fix:
```
1. Send a NEW email
2. Check "Enable RSVP" checkbox
3. Send email
4. Open email in Gmail
5. Click "✓ YES, I WILL ATTEND"
6. Go back to view_messages.php
7. Click the message
8. NOW you'll see RSVP section!
```

---

## Issue 2: Read Status Not Updating

### Why It's Not Updating:
The tracking pixel needs:
1. ✅ Correct message_id
2. ✅ Correct recipient_id
3. ✅ Email to be opened (not just viewed in list)

### How to Fix:
```
1. Send NEW email (not old one)
2. Open email in Gmail (actually open it, not just preview)
3. Wait 5 seconds for pixel to load
4. Go to view_messages.php
5. Click refresh button
6. Should show: 1 read (100%)
```

---

## 🚀 COMPLETE TEST PROCEDURE:

### Step 1: Send Test Email
```
1. Go to registrar/alumni.php
2. Click "Compose New"
3. Select recipient: ABEJO CHARLIE
4. Enter subject: "Test Event Invitation"
5. Enter message
6. ✅ CHECK "Enable RSVP" checkbox
7. Fill event details (optional)
8. Click "Send Message"
9. Wait for success message
```

### Step 2: Open Email
```
1. Open Gmail (capstonee2@gmail.com)
2. Find the email
3. CLICK to open it (not just preview)
4. Wait 5 seconds
5. Tracking pixel loads (invisible)
```

### Step 3: Click RSVP
```
1. In the email, scroll down
2. See RSVP section:
   [✓ YES, I WILL ATTEND]
   [✗ NO, I CANNOT ATTEND]
3. Click "✓ YES, I WILL ATTEND"
4. See confirmation page
5. RSVP recorded!
```

### Step 4: Check Dashboard
```
1. Go to view_messages.php
2. Click refresh button (or wait 30s)
3. Should see:
   - 1 read (100%)  ← Email was opened
   - ✓ 1 Will Attend  ← RSVP recorded
   - 📊 100% Response Rate
4. Click the message
5. See RSVP Responses section:
   ┌─────────┐  ┌─────────┐
   │    1    │  │    0    │
   │  Will   │  │ Cannot  │
   │ Attend  │  │ Attend  │
   └─────────┘  └─────────┘
   
   ✓ Attending (1)
     ✓ ABEJO CHARLIE
```

---

## ❌ Common Issues:

### Issue: "0 read (0%)" even after opening
**Cause:** Old email sent before tracking pixel was added
**Fix:** Send NEW email

### Issue: RSVP section not showing
**Cause:** RSVP not enabled when sending
**Fix:** Check "Enable RSVP" checkbox when sending

### Issue: Attendee list empty
**Cause:** No one has clicked RSVP buttons yet
**Fix:** Click RSVP button in email first

### Issue: "200% read" or wrong percentage
**Cause:** Database issue with recipient count
**Fix:** Send fresh email to test

---

## ✅ What Should Work:

### Email:
✅ Tracking pixel (invisible)
✅ RSVP buttons (if enabled)
✅ Reply button
✅ Event details (if filled)

### Dashboard:
✅ Read count (after opening email)
✅ RSVP count (after clicking button)
✅ Attendee lists (after RSVP)
✅ Auto-refresh (every 30s)

### Message Detail:
✅ RSVP Responses (if enabled and responded)
✅ Attendee lists (with names)
✅ Reply section
✅ Recipient status

---

## 🎯 QUICK FIX:

If nothing is working:

```
1. Delete all old test emails
2. Send ONE fresh email
3. Check "Enable RSVP" ✅
4. Send to yourself
5. Open in Gmail
6. Wait 10 seconds
7. Click RSVP button
8. Go to view_messages.php
9. Click refresh
10. Click the message
11. ✅ Everything should work!
```

---

## 📝 CHECKLIST:

Before reporting issue:
```
□ Sent NEW email (not old one)
□ Checked "Enable RSVP" when sending
□ Actually opened email in Gmail (not preview)
□ Waited 5+ seconds for pixel to load
□ Clicked RSVP button in email
□ Refreshed view_messages.php
□ Clicked message to see details
□ Waited for auto-refresh (30s)
```

---

**Try sending a FRESH email with RSVP enabled, open it in Gmail, click the RSVP button, then check the dashboard. Everything should work! 🚀**
