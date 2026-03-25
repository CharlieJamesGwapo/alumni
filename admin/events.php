<?php
include('db_connect.php');

// Permission checks
$can_manage = isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], [1, 4]);
$is_super_admin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;

// Fetch events
$events = $conn->query("SELECT e.*,
    (SELECT COUNT(*) FROM event_commits WHERE event_id = e.id) as commit_count,
    (SELECT COUNT(*) FROM event_likes WHERE event_id = e.id) as like_count
    FROM events e ORDER BY schedule DESC");
$events_arr = [];
if($events) {
    while($r = $events->fetch_assoc()) $events_arr[] = $r;
}
$total_events = count($events_arr);
$initial_show = 12;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="mb-0">Events Management</h4>
                    <a class="btn btn-primary" href="index.php?page=manage_event">
                        <i class="fa fa-plus-circle"></i> Create Event
                    </a>
                </div>

                <div class="card-body p-0">
                    <!-- Filters -->
                    <div class="p-3 border-bottom" style="border-color: #e2e8f0 !important;">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" id="searchEvents" class="form-control" placeholder="Search events...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="all">All Events (<?php echo $total_events; ?>)</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if($total_events == 0): ?>
                    <div class="text-center p-5">
                        <i class="fas fa-calendar-plus" style="font-size:3rem; color:#cbd5e1;"></i>
                        <p class="mt-3 text-muted">No events yet. Create your first event!</p>
                        <a href="index.php?page=manage_event" class="btn btn-primary"><i class="fa fa-plus"></i> Create Event</a>
                    </div>
                    <?php else: ?>
                    <!-- Events Grid -->
                    <div class="event-grid p-3">
                        <?php foreach($events_arr as $i => $row):
                            $is_upcoming = strtotime($row['schedule']) > time();
                            $banner_file = !empty($row['banner']) ? '../uploads/'.$row['banner'] : '';
                            $banner = ($banner_file && file_exists($banner_file)) ? $banner_file : '';
                            $approved = isset($row['approved']) ? (int)$row['approved'] : 0;
                            $extra_class = ($i+1) > $initial_show ? 'd-none extra-event' : '';
                        ?>
                        <div class="event-card <?php echo $extra_class; ?>" data-status="<?php echo $is_upcoming ? 'upcoming' : 'past'; ?>" data-approved="<?php echo $approved; ?>" data-id="<?php echo $row['id']; ?>">
                            <div class="event-banner position-relative">
                                <?php if($banner): ?>
                                <img src="<?php echo htmlspecialchars($banner); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php else: ?>
                                <div class="event-banner-placeholder">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <?php endif; ?>
                                <div class="event-date">
                                    <span class="date"><?php echo date("M d, Y", strtotime($row['schedule'])); ?></span>
                                    <span class="time"><?php echo date("h:i A", strtotime($row['schedule'])); ?></span>
                                </div>
                                <span class="approve-badge badge position-absolute" style="top:12px; right:12px; background:<?php echo $approved ? '#059669' : '#dc2626'; ?>; color:#fff; padding:5px 10px; border-radius:6px; font-size:0.75rem;">
                                    <?php echo $approved ? 'Approved' : 'Pending'; ?>
                                </span>
                                <?php if($is_upcoming): ?>
                                <span class="badge position-absolute" style="bottom:12px; left:12px; background:#4f46e5; color:#fff; padding:4px 8px; border-radius:5px; font-size:0.7rem;">
                                    Upcoming
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="event-content">
                                <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="text-muted mb-2">
                                    <?php
                                    $desc = strip_tags(html_entity_decode($row['content']));
                                    echo strlen($desc) > 120 ? substr($desc, 0, 120).'...' : $desc;
                                    ?>
                                </p>
                                <div class="event-meta mb-2">
                                    <span><i class="fas fa-users"></i> <?php echo $row['commit_count']; ?> attending</span>
                                    <span><i class="fas fa-heart"></i> <?php echo $row['like_count']; ?> likes</span>
                                </div>
                                <?php if($can_manage): ?>
                                <div class="event-card-actions d-flex gap-2 mt-2 pt-2">
                                    <?php if($is_super_admin): ?>
                                    <button class="btn btn-sm <?php echo $approved ? 'btn-outline-warning' : 'btn-outline-success'; ?> toggle-approve" data-id="<?php echo $row['id']; ?>" data-approved="<?php echo $approved; ?>" title="<?php echo $approved ? 'Unapprove' : 'Approve'; ?>">
                                        <i class="fas <?php echo $approved ? 'fa-ban' : 'fa-check'; ?>"></i>
                                    </button>
                                    <?php endif; ?>
                                    <a href="index.php?page=manage_event&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger flex-fill delete-event" data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($total_events > $initial_show): ?>
                    <div class="text-center p-3">
                        <button id="loadMoreEvents" class="btn btn-outline-primary">Load more events</button>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.event-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}
