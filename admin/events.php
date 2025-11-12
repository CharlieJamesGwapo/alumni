<?php include('db_connect.php');?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Events Timeline</h4>
                    <a class="btn btn-primary" href="index.php?page=manage_event">
                        <i class="fa fa-plus-circle"></i> Create Event
                    </a>
                </div>

                <div class="card-body p-0">
                    <!-- Event Filter -->
                    <div class="p-3 border-bottom">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" id="searchEvents" class="form-control" placeholder="Search events...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="all">All Events</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Events Grid -->
                    <div class="event-grid p-3">
                        <?php 
                        $events = $conn->query("SELECT e.*, 
                            (SELECT COUNT(*) FROM event_commits WHERE event_id = e.id) as commit_count,
                            (SELECT COUNT(*) FROM event_likes WHERE event_id = e.id) as like_count
                            FROM events e ORDER BY schedule DESC");
                        $events_arr = [];
                        while($r = $events->fetch_assoc()) $events_arr[] = $r;
                        $total_events = count($events_arr);
                        $initial_show = 12; // how many to show initially in admin
                        foreach($events_arr as $i => $row):
                            $is_upcoming = strtotime($row['schedule']) > time();
                            $banner = !empty($row['banner']) ? '../uploads/'.$row['banner'] : 'assets/img/default-event.jpg';
                            $approved = isset($row['approved']) ? (int)$row['approved'] : 0;
                            $extra_class = ($i+1) > $initial_show ? 'd-none extra-event' : '';
                            // Permission checks: admin or owner
                            $is_admin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;
                            $is_owner = isset($_SESSION['login_id']) && isset($row['user_id']) && $_SESSION['login_id'] == $row['user_id'];
                            $manage_class = ($is_admin || $is_owner) ? ' can-manage' : '';
                        ?>
                        <div class="event-card <?php echo $extra_class . $manage_class; ?>" data-status="<?php echo $is_upcoming ? 'upcoming' : 'past'; ?>" data-approved="<?php echo $approved; ?>">
                            <div class="event-banner position-relative">
                                <img src="<?php echo $banner; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <div class="event-date">
                                    <span class="date"><?php echo date("M d, Y", strtotime($row['schedule'])); ?></span>
                                    <span class="time"><?php echo date("h:i A", strtotime($row['schedule'])); ?></span>
                                </div>
                                <div class="event-actions">
                                    <?php if($is_admin || $is_owner): ?>
                                    <button class="btn btn-light btn-sm me-2 edit-event" data-id="<?php echo $row['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-event" data-id="<?php echo $row['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php if($is_admin): ?>
                                    <button class="btn btn-sm btn-outline-secondary toggle-approve" data-id="<?php echo $row['id']; ?>" data-approved="<?php echo $approved ? 1 : 0; ?>" title="Toggle approve">
                                        <?php if($approved): ?>
                                        <i class="fas fa-times"></i>
                                        <?php else: ?>
                                        <i class="fas fa-check"></i>
                                        <?php endif; ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <div class="approve-badge badge <?php echo $approved ? 'badge-success' : 'badge-danger'; ?> position-absolute" style="top:15px; right:15px; background: <?php echo $approved ? '#28a745' : '#dc3545'; ?>; color: #fff; padding:6px 10px; border-radius:6px;">
                                    <?php echo $approved ? 'Approved' : 'Pending'; ?>
                                </div>
                            </div>
                            <div class="event-content">
                                <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="text-muted mb-2">
                                    <?php 
                                    $desc = strip_tags(html_entity_decode($row['content']));
                                    echo strlen($desc) > 150 ? substr($desc, 0, 150).'...' : $desc;
                                    ?>
                                </p>
                                <div class="event-meta">
                                    <span><i class="fas fa-users"></i> <?php echo $row['commit_count']; ?> attending</span>
                                    <span><i class="fas fa-heart"></i> <?php echo $row['like_count']; ?> likes</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($total_events > $initial_show): ?>
                    <div class="text-center p-3">
                        <button id="loadMoreEvents" class="btn btn-outline-primary">Load more events</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Event Cards Style */
.event-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.event-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
}

.event-banner {
    position: relative;
    height: 200px;
}

.event-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.event-date {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(255,255,255,0.95);
    padding: 8px 15px;
    border-radius: 8px;
}

.event-date .date {
    display: block;
    font-weight: 600;
    color: #333;
}

.event-date .time {
    font-size: 0.9em;
    color: #666;
}

