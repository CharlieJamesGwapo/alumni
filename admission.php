<?php
    session_start();
    include('header.php');
    include 'includes/hero.php';
    // Render consistent hero for Admission page
    render_hero([
        'title' => 'Admission',
        'subtitle' => 'College, Basic Education, and TVET admission requirements and instructions.',
        'bg' => 'assets/img/moist12.jpg',
        'cta_url' => 'admission.php',
        'cta_text' => 'Apply Now',
    ]);
?>
<a href="index.php" class="back-to-home">
    <i class="fas fa-home"></i> Back to Home
</a>

<main id="main-content" class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card p-4 mb-4" style="border-radius:12px;">
                <h1 style="color:#800000;font-weight:900;">Admission</h1>
                <p class="lead text-muted">College Admission, Basic Education, and TVET admission requirements and instructions.</p>
                <hr>

                <h3 style="color:#333;margin-top:1rem;">College Admission</h3>
                <h5>For Incoming Freshmen</h5>
                <p>Entrance Examination Required</p>
                <ul>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy)</li>
                    <li>Form 138 Card</li>
                    <li>Certificate of Good Moral</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <h5>For Transferees</h5>
                <p>Entrance Examination Required</p>
                <ul>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy)</li>
                    <li>Transcript of Records (TOR)</li>
                    <li>Honorable Dismissal</li>
                    <li>Certificate of Good Moral</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <h5>For Cross-Enrollees</h5>
                <p>Entrance Examination Required</p>
                <ul>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy)</li>
                    <li>Permit to Study Certification (Current School)</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <hr>
                <h3 style="color:#333;margin-top:1rem;">Basic Education Admission</h3>
                <h5>Pre-School (Nursery K1 & K2)</h5>
                <ul>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy) 2pcs.</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <h5>Elementary (Grade 1 - Grade 6)</h5>
                <ul>
                    <li>Report Card</li>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy) 2pcs.</li>
                    <li>Certificate of Good Moral</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <h5>Junior High School (Grade 7 - Grade 10)</h5>
                <ul>
                    <li>Report Card</li>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy) 2pcs.</li>
                    <li>Cert. of Indigency (Grade 7 only)</li>
                    <li>Certificate of Good Moral</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <h5>Senior High School (Grade 11 - Grade 12)</h5>
                <ul>
                    <li>Report Card</li>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy) 2pcs.</li>
                    <li>Long Brown Envelop (4 pcs.)</li>
                    <li>2x2 Picture (2 pcs)</li>
                    <li>Filled-up Pre-Admission Form</li>
                </ul>

                <hr>
                <h3 style="color:#333;margin-top:1rem;">TVET Admission</h3>
                <p>TVET Training Entry Requirements</p>
                <ul>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy)</li>
                    <li>1x1 Picture (4 pcs)</li>
                    <li>Passport ID Picture (2 pcs)</li>
                    <li>Marriage Certificate (If Married)</li>
                    <li>Certificate of Indigency</li>
                    <li>Diploma / Transcript of Records (TOR)</li>
                </ul>

                <h5>Requirements for Assessment</h5>
                <ol>
                    <li>Application Form</li>
                    <li>Passport ID Picture (2 pcs) - White Background, With Collar, No Nametag, Professional/Studio Quality</li>
                    <li>Self Assessment Guide (SAG) form</li>
                    <li>Training Certificate or Certificate of Employment (COE) indicating experience in the said qualification</li>
                    <li>Photocopy of Authenticated Birth Certificate (PSA Copy)</li>
                </ol>

            </div>
        </div>
        <div class="col-lg-3">
        </div>
    </div>
</main>
<?php include('footer.php'); ?>
