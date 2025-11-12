# Backup and Restore Testing Guide

## Overview
The backup and restore functionality has been completely rewritten to handle all edge cases, including when the database is dropped from phpMyAdmin.

## Key Improvements Made

### 1. **Backup Function Enhancements**
- ✅ Now includes `CREATE DATABASE IF NOT EXISTS` statement
- ✅ Includes `USE database` statement
- ✅ Sets proper SQL modes and foreign key checks
- ✅ Creates complete, self-contained SQL dumps

### 2. **Restore Function Enhancements**
- ✅ Connects to MySQL server without selecting a database first
- ✅ Automatically creates `alumni_db` database if it doesn't exist
- ✅ Handles dropped databases gracefully
- ✅ Creates automatic backup before import (if database exists)
- ✅ Properly handles CREATE DATABASE, USE, and all SQL statements
- ✅ Provides detailed success/error messages

## Testing Scenarios

### Scenario 1: Normal Backup and Restore (Database Exists)
**Steps:**
1. Navigate to Admin Panel → Backup page
2. Click "Create & Download Backup"
3. Verify backup file downloads successfully
4. Click "Select SQL File to Import" and choose the backup file
5. Click "Import Database"
6. Verify success message appears

**Expected Result:** ✅ Database restored successfully with all data intact

---

### Scenario 2: Restore After Dropping Database (Main Test)
**Steps:**
1. First, create a backup of current database
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Select `alumni_db` database
4. Click "Operations" tab
5. Scroll down and click "Drop the database (DROP)"
6. Confirm the deletion
7. Go back to Admin Panel → Backup page
8. Upload the backup SQL file you created in step 1
9. Click "Import Database"

**Expected Result:** ✅ Database `alumni_db` is automatically created and all data is restored

---

### Scenario 3: Restore with Non-Existent Database
**Steps:**
1. Ensure you have a backup file
2. In phpMyAdmin, drop the `alumni_db` database if it exists
3. In Admin Panel, go to Backup page
4. Upload the backup SQL file
5. Click "Import Database"

**Expected Result:** ✅ System creates database and imports all data successfully

---

### Scenario 4: Large File Import
**Steps:**
1. Create a backup of a database with substantial data
2. Import the backup file (test with files up to 50MB)

**Expected Result:** ✅ Progress bar shows, import completes successfully

---

## Technical Details

### What Happens During Backup:
```sql
-- The backup file now includes:
CREATE DATABASE IF NOT EXISTS `alumni_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `alumni_db`;
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- Then all table structures and data
-- ...

SET FOREIGN_KEY_CHECKS=1;
```

### What Happens During Restore:
1. **File Validation**: Checks file type and size
2. **Pre-Import Backup**: Creates automatic backup if database exists
3. **Connection**: Connects to MySQL without database selection
4. **Database Creation**: Automatically creates `alumni_db` if needed
5. **SQL Execution**: Runs all statements from backup file
6. **Error Handling**: Handles errors gracefully, retries on database-not-found errors
7. **Reconnection**: Reconnects to database for normal operations

### Error Handling:
- ✅ Handles dropped databases
- ✅ Handles missing databases
- ✅ Handles connection errors
- ✅ Provides detailed error messages
- ✅ Creates automatic backups before import
- ✅ Rolls back on critical errors

## File Locations

- **Backup Files**: `c:\xampp\htdocs\alumni\admin\backups\`
- **Backup Format**: `alumni_backup_YYYY-MM-DD_HH-ii-ss.sql`
- **Pre-Import Backup**: `pre_import_backup_YYYY-MM-DD_HH-ii-ss.sql`

## Troubleshooting

### Issue: "Connection failed" error
**Solution**: Ensure XAMPP MySQL is running

### Issue: "File size exceeds limit"
**Solution**: Files must be under 50MB. For larger databases, use phpMyAdmin or MySQL command line

### Issue: Import takes too long
**Solution**: This is normal for large databases. The timeout is set to 5 minutes (300 seconds)

### Issue: Some errors during import
**Solution**: Non-critical errors are logged but don't stop the import. Check the error count in the success message

## Success Indicators

When import is successful, you should see:
- ✅ "Database imported successfully!" message
- ✅ Number of executed statements
- ✅ "Database 'alumni_db' was created" (if database was dropped)
- ✅ Backup file name (if pre-import backup was created)
- ✅ Page reloads after 3 seconds

## Database Credentials

The system uses these default credentials:
- **Host**: localhost
- **Username**: root
- **Password**: (empty)
- **Database**: alumni_db

## Important Notes

1. **Always test with a backup first** - Create a backup before testing the drop scenario
2. **Maximum file size** - 50MB limit for uploads
3. **Timeout** - 5 minutes (300 seconds) for large imports
4. **Automatic backup** - System creates backup before import if database exists
5. **Character set** - Uses utf8mb4 for full Unicode support

## Quick Test Command

To quickly test if the restore works after dropping the database:

1. Create backup via web interface
2. Run in MySQL command line:
   ```sql
   DROP DATABASE IF EXISTS alumni_db;
   ```
3. Import via web interface
4. Verify database is recreated with all data

## Support

If you encounter any issues:
1. Check XAMPP MySQL is running
2. Check file permissions on backups folder
3. Check PHP error logs
4. Verify SQL file is not corrupted
5. Try with a smaller backup file first