.event-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.event-card:hover .event-actions {
    opacity: 1;
}

.event-content {
    padding: 20px;
}

.event-content h5 {
    margin-bottom: 10px;
    color: #333;
    font-weight: 600;
}

.event-meta {
    display: flex;
    gap: 15px;
    color: #666;
    font-size: 0.9em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .event-grid {
        grid-template-columns: 1fr;
    }
    
    .event-banner {
        height: 180px;
    }
}

/* Animation for new events */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.event-card {
    animation: fadeIn 0.5s ease-out forwards;
}
</style>

<script>
$(document).ready(function(){
    // Search functionality
    $('#searchEvents').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('.event-card').each(function() {
            const title = $(this).find('h5').text().toLowerCase();
            const desc = $(this).find('p').text().toLowerCase();
            $(this).toggle(title.includes(search) || desc.includes(search));
        });
    });

    // Filter functionality
    $('#filterStatus').on('change', function() {
        const status = $(this).val();
        if(status === 'all') {
            $('.event-card').show();
        } else {
            $('.event-card').hide();
            $('.event-card[data-status="' + status + '"]').show();
        }
    });

    // Edit event
    $('.edit-event').click(function(){
        location.href = "index.php?page=manage_event&id=" + $(this).data('id');
    });

    // Delete event with confirmation
    $('.delete-event').click(function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Event?',
            text: "This action cannot be undone",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteEvent(id);
            }
        });
    });

    function deleteEvent(id) {
        $.ajax({
            url: 'ajax.php?action=delete_event',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if(resp == 1) {
                    Swal.fire(
                        'Deleted!',
                        'Event has been deleted.',
                        'success'
                    ).then(() => {
                        // remove the card from DOM
                        $('.event-card').filter(function(){
                            return $(this).find('.delete-event').data('id') == id;
                        }).fadeOut(300, function(){ $(this).remove(); });
                    });
                } else if(resp == 0) {
                    Swal.fire('Permission denied', 'You are not allowed to delete this event.', 'error');
                } else {
                    Swal.fire('Error', 'Could not delete event.', 'error');
                }
            },
            error: function(){
                Swal.fire('Error', 'Server error.', 'error');
            }
        });
    }

    // Approve/unapprove toggle
    $(document).on('click', '.toggle-approve', function(){
        const btn = $(this);
        const id = btn.data('id');
        const current = parseInt(btn.attr('data-approved')) || 0;
        const newVal = current ? 0 : 1;
        btn.prop('disabled', true);
        $.ajax({
            url: 'ajax.php?action=approve_event',
            method: 'POST',
            data: {id: id, approved: newVal},
            success: function(resp){
                btn.prop('disabled', false);
                if(resp == 1){
                    btn.attr('data-approved', newVal);
                    btn.data('approved', newVal);
                    const card = btn.closest('.event-card');
                    card.attr('data-approved', newVal);
                    const badge = card.find('.approve-badge');
                    if(newVal){
                        badge.removeClass('badge-danger').addClass('badge-success').css('background', '#28a745').text('Approved');
                        btn.html('<i class="fas fa-times"></i>');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger').css('background', '#dc3545').text('Pending');
                        btn.html('<i class="fas fa-check"></i>');
                    }
                    if(typeof Toastify !== 'undefined'){
                        Toastify({text: 'Event ' + (newVal ? 'approved' : 'marked pending'), duration: 3000, gravity:'top', position:'right', backgroundColor: newVal? '#28a745':'#dc3545'}).showToast();
                    } else {
                        Swal.fire('Updated', 'Approval status updated.', 'success');
                    }
                } else {
                    Swal.fire('Error', 'Could not update approval status.', 'error');
                }
            },
            error: function(){
                btn.prop('disabled', false);
                Swal.fire('Error', 'Server error.', 'error');
            }
        });
    });

    // Load more events
    $('#loadMoreEvents').on('click', function(){
        const $extras = $('.extra-event.d-none');
        const per = 6;
        if($extras.length){
            $extras.slice(0, per).removeClass('d-none');
            if($('.extra-event.d-none').length === 0){
                $(this).hide();
            }
            // smooth scroll to newly revealed
            $('html,body').animate({scrollTop: $(this).offset().top - 100}, 400);
        } else {
            $(this).hide();
        }
    });

    // Hover effects for better UX
    $('.event-card').hover(
        function() {
            $(this).find('.event-actions').css('opacity', '1');
        },
        function() {
            $(this).find('.event-actions').css('opacity', '0');
        }
    );
});
</script>