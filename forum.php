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
    width: calc(50%)
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
            <h3 class="text-white">Forum List</h3>
            <hr class="divider my-4" />
            <div class="row mb-2 justify-content-center">
                    <button class="btn btn-primary btn-block col-sm-12" type="button" id="new_forum"><i class="fa fa-plus"></i> Create New Topic</button>
            </div> 
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
<div class="container mt-3 pt-2">

    <?php include 'admin/db_connect.php' ?>

<!-- Search Bar -->
<div class="mb-3">
    <input type="text" id="forumSearch" class="form-control" placeholder="Search forum topics..." />
</div>

<!-- Forum Listings -->
<div class="row" id="forumsContainer">
    <?php
    $forum = $conn->query("SELECT f.*, u.name FROM forum_topics f INNER JOIN users u ON u.id = f.user_id ORDER BY f.id DESC");
    while ($row = $forum->fetch_assoc()): 
        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
        $desc = strtr(html_entity_decode($row['description']), $trans);
        $desc = str_replace(["<li>", "</li>"], ["", ","], $desc);
        $date_posted = date("d M, Y", strtotime($row['date_created'])); // Format date
        $comment_count = $conn->query("SELECT * FROM forum_comments WHERE topic_id = " . $row['id'])->num_rows;
    ?>
    <div class="col-md-4 mb-4 forum-card-container">
        <div class="forum-card">
            <span class="forum-date"><?php echo $date_posted; ?></span>
            <div class="forum-logo">
                <i class="fa fa-comments"></i>
            </div>
            <h6 class="mt-4 text-muted">By: <?php echo ucwords($row['name']); ?></h6>
            <h5 class="fw-bold"><?php echo ucwords($row['title']); ?></h5>
            <p class="truncate"><?php echo strip_tags($desc); ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="forum-comments"><i class="fa fa-comment"></i> <?php echo $comment_count; ?> Comments</span>
                <button class="details-btn view_topic" data-id="<?php echo $row['id']; ?>">View Topic</button>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Styling -->
<style>
    .forum-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: 0.3s ease-in-out;
        position: relative;
    }
    .forum-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }
    .forum-logo {
        font-size: 3rem;
        color: #007bff;
        text-align: center;
    }
    .truncate {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .forum-comments {
        font-weight: bold;
        color: #6c757d;
    }
    .forum-date {
        font-size: 0.9rem;
        color: gray;
        position: absolute;
        top: 10px;
        right: 15px;
    }
    .details-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }
    .details-btn:hover {
        background: #0056b3;
    }
</style>

<!-- JavaScript for Search & View Topic -->
<script>
    $(document).ready(function () {
        // Implement search filtering
        $("#forumSearch").on("keyup", function () {
            let value = $(this).val().toLowerCase();
            $(".forum-card-container").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

</div>
    



<script>
    // $('.card.gallery-list').click(function(){
    //     location.href = "index.php?page=view_gallery&id="+$(this).attr('data-id')
    // })
    $('#new_forum').click(function(){
        uni_modal("New Topic","manage_forum.php",'mid-large')
    })
    $('.view_topic').click(function(){
       location.replace('index.php?page=view_forum&id='+$(this).attr('data-id'))
    })
    $('.edit_forum').click(function(){
        uni_modal("Edit Topic","manage_forum.php?id="+$(this).attr('data-id'),'mid-large')
    })
    $('.gallery-img img').click(function(){
        viewer_modal($(this).attr('src'))
    })
     $('.delete_forum').click(function(){
        _conf("Are you sure to delete this Topic?","delete_forum",[$(this).attr('data-id')],'mid-large')
    })

    function delete_forum($id){
        start_load()
        $.ajax({
            url:'admin/ajax.php?action=delete_forum',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)

                }
            }
        })
    }
    $('#filter').keypress(function(e){
    if(e.which == 13)
        $('#search').trigger('click')
   })
    $('#search').click(function(){
        var txt = $('#filter').val()
        start_load()
        if(txt == ''){
        $('.Forum-list').show()
        end_load()
        return false;
        }
        $('.Forum-list').each(function(){
            var content = "";
            $(this).find(".filter-txt").each(function(){
                content += ' '+$(this).text()
            })
            if((content.toLowerCase()).includes(txt.toLowerCase()) == true){
                $(this).toggle(true)
            }else{
                $(this).toggle(false)
            }
        })
        end_load()
    })

</script>