.event-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.event-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.event-banner { position: relative; height: 180px; }
.event-banner img { width: 100%; height: 100%; object-fit: cover; }
.event-banner-placeholder {
    width: 100%; height: 180px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed, #9333ea);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.5); font-size: 3rem;
}
.event-date {
    position: absolute; top: 12px; left: 12px;
    background: rgba(255,255,255,0.95); padding: 8px 14px;
    border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.event-date .date { display: block; font-weight: 600; color: #1e293b; font-size: 0.88rem; }
.event-date .time { font-size: 0.78rem; color: #64748b; }
.event-content { padding: 16px 20px; }
.event-content h5 { margin-bottom: 8px; color: #1e293b; font-weight: 600; font-size: 1.05rem; }
.event-content p { color: #64748b; font-size: 0.85rem; line-height: 1.5; }
.event-meta { display: flex; gap: 15px; color: #94a3b8; font-size: 0.85rem; }
.event-meta i { margin-right: 4px; }
.event-card-actions { border-top: 1px solid #f1f5f9 !important; }
.event-card-actions .btn { font-size: 0.82rem; }
@media (max-width: 768px) {
    .event-grid { grid-template-columns: 1fr; gap: 16px; }
    .event-banner, .event-banner-placeholder { height: 160px; }
}
</style>

<script>
$(document).ready(function(){
    // Search
    $('#searchEvents').on('input', function() {
        var search = $(this).val().toLowerCase();
        $('.event-card').each(function() {
            var title = $(this).find('h5').text().toLowerCase();
            var desc = $(this).find('p').text().toLowerCase();
            $(this).toggle(title.indexOf(search) > -1 || desc.indexOf(search) > -1);
        });
    });

    // Filter
    $('#filterStatus').on('change', function() {
        var status = $(this).val();
        if(status === 'all') {
            $('.event-card').show();
        } else {
            $('.event-card').hide();
            $('.event-card[data-status="' + status + '"]').show();
        }
    });

    // Delete event
    $(document).on('click', '.delete-event', function(){
        var id = $(this).data('id');
        var title = $(this).data('title') || $(this).closest('.event-card').find('h5').text();

        if(typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Event?',
                html: 'Are you sure you want to delete <strong>' + title + '</strong>?<br>This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if(result.isConfirmed) doDelete(id);
            });
        } else {
            if(confirm('Delete "' + title + '"? This cannot be undone.')) doDelete(id);
        }
    });

    function doDelete(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_event',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                end_load();
                resp = (resp+'').trim();
                if(resp == '1') {
                    alert_toast('Event deleted successfully', 'success');
                    setTimeout(function(){ location.reload(); }, 600);
                } else {
                    alert_toast('Could not delete event.', 'danger');
                }
            },
            error: function(){ end_load(); alert_toast('Server error.', 'danger'); }
        });
    }

    // Approve/unapprove
    $(document).on('click', '.toggle-approve', function(){
        var btn = $(this);
        var id = btn.data('id');
        var current = parseInt(btn.attr('data-approved')) || 0;
        var newVal = current ? 0 : 1;
        btn.prop('disabled', true);
        $.ajax({
            url: 'ajax.php?action=approve_event',
            method: 'POST',
            data: {id: id, approved: newVal},
            success: function(resp){
                btn.prop('disabled', false);
                resp = (resp+'').trim();
                if(resp == '1'){
                    alert_toast('Event ' + (newVal ? 'approved' : 'set to pending'), newVal ? 'success' : 'warning');
                    setTimeout(function(){ location.reload(); }, 600);
                } else {
                    alert_toast('Could not update status.', 'danger');
                }
            },
            error: function(){ btn.prop('disabled', false); alert_toast('Server error.', 'danger'); }
        });
    });

    // Load more
    $('#loadMoreEvents').on('click', function(){
        var $extras = $('.extra-event.d-none');
        if($extras.length){
            $extras.slice(0, 6).removeClass('d-none');
            if($('.extra-event.d-none').length === 0) $(this).hide();
        } else { $(this).hide(); }
    });
});
</script>
