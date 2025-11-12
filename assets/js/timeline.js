$(document).ready(function() {
    // Filter events
    $('.timeline-filters button').click(function() {
        $('.timeline-filters button').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        if(filter === 'all') {
            $('.event-item').show();
        } else {
            $('.event-item').hide();
            $('.event-item.' + filter).show();
        }
    });

    // Join event functionality
    $('.join-event').click(function() {
        const eventId = $(this).data('id');
        const btn = $(this);

        $.ajax({
            url: 'ajax/join_event.php',
            type: 'POST',
            data: {
                event_id: eventId
            },
            success: function(response) {
                if(response.success) {
                    btn.text('Joined')
                       .removeClass('btn-outline-primary')
                       .addClass('btn-success')
                       .prop('disabled', true);
                    
                    // Update attendee count
                    const countEl = btn.closest('.card').find('.event-meta span:first');
                    const currentCount = parseInt(countEl.text());
                    countEl.html(`<i class="fas fa-users"></i> ${currentCount + 1} attending`);
                } else {
                    alert(response.message || 'Failed to join event');
                }
            },
            error: function() {
                alert('Error joining event');
            }
        });
    });
});