<?php
/**
 * Render contact block (two cards): Contact (info + AJAX form) and Location (map + modal)
 * Usage: include 'includes/contact.php'; render_contact();
 */
function render_contact(){
    ?>
    <div class="mb-3">
        <div class="card p-3" style="border-radius:12px;overflow:hidden;">
            <div class="row no-gutters">
                <div class="col-md-6 p-3" style="background:linear-gradient(135deg, rgba(128,0,0,0.95), rgba(183,28,28,0.95));color:#fff;">
                    <h5 style="color:#ffd700;font-weight:800;margin-bottom:0.5rem;">Contact Admissions</h5>
                    <p style="margin-bottom:0.25rem;">Sta. Cruz, Cogon, Balingasag, Misamis Oriental</p>
                    <p style="margin-bottom:0.25rem;"><strong>Phone:</strong> <a href="tel:+0888552885" style="color:#fff;">PDLT: (088)-855-2885</a></p>
                    <p style="margin-bottom:0.5rem;"><strong>Email:</strong> <a href="mailto:moist@moist.edu.ph" style="color:#ffd700;">moist@moist.edu.ph</a></p>
                    <hr style="border-color:rgba(255,255,255,0.08);">

                    <!-- Simple AJAX contact form -->
                    <form id="admissionContactForm" novalidate>
                        <div class="form-group">
                            <label for="c_name" class="sr-only">Full name</label>
                            <input type="text" id="c_name" name="name" class="form-control" placeholder="Full name" required style="border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label for="c_email" class="sr-only">Email address</label>
                            <input type="email" id="c_email" name="email" class="form-control" placeholder="Email address" required style="border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label for="c_phone" class="sr-only">Phone</label>
                            <input type="tel" id="c_phone" name="phone" class="form-control" placeholder="Phone (optional)" style="border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label for="c_message" class="sr-only">Message</label>
                            <textarea id="c_message" name="message" class="form-control" rows="4" placeholder="Your message / enquiry" required style="border-radius:8px;"></textarea>
                        </div>
                        <div id="contactFormMsg" class="mb-2" role="status" aria-live="polite"></div>
                        <button type="submit" class="btn btn-sm" style="background:#ffd700;color:#800;margin-right:8px;border-radius:8px;font-weight:700;">Send Message</button>
                    </form>
                </div>

                <div class="col-md-6 p-3 d-flex flex-column">
                    <div style="flex:1 1 auto; border-radius:8px; overflow:hidden;">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15734.96407396413!2d124.761493!3d8.647347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32ffe20209d4c4c9:0xd59407e48f0872e4!2sMisamis%20Oriental%20Institute%20of%20Science%20and%20Technology!5e0!3m2!1sen!2sph!4v1692100000000!5m2!1sen!2sph" width="100%" height="100%" style="min-height:220px;border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="MOIST Location Map"></iframe>
                    </div>
                    <div class="mt-3 text-right">
                        <button class="btn btn-moist" id="openMapModal">View Larger Map</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal with larger map -->
        <div class="modal fade" id="admissionMapModal" tabindex="-1" role="dialog" aria-labelledby="admissionMapModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius:12px;overflow:hidden;">
                    <div class="modal-header" style="background:#800000;color:#fff;border-bottom:0;">
                        <h5 class="modal-title" id="admissionMapModalLabel">MOIST Location</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;font-size:1.6rem;opacity:1;">&times;</button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15734.96407396413!2d124.761493!3d8.647347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32ffe20209d4c4c9:0xd59407e48f0872e4!2sMisamis%20Oriental%20Institute%20of%20Science%20and%20Technology!5e0!3m2!1sen!2sph!4v1692100000000!5m2!1sen!2sph" width="100%" height="520" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="MOIST Location Map"></iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    (function(){
        // Open modal
        var openBtn = document.getElementById('openMapModal');
        if (openBtn) openBtn.addEventListener('click', function(e){ e.preventDefault(); $('#admissionMapModal').modal('show'); });

        // AJAX contact form
        var form = document.getElementById('admissionContactForm');
        var msg = document.getElementById('contactFormMsg');
        if (form){
            form.addEventListener('submit', function(e){
                e.preventDefault();
                msg.innerHTML = '';
                var name = document.getElementById('c_name').value.trim();
                var email = document.getElementById('c_email').value.trim();
                var phone = document.getElementById('c_phone').value.trim();
                var message = document.getElementById('c_message').value.trim();

                if (!name || !email || !message){
                    msg.innerHTML = '<div class="alert alert-warning">Please fill in Name, Email and Message.</div>';
                    return;
                }

                var data = new FormData();
                data.append('name', name);
                data.append('email', email);
                data.append('phone', phone);
                data.append('message', message);

                var submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn){ submitBtn.disabled = true; submitBtn.textContent = 'Sending...'; }

                fetch('contact_submit.php', { method: 'POST', body: data })
                    .then(function(r){ return r.json(); })
                    .then(function(j){
                        if (j && j.success){
                            msg.innerHTML = '<div class="alert alert-success">' + (j.message || 'Message sent. We will get back to you.') + '</div>';
                            form.reset();
                        } else {
                            msg.innerHTML = '<div class="alert alert-danger">' + (j.message || 'Failed to send message. Please try again later.') + '</div>';
                        }
                    })
                    .catch(function(){ msg.innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>'; })
                    .finally(function(){ if (submitBtn){ submitBtn.disabled = false; submitBtn.textContent = 'Send Message'; } });
            });
        }
    })();
    </script>
    <?php
}

?>
