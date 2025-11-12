<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b><i class="fa fa-database"></i> Database Backup</b>
                    </div>
                    <div class="card-body">
                        <?php if(isset($GLOBALS['db_missing']) && $GLOBALS['db_missing']): ?>
                        <!-- Database Missing Alert -->
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-exclamation-triangle"></i> Database Not Found!</h5>
                            <p class="mb-2">The <strong>alumni_db</strong> database does not exist. This usually happens when:</p>
                            <ul class="mb-3">
                                <li>The database was dropped in phpMyAdmin</li>
                                <li>This is a fresh installation</li>
                                <li>The database was accidentally deleted</li>
                            </ul>
                            <p class="mb-0"><strong>Solution:</strong> Use the import function below to restore your database from a backup file.</p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Backup Section -->
                        <div class="row mb-5">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fa fa-download"></i> Database Backup</h5>
                                <p class="text-muted">Create a backup of your database to protect your data. The backup will include all tables and data from the alumni_db database.</p>
                                
                                <?php if(isset($GLOBALS['db_missing']) && $GLOBALS['db_missing']): ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-info-circle"></i> <strong>Backup Unavailable:</strong> Cannot create backup because the database does not exist. Please restore from a backup file first.
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> <strong>Backup Information:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Database: <strong>alumni_db</strong></li>
                                        <li>Format: SQL dump file with database creation statements</li>
                                        <li>File will be downloaded automatically</li>
                                        <li>Recommended frequency: Weekly or before major changes</li>
                                        <li><strong>NEW:</strong> Backup includes database creation - can restore even if database is dropped!</li>
                                    </ul>
                                </div>
                                
                                <button class="btn btn-primary btn-lg" id="create-backup">
                                    <i class="fa fa-download"></i> Create & Download Backup
                                </button>
                                
                                <div id="backup-status" class="mt-3" style="display: none;">
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i> <span id="status-message">Backup created successfully!</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fa fa-clock"></i> Recent Backups</h6>
                                        <div id="recent-backups">
                                            <small class="text-muted">No recent backups found</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Import Section -->
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fa fa-upload"></i> Database Import</h5>
                                <p class="text-muted">Import a database backup file to restore your data. This will replace all existing data in the alumni_db database.</p>
                                
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> <strong>Warning:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>This will <strong>overwrite all existing data</strong></li>
                                        <li>Automatic backup will be created before importing</li>
                                        <li>Only upload SQL files from trusted sources</li>
                                        <li>Maximum file size: 50MB</li>
                                        <li><strong>NEW:</strong> Works even if database is dropped in phpMyAdmin!</li>
                                    </ul>
                                </div>
                                
                                <div class="import-section">
                                    <form id="import-form" enctype="multipart/form-data">
                                        <div class="form-group mb-3">
                                            <label for="sql-file-input" class="form-label">
                                                <i class="fa fa-file"></i> Select SQL File to Import
                                            </label>
                                            <input type="file" class="form-control" id="sql-file-input" name="sql_file" accept=".sql" required>
                                            <small class="form-text text-muted">Maximum file size: 50MB. Only .sql files are supported.</small>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <button type="button" class="btn btn-success btn-lg" id="import-database" style="margin-right: 10px;">
                                                <i class="fa fa-upload"></i> Import Database
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="reset-form">
                                                <i class="fa fa-refresh"></i> Reset
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <div id="import-progress" class="mt-3" style="display: none;">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Importing database... Please do not close this page.</small>
                                    </div>
                                    
                                    <div id="import-status" class="mt-3" style="display: none;">
                                        <div class="alert" id="import-alert">
                                            <i class="fa fa-check-circle"></i> <span id="import-message">Import completed successfully!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fa fa-info-circle"></i> Import Tips</h6>
                                        <div class="import-tips">
                                            <small class="d-block mb-2"><i class="fa fa-check text-success"></i> Ensure SQL file is from a compatible database</small>
                                            <small class="d-block mb-2"><i class="fa fa-check text-success"></i> File should contain CREATE and INSERT statements</small>
                                            <small class="d-block mb-2"><i class="fa fa-check text-success"></i> Backup current data before importing</small>
                                            <small class="d-block"><i class="fa fa-check text-success"></i> Import may take several minutes for large files</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background: linear-gradient(135deg, #8B0000, #A0522D);
        color: white;
        font-weight: bold;
    }
    
    #create-backup {
        background: linear-gradient(135deg, #8B0000, #A0522D);
        border: none;
        padding: 12px 30px;
        font-weight: bold;
    }
    
    #create-backup:hover {
        background: linear-gradient(135deg, #A0522D, #8B0000);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(139,0,0,0.3);
    }
    
    .alert-info {
        border-left: 4px solid #17a2b8;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    /* Import Section Styles */
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }
    
    .file-upload-area:hover {
        border-color: #8B0000;
        background-color: #fff;
        transform: translateY(-2px);
    }
    
    .file-upload-area.dragover {
        border-color: #8B0000;
        background-color: rgba(139, 0, 0, 0.1);
    }
    
    .upload-content h6 {
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    #import-database {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        padding: 12px 30px;
        font-weight: bold;
    }
    
    #import-database:hover {
        background: linear-gradient(135deg, #20c997, #28a745);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    #import-database:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
    }
    
    .progress-bar {
        background: linear-gradient(135deg, #8B0000, #A0522D);
    }
    
    .import-tips small {
        line-height: 1.6;
    }
</style>

<script>
$(document).ready(function(){
    // Check if database is missing and disable backup button
    <?php if(isset($GLOBALS['db_missing']) && $GLOBALS['db_missing']): ?>
    $('#create-backup').prop('disabled', true).html('<i class="fa fa-ban"></i> Backup Unavailable (Database Missing)');
    <?php endif; ?>
    
    // Load recent backups on page load
    loadRecentBackups();
    
    // Initialize import functionality
    initializeImportFunctionality();
    
    $('#create-backup').click(function(){
        var btn = $(this);
        var originalText = btn.html();
        
        // Show loading state
        btn.html('<i class="fa fa-spinner fa-spin"></i> Creating Backup...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: 'ajax.php?action=create_backup',
            method: 'POST',
            success: function(resp){
                if(resp.success) {
                    // Show success message
                    $('#status-message').text('Backup created successfully! Download started.');
                    $('#backup-status').show();
                    
                    // Trigger download
                    window.location.href = 'ajax.php?action=download_backup&file=' + resp.filename;
                    
                    // Reload recent backups
                    loadRecentBackups();
                    
                    // Hide success message after 5 seconds
                    setTimeout(function(){
                        $('#backup-status').fadeOut();
                    }, 5000);
                } else {
                    alert('Error creating backup: ' + (resp.message || 'Unknown error'));
                }
            },
            error: function(){
                alert('Error creating backup. Please try again.');
            },
            complete: function(){
                // Restore button
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });
});

function loadRecentBackups() {
    $.ajax({
        url: 'ajax.php?action=get_recent_backups',
        method: 'GET',
        success: function(resp){
            if(resp.success && resp.backups.length > 0) {
                var html = '';
                resp.backups.forEach(function(backup) {
                    html += '<div class="mb-2">';
                    html += '<small class="d-block"><i class="fa fa-file-archive"></i> ' + backup.name + '</small>';
                    html += '<small class="text-muted">' + backup.date + ' (' + backup.size + ')</small>';
                    html += '</div>';
                });
                $('#recent-backups').html(html);
            }
        }
    });
}

function initializeImportFunctionality() {
    // Import button click handler
    $('#import-database').click(function() {
        var fileInput = $('#sql-file-input')[0];
        var file = fileInput.files[0];
        
        if (!file) {
            alert('Please select an SQL file first.');
            return;
        }
        
        // Validate file type
        if (!file.name.toLowerCase().endsWith('.sql')) {
            alert('Please select a valid SQL file.');
            return;
        }
        
        // Validate file size (50MB limit)
        var maxSize = 50 * 1024 * 1024; // 50MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 50MB limit.');
            return;
        }
        
        // Show confirmation dialog
        if (!confirm('WARNING: This will replace ALL existing data in the database. Are you sure you want to continue?')) {
            return;
        }
        
        performImport(file);
    });
    
    // Reset form handler
    $('#reset-form').click(function() {
        $('#import-form')[0].reset();
        $('#import-progress').hide();
        $('#import-status').hide();
        $('#import-database').prop('disabled', false);
    });
    
    function performImport(file) {
        var formData = new FormData();
        formData.append('sql_file', file);
        formData.append('action', 'import_database');
        
        // Show progress and disable button
        $('#import-progress').show();
        $('#import-database').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
        $('#import-status').hide();
        
        var progressBar = $('.progress-bar');
        var progress = 0;
        
        // Simulate progress
        var progressInterval = setInterval(function() {
            progress += Math.random() * 10;
            if (progress > 90) progress = 90;
            progressBar.css('width', progress + '%');
        }, 1000);
        
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 300000, // 5 minutes timeout
            success: function(resp) {
                clearInterval(progressInterval);
                progressBar.css('width', '100%');
                
                setTimeout(function() {
                    $('#import-progress').hide();
                    
                    try {
                        // Log the raw response for debugging
                        console.log('Raw response:', resp);
                        console.log('Response type:', typeof resp);
                        
                        var response;
                        if (typeof resp === 'string') {
                            // Try to extract JSON from the response
                            var jsonMatch = resp.match(/\{[\s\S]*\}/);
                            if (jsonMatch) {
                                response = JSON.parse(jsonMatch[0]);
                            } else {
                                throw new Error('No JSON found in response: ' + resp.substring(0, 200));
                            }
                        } else {
                            response = resp;
                        }
                        
                        console.log('Parsed response:', response);
                        
                        if (response.success) {
                            showImportStatus('success', response.message || 'Database imported successfully!');
                            $('#import-form')[0].reset();
                            
                            // Reload the page after 3 seconds
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else {
                            showImportStatus('error', response.message || 'Import failed. Please try again.');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        console.error('Response was:', resp);
                        showImportStatus('error', 'Invalid response from server. Check browser console for details.');
                    }
                    
                    $('#import-database').prop('disabled', false).html('<i class="fa fa-upload"></i> Import Database');
                }, 1000);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('#import-progress').hide();
                
                console.error('AJAX Error:', status, error);
                console.error('XHR:', xhr);
                console.error('Response Text:', xhr.responseText);
                
                var errorMessage = 'Import failed. ';
                if (status === 'timeout') {
                    errorMessage += 'The operation timed out. Large files may take longer to process.';
                } else if (status === 'parsererror') {
                    errorMessage += 'Invalid JSON response from server. ';
                    if (xhr.responseText) {
                        errorMessage += 'Response: ' + xhr.responseText.substring(0, 200);
                    }
                } else {
                    errorMessage += 'Please check your file and try again. Error: ' + error;
                }
                
                showImportStatus('error', errorMessage);
                $('#import-database').prop('disabled', false).html('<i class="fa fa-upload"></i> Import Database');
            }
        });
    }
    
    function showImportStatus(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        $('#import-alert').removeClass('alert-success alert-danger').addClass(alertClass);
        $('#import-alert i').removeClass('fa-check-circle fa-exclamation-triangle').addClass(icon);
        $('#import-message').text(message);
        $('#import-status').show();
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('#import-status').fadeOut();
            }, 5000);
        }
    }
}
</script>
