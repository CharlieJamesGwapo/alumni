<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg rounded-3 border-0">
      <div class="modal-header bg-maroon text-white">
        <h5 class="modal-title fw-bold" id="feedbackModalLabel">
          <i class="fas fa-comment-dots me-2"></i> Share Your Feedback
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="feedbackForm" method="POST" action="submit_feedback.php">
        <div class="modal-body p-4">
          <p class="text-muted small mb-3">
            We’d love to hear from you! Your feedback helps improve the MOIST Alumni Portal.
          </p>
          <div class="mb-3">
            <label for="fb-name" class="form-label">Your Name</label>
            <input type="text" name="name" id="fb-name" class="form-control form-control-lg" placeholder="Enter your name" required>
          </div>
          <div class="mb-3">
            <label for="fb-email" class="form-label">Your Email</label>
            <input type="email" name="email" id="fb-email" class="form-control form-control-lg" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label for="fb-message" class="form-label">Message</label>
            <textarea name="message" id="fb-message" rows="4" class="form-control form-control-lg" placeholder="Type your feedback here..." required></textarea>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-maroon px-4">
            <i class="fas fa-paper-plane me-2"></i> Send Feedback
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom Styles -->
<style>
  .bg-maroon { background-color: #800000 !important; }
  .btn-maroon {
    background-color: #800000;
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s ease;
  }
  .btn-maroon:hover {
    background-color: #a00000;
    color: #fff;
  }
  #feedbackModal .modal-content {
    border-radius: 12px;
    overflow: hidden;
  }
  #feedbackModal .form-control {
    border-radius: 8px;
    font-size: 1rem;
  }
  #feedbackModal textarea {
    resize: none;
  }
</style>

<!-- Trigger link (replace old Send Feedback link) -->
<li><a href="#" data-bs-toggle="modal" data-bs-target="#feedbackModal">Send Feedback</a></li>
