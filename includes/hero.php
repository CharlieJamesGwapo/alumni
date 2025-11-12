<?php
/**
 * Render a reusable hero section.
 * Usage: include 'includes/hero.php'; render_hero(['title'=>'Title','subtitle'=>'Subtitle','bg'=>'path','cta_url'=>'','cta_text'=>'']);
 */
function render_hero($opts = []){
    $title = htmlspecialchars($opts['title'] ?? 'MOIST Alumni Portal');
    $subtitle = htmlspecialchars($opts['subtitle'] ?? '');
    $bg = htmlspecialchars($opts['bg'] ?? 'assets/img/moist12.jpg');
    $cta_url = htmlspecialchars($opts['cta_url'] ?? 'signup.php');
    $cta_text = htmlspecialchars($opts['cta_text'] ?? 'Join Now');
    $alt = htmlspecialchars($opts['alt'] ?? 'MOIST Campus');
    ?>
    <section class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(128,0,0,0.55)), url('<?php echo $bg;?>') center center/cover no-repeat fixed; position:relative;">
        <div class="container hero-content text-white" style="padding:4rem 2rem; max-width:1100px;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 style="font-size:2.8rem; font-weight:900; line-height:1.15; margin-bottom:0.6rem;"><?php echo $title; ?></h1>
                    <?php if($subtitle): ?>
                    <p class="lead" style="font-size:1.1rem; color:rgba(255,255,255,0.95); margin-bottom:1rem;"><?php echo $subtitle; ?></p>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo $cta_url;?>" class="btn btn-warning btn-lg" style="background:#ffd700;color:#800000;font-weight:700;border-radius:40px;border:none;min-width:160px;"><?php echo $cta_text;?></a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg" style="color:#fff;border-color:rgba(255,255,255,0.25);border-radius:40px;min-width:160px;">Contact</a>
                    </div>
                </div>
                <div class="col-md-4 text-md-right d-none d-md-block">
                    <img src="assets/img/logo.png" alt="<?php echo $alt;?>" style="width:110px;height:110px;border-radius:50%;box-shadow:0 8px 24px rgba(0,0,0,0.25);object-fit:cover;" />
                </div>
            </div>
        </div>
    </section>
    <?php
}

?>
