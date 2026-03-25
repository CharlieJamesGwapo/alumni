<?php
/**
 * Alumni User Activity Tracking
 * Tracks activities performed by alumni users on the main site
 */

include 'admin/db_connect.php';
include 'admin/log_activity.php';

/**
 * Track alumni user activities
 * Call this function whenever an alumni user performs an action
 */
function track_alumni_activity($alumni_id, $action, $details = '', $target_id = null, $target_type = null) {
    // Map common alumni actions
    $action_map = [
        'login' => 'ALUMNI_LOGIN',
        'logout' => 'ALUMNI_LOGOUT', 
        'profile_update' => 'ALUMNI_PROFILE_UPDATE',
        'password_change' => 'ALUMNI_PASSWORD_CHANGE',
        'forum_post' => 'ALUMNI_FORUM_POST',
        'forum_comment' => 'ALUMNI_FORUM_COMMENT',
        'event_join' => 'ALUMNI_EVENT_JOIN',
        'job_post' => 'ALUMNI_JOB_POST',
        'directory_search' => 'ALUMNI_DIRECTORY_SEARCH',
        'profile_view' => 'ALUMNI_PROFILE_VIEW'
    ];
    
    $mapped_action = $action_map[$action] ?? strtoupper($action);
    
    return log_activity($alumni_id, $mapped_action, $details, $target_id, $target_type);
}

/**
 * Quick tracking functions for common alumni activities
 */
function track_alumni_login($alumni_id) {
    return track_alumni_activity($alumni_id, 'login', 'Alumni logged into the system');
}

function track_alumni_logout($alumni_id) {
    return track_alumni_activity($alumni_id, 'logout', 'Alumni logged out of the system');
}

function track_profile_update($alumni_id, $fields_updated = '') {
    $details = $fields_updated ? "Updated profile fields: $fields_updated" : 'Updated profile information';
    return track_alumni_activity($alumni_id, 'profile_update', $details);
}

function track_forum_activity($alumni_id, $action, $forum_id, $title = '') {
    $details = $title ? "Forum $action: $title" : "Forum $action";
    return track_alumni_activity($alumni_id, "forum_$action", $details, $forum_id, 'forum');
}

function track_event_activity($alumni_id, $action, $event_id, $event_title = '') {
    $details = $event_title ? "Event $action: $event_title" : "Event $action";
    return track_alumni_activity($alumni_id, "event_$action", $details, $event_id, 'event');
}

function track_directory_search($alumni_id, $search_terms = '') {
    $details = $search_terms ? "Directory search: $search_terms" : 'Performed directory search';
    return track_alumni_activity($alumni_id, 'directory_search', $details);
}

function track_profile_view($alumni_id, $viewed_alumni_id) {
    return track_alumni_activity($alumni_id, 'profile_view', "Viewed profile of alumni ID: $viewed_alumni_id", $viewed_alumni_id, 'alumni');
}
?>
