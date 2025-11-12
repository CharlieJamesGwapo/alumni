<?php
/**
 * Alumni Messaging Interface
 * Allows alumni to send messages to registrar and view responses
 */
session_start();
include 'admin/db_connect.php';

// Check if user is logged in as alumni
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 3) {
    header("Location: login.php");
    exit();
}

$alumni_id = $_SESSION['user_id'];

// Get alumni info
$stmt = $conn->prepare("SELECT firstname, lastname, email FROM alumnus_bio WHERE id = ?");
$stmt->bind_param("i", $alumni_id);
$stmt->execute();
$alumni_info = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - MOIST Alumni</title>
    <link rel="icon" type="image/png" href="assets/uploads/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #800000;
            --primary-dark: #600000;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .messages-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .messages-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 30px;
        }
        
        .message-tabs {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .message-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .message-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 20px 30px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .message-item:hover {
            background: #f8f9fa;
        }
        
        .message-item.unread {
            background: #fff8e1;
            font-weight: 600;
        }
        
        .message-subject {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .message-preview {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .message-date {
            font-size: 12px;
            color: #999;
        }
        
        .compose-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .compose-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
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
        
        .badge-unread {
            background: #ff5722;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <div class="messages-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>My Messages</h4>
                    <small>Communication with MOIST Alumni Office</small>
                </div>
                <div>
                    <button class="compose-btn" data-bs-toggle="modal" data-bs-target="#composeModal">
                        <i class="fas fa-pen me-2"></i>New Message
                    </button>
                    <a href="home.php" class="btn btn-light btn-sm ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
        
        <ul class="nav nav-tabs message-tabs px-3 pt-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#inbox">
                    <i class="fas fa-inbox me-1"></i>Inbox <span class="badge-unread" id="unreadCount" style="display:none;">0</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sent">
                    <i class="fas fa-paper-plane me-1"></i>Sent
                </a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane fade show active" id="inbox">
                <div id="inboxMessages">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Loading messages...</p>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="sent">
                <div id="sentMessages">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Loading messages...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Compose Message Modal -->
    <div class="modal fade" id="composeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-pen me-2"></i>New Message to Registrar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">To:</label>
                        <input type="text" class="form-control" value="MOIST Alumni Office (Registrar)" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject: <span class="text-danger">*</span></label>
                        <input type="text" id="composeSubject" class="form-control" placeholder="Enter subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message: <span class="text-danger">*</span></label>
                        <textarea id="composeMessage" class="form-control" rows="8" placeholder="Type your message here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendMessageBtn">
                        <i class="fas fa-paper-plane me-1"></i>Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message Detail Modal -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailSubject"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailBody">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="replyBtn">
                        <i class="fas fa-reply me-1"></i>Reply
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let currentMessageId = null;
        
        $(document).ready(function() {
            loadInboxMessages();
            
            // Tab switching
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                if (e.target.getAttribute('href') === '#sent') {
                    loadSentMessages();
                } else {
                    loadInboxMessages();
                }
            });
        });
        
        function loadInboxMessages() {
            $('#inboxMessages').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading messages...</p></div>');
            
            $.ajax({
                url: 'registrar/send_message.php',
                method: 'POST',
                data: { action: 'get_received_messages' },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.status === 'success') {
                        displayInboxMessages(response.messages);
                        updateUnreadCount(response.messages);
                    } else {
                        $('#inboxMessages').html(`<div class="alert alert-warning m-3">${response.message || 'Failed to load messages'}</div>`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load messages:', status, error);
                    let errorMsg = 'Failed to connect to server. ';
                    if (status === 'timeout') {
                        errorMsg += 'Request timed out.';
                    } else if (xhr.status === 404) {
                        errorMsg += 'Server endpoint not found.';
                    } else {
                        errorMsg += 'Please check your connection.';
                    }
                    $('#inboxMessages').html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> ${errorMsg}
                            <br><small>Status: ${status}, Code: ${xhr.status}</small>
                        </div>
                    `);
                }
            });
        }
        
        function displayInboxMessages(messages) {
            if (messages.length === 0) {
                $('#inboxMessages').html(`
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No messages yet</h5>
                        <p>You haven't received any messages from the alumni office</p>
                    </div>
                `);
                return;
            }
            
            let html = '<ul class="message-list">';
            messages.forEach(msg => {
                const unreadClass = msg.is_read == 0 ? 'unread' : '';
                const date = new Date(msg.sent_at);
                const timeAgo = getTimeAgo(date);
                const preview = stripHtml(msg.message_body).substring(0, 100);
                
                html += `
                    <li class="message-item ${unreadClass}" onclick="viewMessage(${msg.id})">
                        <div class="message-subject">
                            ${msg.is_read == 0 ? '<i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>' : ''}
                            ${escapeHtml(msg.subject)}
                        </div>
                        <div class="message-preview">${preview}...</div>
                        <div class="message-date">
                            <i class="fas fa-clock me-1"></i>${timeAgo}
                        </div>
                    </li>
                `;
            });
            html += '</ul>';
            
            $('#inboxMessages').html(html);
        }
        
        function updateUnreadCount(messages) {
            const unreadCount = messages.filter(m => m.is_read == 0).length;
            if (unreadCount > 0) {
                $('#unreadCount').text(unreadCount).show();
            } else {
                $('#unreadCount').hide();
            }
        }
        
        function viewMessage(messageId) {
            $.ajax({
                url: 'registrar/send_message.php',
                method: 'POST',
                data: { 
                    action: 'get_message_details',
                    message_id: messageId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showMessageDetail(response.message);
                        markAsRead(messageId);
                    }
                }
            });
        }
        
        function showMessageDetail(message) {
            currentMessageId = message.id;
            const date = new Date(message.sent_at);
            const formattedDate = date.toLocaleString();
            
            $('#detailSubject').text(message.subject);
            $('#detailBody').html(`
                <div class="mb-3">
                    <strong>From:</strong> MOIST Alumni Office<br>
                    <strong>Date:</strong> ${formattedDate}
                </div>
                <hr>
                <div style="line-height: 1.8;">
                    ${message.message_body}
                </div>
            `);
            
            new bootstrap.Modal(document.getElementById('messageDetailModal')).show();
        }
        
        function markAsRead(messageId) {
            $.post('registrar/send_message.php', {
                action: 'mark_read',
                message_id: messageId
            }).done(function() {
                loadInboxMessages();
            });
        }
        
        // Send new message
        $('#sendMessageBtn').click(function() {
            const subject = $('#composeSubject').val().trim();
            const message = $('#composeMessage').val().trim();
            
            if (!subject || !message) {
                Swal.fire('Error', 'Please fill in all fields', 'error');
                return;
            }
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
            
            $.ajax({
                url: 'registrar/send_message.php',
                method: 'POST',
                data: {
                    action: 'send_reply',
                    parent_message_id: 0,
                    subject: subject,
                    message_body: message
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', 'Message sent successfully!', 'success');
                        $('#composeModal').modal('hide');
                        $('#composeSubject').val('');
                        $('#composeMessage').val('');
                        loadInboxMessages();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to send message', 'error');
                },
                complete: function() {
                    $('#sendMessageBtn').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Send Message');
                }
            });
        });
        
        // Reply button
        $('#replyBtn').click(function() {
            $('#messageDetailModal').modal('hide');
            $('#composeModal').modal('show');
            $('#composeSubject').val('Re: ' + $('#detailSubject').text());
        });
        
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
        
        function showError(message) {
            Swal.fire('Error', message, 'error');
        }
    </script>
</body>
</html>
