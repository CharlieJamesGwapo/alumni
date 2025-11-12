<?php 
include 'admin/db_connect.php'; 
?>
<style>
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.gallery-list{
cursor: pointer;
border: unset;
flex-direction: inherit;
}
.gallery-img,.gallery-list .card-body {
    width: calc(100%)
}
.gallery-img img{
    border-radius: 5px;
    min-height: 50vh;
    max-width: calc(100%);
}
span.hightlight{
    background: yellow;
}
.carousel,.carousel-inner,.carousel-item{
   min-height: calc(100%)
}
header.masthead,header.masthead:before {
        min-height: 50vh !important;
        height: 50vh !important
    }
.row-items{
    position: relative;
}
.card-left{
    left:0;
}
.card-right{
    right:0;
}
.rtl{
    direction: rtl ;
}
.gallery-text{
    justify-content: center;
    align-items: center ;
}
.masthead{
        min-height: 23vh !important;
        height: 23vh !important;
    }
     .masthead:before{
        min-height: 23vh !important;
        height: 23vh !important;
    }

</style>
        <div class="container-fluid h-100 position-relative">
    <!-- Animated Background -->
    <canvas id="netCanvas"></canvas>
    
    <!-- Content -->
    <div class="row h-100 align-items-center justify-content-center text-center position-absolute w-100">
        <div class="col-lg-8 d-flex flex-column align-items-center justify-content-center text-center vh-100">
            <h3 class="text-white">Gallery</h3>
            <hr class="divider my-4" />
        </div>
    </div>
</div>

<!-- CSS for Styling -->
<style>
    /* Fullscreen Background */
    #netCanvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: radial-gradient(circle, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 1));
    }

    /* Center Content */
    .vh-100 {
        height: 100vh;
    }
</style>

<!-- JavaScript for Animated Net Pattern with Color Changes -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("netCanvas");
    const ctx = canvas.getContext("2d");
    
    let w, h;
    const resizeCanvas = () => {
        canvas.width = w = window.innerWidth;
        canvas.height = h = window.innerHeight;
    };
    
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();
    
    // Particle Settings
    const particles = [];
    const particleCount = 100;
    let hue = 0; // Hue for color shifting
    
    class Particle {
        constructor() {
            this.x = Math.random() * w;
            this.y = Math.random() * h;
            this.vx = (Math.random() - 0.5) * 1;
            this.vy = (Math.random() - 0.5) * 1;
            this.radius = Math.random() * 2 + 1;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            if (this.x < 0 || this.x > w) this.vx *= -1;
            if (this.y < 0 || this.y > h) this.vy *= -1;
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = `hsla(${hue}, 100%, 70%, 0.6)`; // Dynamic color
            ctx.fill();
        }
    }

    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    const drawLines = () => {
        ctx.strokeStyle = `hsla(${hue}, 100%, 50%, 0.2)`; // Dynamic color
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                
                if (dist < 100) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
    };

    const animate = () => {
        ctx.clearRect(0, 0, w, h);
        
        particles.forEach((particle) => {
            particle.update();
            particle.draw();
        });

        drawLines();

        hue += 1; // Change hue value for color shift
        if (hue > 360) hue = 0; // Reset to avoid overflow

        requestAnimationFrame(animate);
    };

    animate();
});
</script>
            <div class="container mt-4 pt-2">
    <h4 class="text-center text-maroon">Gallery</h4>
    <hr class="divider">

    <div class="gallery-container">
        <?php
        $fpath = 'admin/assets/uploads/gallery';
        $files = is_dir($fpath) ? scandir($fpath) : array();
        $img = array();
        
        // Map images by gallery ID
        foreach ($files as $val) {
            if (!in_array($val, array('.', '..'))) {
                $n = explode('_', $val);
                $img[$n[0]] = $val;
            }
        }

        $gallery = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
        while ($row = $gallery->fetch_assoc()):
        ?>
        
        <div class="gallery-item">
            <div class="gallery-img">
                <img src="<?php echo isset($img[$row['id']]) && is_file($fpath.'/'.$img[$row['id']]) ? $fpath.'/'.$img[$row['id']] : 'default-placeholder.jpg' ?>" alt="Gallery Image">
            </div>
            <div class="gallery-caption">
                <p><?php echo ucwords($row['about']) ?></p>
            </div>
        </div>

        <?php endwhile; ?>
    </div>
</div>

<!-- CSS for Simple Styling -->
<style>
    /* Centered Gallery Container */
    .gallery-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    /* Gallery Item */
    .gallery-item {
        width: 100%;
        max-width: 600px; /* Limits width for a clean look */
        overflow: hidden;
/*        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);*/
        transition: transform 0.3s ease-in-out;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
/*        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);*/
    }

    /* Image Styling */
    .gallery-img img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Caption Styling */
    .gallery-caption {
        background: #fff;
        padding: 10px;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
    }
</style>



<script>
    // $('.card.gallery-list').click(function(){
    //     location.href = "index.php?page=view_gallery&id="+$(this).attr('data-id')
    // })
    $('.book-gallery').click(function(){
        uni_modal("Submit Booking Request","booking.php?gallery_id="+$(this).attr('data-id'))
    })
    $('.gallery-img img').click(function(){
        viewer_modal($(this).attr('src'))
    })

</script>