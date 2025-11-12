<?php
session_start();
include '../admin/db_connect.php';

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Inbox - MOIST Alumni</title>
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        :root {
            --primary: #800000;
            --primary-dark: #600000;
            --secondary: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .inbox-container {
            max-width: 1400px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .inbox-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .inbox-header h4 {
            margin: 0;
            font-size: 24px;
        }
        
        .message-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .message-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 30px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .message-item:hover {
            background: #f8f9fa;
        }
        
        .message-item.unread {
            background: #fff8e1;
            font-weight: 600;
        }
        
        .message-checkbox {
            flex-shrink: 0;
        }
        
        .message-content {
            flex-grow: 1;
            min-width: 0;
        }
        
        .message-subject {
            font-size: 16px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .message-preview {
            font-size: 13px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .message-meta {
            flex-shrink: 0;
            text-align: right;
            font-size: 13px;
            color: #666;
        }
        
        .message-stats {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }
        
        .stat-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        
        .toolbar {
            padding: 15px 30px;
            background: #fafafa;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-compose {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-compose:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .search-box {
            flex-grow: 1;
            max-width: 400px;
        }
        
        .stat-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 8px;
        }
        .rsvp-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .rsvp-summary {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .rsvp-card {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .rsvp-card.accept {
            background: #d4edda;
            border: 2px solid #28a745;
        }
        
        .rsvp-card.decline {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }
        
        .rsvp-card.maybe {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }
        
        .rsvp-card h4 {
            font-size: 32px;
            margin: 0;
            font-weight: 700;
        }
        
        .rsvp-card p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .message-detail-modal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .recipient-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .recipient-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
        }
        
        .recipient-badge.read {
            background: #c8e6c9;
            color: #2e7d32;
        }
        
        .message-body-content {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .message-item {
            position: relative;
        }
        
        .message-actions-menu {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
        }
        
        .dots-button {
            background: transparent;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .dots-button:hover {
            background: #f0f0f0;
            color: #800000;
        }
        
        .dropdown-menu {
            min-width: 180px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
        }
        
        .dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
        }
        
        .dropdown-item.text-danger:hover {
            background: #fff5f5;
        }
        
        @media (max-width: 768px) {
            .message-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .message-meta {
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="inbox-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fas fa-paper-plane me-2"></i>Sent Messages</h4>
                <p class="text-muted mb-0">
                    View and manage your sent messages
                    <span class="badge bg-success ms-2" style="font-size: 11px;">
                        <i class="fas fa-sync-alt me-1"></i>Auto-refresh: 30s
                    </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" id="searchMessages" class="form-control search-box" placeholder="Search messages...">
                <button id="refreshMessages" class="btn btn-outline-primary" title="Refresh messages">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <a href="alumni.php" class="btn btn-compose">
                    <i class="fas fa-plus me-2"></i>Compose New
                </a>
            </div>
        </div>
        
        <div id="messageListContainer">
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading messages...</p>
            </div>
        </div>
    </div>
    
    <!-- Message Detail Modal -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-envelope-open me-2"></i>Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageDetailContent">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        let allMessages = [];
        
        $(document).ready(function() {
            loadMessages();
            
            // Auto-refresh every 30 seconds for real-time updates
            let autoRefreshInterval = setInterval(function() {
                console.log('Auto-refreshing messages...');
                loadMessages();
            }, 30000); // 30 seconds
            
            // Manual refresh button
            $('#refreshMessages').click(function() {
                $(this).find('i').addClass('fa-spin');
                loadMessages();
                setTimeout(() => {
                    $(this).find('i').removeClass('fa-spin');
                }, 1000);
            });
            
            // Search functionality
            $('#searchMessages').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                filterMessages(searchTerm);
            });
            
            // Stop auto-refresh when page is hidden
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    clearInterval(autoRefreshInterval);
                } else {
                    loadMessages();
                    autoRefreshInterval = setInterval(loadMessages, 30000);
                }
            });
        });
        
        function loadMessages() {
            $('#messageListContainer').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading messages...</p></div>');
            
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: { action: 'get_sent_messages' },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.status === 'success') {
                        allMessages = response.messages;
                        displayMessages(allMessages);
                    } else {
                        showError('Failed to load messages: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load messages error:', status, error);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMsg = 'Failed to connect to server. ';
                    if (status === 'timeout') {
                        errorMsg += 'Request timed out.';
                    } else if (xhr.status === 404) {
                        errorMsg += 'Server endpoint not found.';
                    } else if (xhr.status === 500) {
                        errorMsg += 'Server error. Check if database columns exist.';
                    } else {
                        errorMsg += 'Please check your connection.';
                    }
                    
                    $('#messageListContainer').html(`
                        <div class="alert alert-danger m-3">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error Loading Messages</h5>
                            <p>${errorMsg}</p>
                            <p><small>Status: ${status}, Code: ${xhr.status}</small></p>
                            <button class="btn btn-primary btn-sm mt-2" onclick="loadMessages()">
                                <i class="fas fa-sync me-1"></i>Try Again
                            </button>
                            <a href="test_connection.html" target="_blank" class="btn btn-info btn-sm mt-2 ms-2">
                                <i class="fas fa-stethoscope me-1"></i>Test Connection
                            </a>
                        </div>
                    `);
                }
            });
        }
        
        function displayMessages(messages) {
            const container = $('#messageListContainer');
            
            if (messages.length === 0) {
                container.html(`
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No messages yet</h5>
                        <p>Start composing messages to your alumni!</p>
                    </div>
                `);
                return;
            }
            
            let html = '<ul class="message-list">';
            
            messages.forEach(msg => {
                const date = new Date(msg.sent_at);
                const timeAgo = getTimeAgo(date);
                const readPercentage = msg.recipient_count > 0 ? Math.round((msg.read_count / msg.recipient_count) * 100) : 0;
                
                // Check if message has RSVP responses
                const hasRSVP = (msg.accept_count > 0 || msg.decline_count > 0 || msg.maybe_count > 0);
                const totalResponses = (parseInt(msg.accept_count) || 0) + (parseInt(msg.decline_count) || 0) + (parseInt(msg.maybe_count) || 0);
                const responseRate = msg.recipient_count > 0 ? Math.round((totalResponses / msg.recipient_count) * 100) : 0;
                
                const rsvpHtml = hasRSVP ? `
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                        <span class="stat-badge stat-accept" style="font-weight: 700;">
                            <i class="fas fa-check-circle me-1"></i>${msg.accept_count} Accepted
                        </span>
                        <span class="stat-badge stat-maybe" style="font-weight: 700;">
                            <i class="fas fa-question-circle me-1"></i>${msg.maybe_count} Maybe
                        </span>
                        <span class="stat-badge stat-decline" style="font-weight: 700;">
                            <i class="fas fa-times-circle me-1"></i>${msg.decline_count} Declined
                        </span>
                        <span class="stat-badge" style="background: #e0e0e0; color: #666; font-weight: 700;">
                            <i class="fas fa-chart-line me-1"></i>${responseRate}% Response Rate
                        </span>
                    </div>
                ` : '';
                
                html += `
                    <li class="message-item" onclick="viewMessageDetails(${msg.id})">
                        <div class="message-actions-menu" onclick="event.stopPropagation()">
                            <div class="dropdown">
                                <button class="dots-button" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" onclick="markAsUnread(${msg.id})"><i class="fas fa-envelope"></i>Mark as Unread</a></li>
                                    <li><a class="dropdown-item" onclick="archiveMessage(${msg.id})"><i class="fas fa-archive"></i>Archive</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" onclick="deleteMessage(${msg.id})"><i class="fas fa-trash"></i>Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="message-content">
                            <div class="message-subject">
                                <i class="fas fa-envelope me-2"></i>
                                ${escapeHtml(msg.subject)}
                                ${hasRSVP ? '<span class="badge bg-info ms-2">RSVP</span>' : ''}
                            </div>
                            <div class="message-preview">${stripHtml(msg.message_body).substring(0, 100)}...</div>
                            <div class="message-stats">
                                <span class="stat-badge">
                                    <i class="fas fa-users me-1"></i>${msg.recipient_count} recipients
                                </span>
                                <span class="stat-badge">
                                    <i class="fas fa-eye me-1"></i>${msg.read_count} read (${readPercentage}%)
                                </span>
                                ${msg.reply_count > 0 ? `
                                    <span class="stat-badge" style="background: #e3f2fd; color: #1976d2; font-weight: 700;">
                                        <i class="fas fa-reply me-1"></i>${msg.reply_count} ${msg.reply_count === 1 ? 'Reply' : 'Replies'}
                                    </span>
                                ` : ''}
                                ${rsvpHtml}
                            </div>
                        </div>
                        <div class="message-meta">
                            <div>${timeAgo}</div>
                        </div>
                    </li>
                `;
            });
            
            html += '</ul>';
            container.html(html);
        }
        
        function filterMessages(searchTerm) {
            if (!searchTerm) {
                displayMessages(allMessages);
                return;
            }
            
            const filtered = allMessages.filter(msg => 
                msg.subject.toLowerCase().includes(searchTerm) ||
                msg.message_body.toLowerCase().includes(searchTerm)
            );
            
            displayMessages(filtered);
        }
        
        function viewMessageDetails(messageId) {
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: { 
                    action: 'get_message_details',
                    message_id: messageId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showMessageDetail(response.message);
                    } else {
                        showError('Failed to load message details');
                    }
                },
                error: function() {
                    showError('Failed to connect to server');
                }
            });
        }
        
        function showMessageDetail(message) {
            const date = new Date(message.sent_at);
            const formattedDate = date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Count RSVP responses and build attendee lists
            let acceptCount = 0, declineCount = 0, pendingCount = 0;
            let acceptedList = [], declinedList = [], pendingList = [];
            
            message.recipients.forEach(r => {
                const name = r.firstname + ' ' + r.lastname;
                if (r.rsvp_status === 'accept') {
                    acceptCount++;
                    acceptedList.push(name);
                } else if (r.rsvp_status === 'decline') {
                    declineCount++;
                    declinedList.push(name);
                } else {
                    pendingCount++;
                    pendingList.push(name);
                }
            });
            
            const hasRSVP = (acceptCount > 0 || declineCount > 0);
            
            // RSVP Summary with Professional Attendee Lists
            let rsvpSummaryHtml = '';
            if (hasRSVP || pendingCount > 0) {
                rsvpSummaryHtml = `
                    <div class="rsvp-section">
                        <h6><i class="fas fa-calendar-check me-2"></i>RSVP Responses</h6>
                        <div class="rsvp-summary">
                            <div class="rsvp-card accept">
                                <h4>${acceptCount}</h4>
                                <p><i class="fas fa-check me-1"></i>Will Attend</p>
                            </div>
                            <div class="rsvp-card decline">
                                <h4>${declineCount}</h4>
                                <p><i class="fas fa-times me-1"></i>Cannot Attend</p>
                            </div>
                        </div>
                        <p class="text-muted mb-3"><small>${pendingCount} pending response(s)</small></p>
                        
                        <!-- Professional Attendee Lists -->
                        ${acceptCount > 0 ? `
                            <div class="card mb-3" style="border-left: 4px solid #28a745;">
                                <div class="card-header" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; font-weight: 700;">
                                    <i class="fas fa-check-circle me-2"></i>Attending (${acceptCount})
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        ${acceptedList.map(name => `<li style="padding: 5px 0;"><i class="fas fa-user-check text-success me-2"></i><strong>${escapeHtml(name)}</strong></li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${declineCount > 0 ? `
                            <div class="card mb-3" style="border-left: 4px solid #dc3545;">
                                <div class="card-header" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; font-weight: 700;">
                                    <i class="fas fa-times-circle me-2"></i>Cannot Attend (${declineCount})
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        ${declinedList.map(name => `<li style="padding: 5px 0;"><i class="fas fa-user-times text-danger me-2"></i><strong>${escapeHtml(name)}</strong></li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        ` : ''}
                        
                        ${pendingCount > 0 ? `
                            <div class="card" style="border-left: 4px solid #ffc107;">
                                <div class="card-header" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; font-weight: 700;">
                                    <i class="fas fa-clock me-2"></i>Pending Response (${pendingCount})
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        ${pendingList.map(name => `<li style="padding: 5px 0;"><i class="fas fa-user-clock text-warning me-2"></i><strong>${escapeHtml(name)}</strong></li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            }
            
            // Recipients list with RSVP status
            let recipientsHtml = '<div class="recipient-list">';
            message.recipients.forEach(recipient => {
                const readClass = recipient.is_read ? 'read' : '';
                const readIcon = recipient.is_read ? '<i class="fas fa-check-double me-1"></i>' : '<i class="fas fa-check me-1"></i>';
                
                let rsvpBadge = '';
                if (recipient.rsvp_status === 'accept') {
                    rsvpBadge = '<span class="badge bg-success ms-2">✓ Accepted</span>';
                } else if (recipient.rsvp_status === 'decline') {
                    rsvpBadge = '<span class="badge bg-danger ms-2">✗ Declined</span>';
                }
                
                recipientsHtml += `
                    <span class="recipient-badge ${readClass}" title="${recipient.is_read ? 'Read on ' + recipient.read_at : 'Not read yet'}">
                        ${readIcon}${escapeHtml(recipient.firstname + ' ' + recipient.lastname)}${rsvpBadge}
                    </span>
                `;
            });
            recipientsHtml += '</div>';
            
            const content = `
                <div class="mb-3">
                    <h5>${escapeHtml(message.subject)}</h5>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>${formattedDate}
                    </small>
                </div>
                
                ${rsvpSummaryHtml}
                
                <div class="mb-3">
                    <strong>Recipients (${message.recipients.length}):</strong>
                    ${recipientsHtml}
                </div>
                
                <div class="message-body-content">
                    ${message.message_body}
                </div>
                
                <div id="repliesSection" class="mt-4">
                    <h6><i class="fas fa-reply-all me-2"></i>Email Replies from Alumni</h6>
                    <div id="repliesContainer">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <p class="text-muted mb-0 mt-2">Loading replies...</p>
                        </div>
                    </div>
                    
                    <!-- Add Reply Form -->
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <strong><i class="fas fa-plus-circle me-2"></i>Record Alumni Reply</strong>
                            <small class="text-muted d-block">If alumni replied to your email, record it here</small>
                        </div>
                        <div class="card-body">
                            <form id="recordReplyForm">
                                <input type="hidden" id="replyMessageId" value="${message.id}">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Select Alumni:</strong></label>
                                    <select class="form-select" id="replyRecipientEmail" required>
                                        <option value="">-- Select who replied --</option>
                                        ${message.recipients.map(r => `<option value="${r.email}">${r.firstname} ${r.lastname} (${r.email})</option>`).join('')}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Reply Content:</strong></label>
                                    <textarea class="form-control" id="replyContent" rows="4" placeholder="Paste the reply message from Gmail here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Record Reply
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Registrar Response Form -->
                    <div class="card mt-3">
                        <div class="card-header" style="background: #800000; color: white;">
                            <strong><i class="fas fa-reply me-2"></i>Send Response to Alumni</strong>
                        </div>
                        <div class="card-body">
                            <form id="sendResponseForm">
                                <input type="hidden" id="responseMessageId" value="${message.id}">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Send To:</strong></label>
                                    <select class="form-select" id="responseRecipient" required>
                                        <option value="">-- Select recipient --</option>
                                        ${message.recipients.map(r => `<option value="${r.id}" data-email="${r.email}">${r.firstname} ${r.lastname} (${r.email})</option>`).join('')}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Subject:</strong></label>
                                    <input type="text" class="form-control" id="responseSubject" value="Re: ${escapeHtml(message.subject)}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Your Response:</strong></label>
                                    <textarea class="form-control" id="responseBody" rows="5" placeholder="Type your response here..." required></textarea>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sendResponseEmail" checked>
                                    <label class="form-check-label" for="sendResponseEmail">
                                        Send email notification to alumni
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Response
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            
            $('#messageDetailContent').html(content);
            new bootstrap.Modal(document.getElementById('messageDetailModal')).show();
            
            // Load replies for this message
            loadReplies(message.id);
            
            // Handle record reply form
            $('#recordReplyForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                recordAlumniReply();
            });
            
            // Handle send response form
            $('#sendResponseForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                sendResponseToAlumni();
            });
        }
        
        function loadReplies(messageId) {
            $.ajax({
                url: 'email_reply_handler.php',
                method: 'POST',
                data: { action: 'get_replies', message_id: messageId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayReplies(response.replies);
                    } else {
                        $('#repliesContainer').html('<p class="text-muted text-center">No replies yet</p>');
                    }
                },
                error: function() {
                    $('#repliesContainer').html('<p class="text-danger text-center">Failed to load replies</p>');
                }
            });
        }
        
        function displayReplies(replies) {
            if (replies.length === 0) {
                $('#repliesContainer').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No replies yet. Replies from alumni will appear here.
                    </div>
                `);
                return;
            }
            
            let html = '<div class="list-group">';
            replies.forEach(reply => {
                const date = new Date(reply.replied_at);
                const timeAgo = getTimeAgo(date);
                
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><i class="fas fa-user-circle me-1"></i>${escapeHtml(reply.firstname + ' ' + reply.lastname)}</strong>
                                <br><small class="text-muted">${reply.email}</small>
                            </div>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                        <div class="reply-content" style="background: #f8f9fa; padding: 12px; border-radius: 6px; border-left: 3px solid #1976d2;">
                            ${escapeHtml(reply.reply_content).replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            $('#repliesContainer').html(html);
        }
        
        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " years ago";
            
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " months ago";
            
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " days ago";
            
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " hours ago";
            
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutes ago";
            
            return "Just now";
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function stripHtml(html) {
            const div = document.createElement('div');
            div.innerHTML = html;
            return div.textContent || div.innerText || '';
        }
        
        function recordAlumniReply() {
            const messageId = $('#replyMessageId').val();
            const recipientEmail = $('#replyRecipientEmail').val();
            const replyContent = $('#replyContent').val();
            
            if (!recipientEmail || !replyContent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select alumni and enter reply content',
                    confirmButtonColor: '#800000'
                });
                return;
            }
            
            $.ajax({
                url: 'email_reply_handler.php',
                method: 'POST',
                data: {
                    action: 'record_reply',
                    message_id: messageId,
                    recipient_email: recipientEmail,
                    reply_content: replyContent
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reply Recorded!',
                            text: 'Alumni reply has been recorded successfully',
                            confirmButtonColor: '#800000'
                        });
                        $('#recordReplyForm')[0].reset();
                        loadReplies(messageId);
                        loadMessages(); // Refresh list
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to record reply',
                            confirmButtonColor: '#800000'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Failed to connect to server',
                        confirmButtonColor: '#800000'
                    });
                }
            });
        }
        
        function sendResponseToAlumni() {
            const messageId = $('#responseMessageId').val();
            const recipientSelect = $('#responseRecipient');
            const recipientId = recipientSelect.val();
            const recipientEmail = recipientSelect.find(':selected').data('email');
            const subject = $('#responseSubject').val();
            const body = $('#responseBody').val();
            const sendEmail = $('#sendResponseEmail').is(':checked');
            
            if (!recipientId || !subject || !body) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in all fields',
                    confirmButtonColor: '#800000'
                });
                return;
            }
            
            Swal.fire({
                title: 'Sending Response...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    action: 'send_single',
                    recipient: JSON.stringify({
                        id: recipientId,
                        email: recipientEmail,
                        firstname: recipientSelect.find(':selected').text().split('(')[0].trim().split(' ')[0],
                        lastname: recipientSelect.find(':selected').text().split('(')[0].trim().split(' ').slice(1).join(' ')
                    }),
                    subject: subject,
                    message_body: body,
                    send_email: sendEmail
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Response Sent!',
                            text: sendEmail ? 'Your response has been sent via email' : 'Your response has been recorded',
                            confirmButtonColor: '#800000'
                        });
                        $('#sendResponseForm')[0].reset();
                        loadMessages(); // Refresh list
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to send response',
                            confirmButtonColor: '#800000'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Failed to connect to server',
                        confirmButtonColor: '#800000'
                    });
                }
            });
        }
        
        function markAsUnread(messageId) {
            // Mark message as unread (for all recipients)
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    action: 'mark_unread',
                    message_id: messageId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Message marked as unread');
                        loadMessages();
                    } else {
                        toastr.error(response.message || 'Failed to mark as unread');
                    }
                },
                error: function() {
                    toastr.error('Connection error');
                }
            });
        }
        
        function archiveMessage(messageId) {
            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    action: 'archive_message',
                    message_id: messageId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('Message archived successfully');
                        loadMessages();
                    } else {
                        toastr.error(response.message || 'Failed to archive message');
                    }
                },
                error: function() {
                    toastr.error('Connection error');
                }
            });
        }
        
        function deleteMessage(messageId) {
            Swal.fire({
                title: 'Delete Message?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'send_message.php',
                        method: 'POST',
                        data: {
                            action: 'delete_message',
                            message_id: messageId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success('Message deleted successfully');
                                loadMessages();
                            } else {
                                toastr.error(response.message || 'Failed to delete message');
                            }
                        },
                        error: function() {
                            toastr.error('Connection error');
                        }
                    });
                }
            });
        }
        
        function showError(message) {
            toastr.error(message);
        }
        
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>
</body>
</html>
