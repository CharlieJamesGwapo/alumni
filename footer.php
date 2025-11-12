<footer class="footer-moist py-5">
    <div class="container">
        <div class="row text-center text-md-left">
            <!-- Social Media Links -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="footer-title">Connect With Us</h5>
                <div class="footer-social d-flex justify-content-center justify-content-md-start flex-wrap">
                    <a href="https://facebook.com/" target="_blank" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://youtube.com/" target="_blank" class="footer-social-link"><i class="fab fa-youtube"></i></a>
                    <a href="https://linkedin.com/" target="_blank" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://instagram.com/" target="_blank" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                </div>
                <div class="footer-contact mt-3">
                    <div>
                        <i class="fas fa-phone-alt mr-1"></i>
                        <?php echo isset($_SESSION['system']['contact']) ? htmlspecialchars($_SESSION['system']['contact']) : "0912-345-6789"; ?>
                    </div>
                    <div>
                        <i class="fas fa-envelope mr-1"></i>
                        <a href="mailto:<?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : "info@moist.edu.ph"; ?>" class="footer-link">
                            <?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : "info@moist.edu.ph"; ?>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Quick Links -->
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="about.php">About MOIST Alumni</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="directory.php">Alumni Directory</a></li>
                    <li><a href="help.php">Help Center</a></li>
                    <li><a href="https://moist.edu.ph/" target="_blank">MOIST Main Site</a></li>
                </ul>
            </div>
            <!-- Feedback & Legal -->
            <div class="col-md-4">
                <h5 class="footer-title">Feedback & Legal</h5>
                <ul class="footer-links">
                    <!-- Changed this link to open modal -->
                    <li><a href="#" data-toggle="modal" data-target="#feedbackModal">Send Feedback</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Use</a></li>
                    <li><a href="admin_profile.php">Admin Profile</a></li>
                </ul>
                <div class="footer-powered mt-3">
                    Powered by <a href="https://www.facebook.com/Retrigadz/" target="_blank">BboyMaster</a>
                </div>
            </div>
        </div>
        <hr class="footer-divider my-4">
        <div class="row">
            <div class="col-12 text-center small">
                &copy; <?php echo date('Y'); ?> <?php echo isset($_SESSION['system']['name']) ? htmlspecialchars($_SESSION['system']['name']) : "MOIST Alumni Portal"; ?>. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Send Feedback</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="submit_feedback.php" method="POST">
          <div class="form-group">
            <label for="feedbackName">Your Name</label>
            <input type="text" class="form-control" id="feedbackName" name="name" required>
          </div>
          <div class="form-group">
            <label for="feedbackEmail">Your Email</label>
            <input type="email" class="form-control" id="feedbackEmail" name="email" required>
          </div>
          <div class="form-group">
            <label for="feedbackMessage">Message</label>
            <textarea class="form-control" id="feedbackMessage" rows="4" name="message" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.footer-moist {
    background: #ffffff;
    color: #333333;
    font-size: 1.06rem;
    box-shadow: 0 -2px 16px rgba(0,0,0,0.05);
    letter-spacing: 0.3px;
    border-top: 1px solid #eee;
}
.footer-title {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: 0.7px;
    margin-bottom: 1rem;
    color: #333333;
}
.footer-social {
    gap: 12px;
}
.footer-social-link {
    display: inline-block;
    background: #f8f9fa;
    color: #333333;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 1.27rem;
    line-height: 36px;
    text-align: center;
    margin-right: 7px;
    margin-bottom: 3px;
    border: 2px solid #f8f9fa;
    transition: all 0.3s ease;
}
.footer-social-link:hover, .footer-social-link:focus {
    background: #e9ecef;
    color: #000000;
    border: 2px solid #e9ecef;
    text-decoration: none;
    transform: translateY(-2px);
}
.footer-contact {
    margin-top: 10px;
    font-size: 1rem;
    color: #555555;
}
.footer-contact a.footer-link {
    color: #333333;
    text-decoration: none;
    word-break: break-all;
    transition: color 0.3s ease;
}
.footer-contact a.footer-link:hover {
    color: #000000;
    text-decoration: underline;
}
.footer-links {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
}
.footer-links li {
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    padding-bottom: 5px;
}
.footer-links li:last-child {
    border-bottom: none;
    margin-bottom: 0;
}
.footer-links a {
    color: #555555;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}
.footer-links a:hover, .footer-links a:focus {
    color: #000000;
    text-decoration: none;
    padding-left: 5px;
}
.footer-divider {
    border-top: 1px solid #eee;
    margin: 2rem auto 1rem auto;
    max-width: 900px;
}
.footer-powered {
    font-size: 0.97rem;
    color: #666666;
}
.footer-powered a {
    color: #333333;
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-powered a:hover {
    color: #000000;
    text-decoration: underline;
}
@media (max-width: 991.98px) {
    .footer-title, .footer-links, .footer-contact, .footer-powered {
        text-align: center;
    }
    .footer-social {
        justify-content: center !important;
    }
    .footer-links {
        margin-bottom: 1.2rem;
    }
}
@media (max-width: 600px) {
    .footer-title { font-size: 1.09rem; }
    .footer-moist { font-size: 0.97rem; }
    .footer-social-link { width: 30px; height: 30px; font-size: 1.1rem; line-height: 30px; }
}
</style>

<!-- Site-wide loading overlay -->
<div id="siteLoader" aria-hidden="false" role="status" aria-live="polite">
    <div class="site-loader-backdrop"></div>
    <div class="site-loader-inner" role="progressbar" aria-busy="true">
        <img src="assets/img/logo.png" alt="MOIST Logo" class="site-loader-logo">
        <div class="site-loader-spinner" aria-hidden="true"></div>
        <div class="site-loader-text">Loading…</div>
    </div>
</div>

<style>
/* Loader styles - responsive and minimal */
#siteLoader{position:fixed;inset:0;z-index:2147483646;display:flex;align-items:center;justify-content:center;pointer-events:auto}
#siteLoader[aria-hidden="true"]{opacity:0;visibility:hidden;pointer-events:none;transition:opacity .35s ease, visibility .35s ease}
.site-loader-backdrop{position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,1),rgba(248,249,250,1));}
.site-loader-inner{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;gap:12px;padding:18px;border-radius:12px}
.site-loader-logo{width:84px;height:84px;object-fit:contain;border-radius:12px;box-shadow:0 8px 30px rgba(2,6,23,0.08);}
.site-loader-spinner{width:48px;height:48px;border-radius:50%;border:4px solid rgba(0,0,0,0.06);border-top-color:var(--primary, #800000);animation:spin 1s linear infinite}
.site-loader-text{font-weight:600;color:#333;font-size:0.98rem}
@keyframes spin{to{transform:rotate(360deg)}}

@media (max-width:480px){
    .site-loader-logo{width:64px;height:64px}
    .site-loader-text{font-size:0.95rem}
    .site-loader-spinner{width:40px;height:40px}
}
</style>

<script>
// Simple SiteLoader API: SiteLoader.show(), SiteLoader.hide()
(function(){
    var loader = document.getElementById('siteLoader');
    if (!loader) return;
    function hide(){
        try{ loader.setAttribute('aria-hidden','true'); loader.querySelector('.site-loader-inner').setAttribute('aria-hidden','true'); }
        catch(e){}
        // remove after transition
        setTimeout(function(){ if(loader && loader.parentNode) loader.parentNode.removeChild(loader); }, 600);
    }
    function show(){ if(loader) { loader.setAttribute('aria-hidden','false'); }}
    // expose globally
    window.SiteLoader = { hide: hide, show: show };
    // auto hide when window fully loaded
    if (document.readyState === 'complete') { hide(); }
    else { window.addEventListener('load', hide); setTimeout(function(){ if(document.readyState!=='complete') hide(); }, 8000); }
})();
</script>
