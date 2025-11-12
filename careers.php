<?php 
include 'admin/db_connect.php'; 
?>
<style>

.container {
    max-width: 1140px !important;
}

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
            <h3 class="text-white">Job List</h3>
            <hr class="divider my-4" />
            <div class="row mb-2 justify-content-center">
                    <button class="btn btn-primary btn-block col-sm-12" type="button" id="new_career"><i class="fa fa-plus"></i> Post a Job Opportunity</button>
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
<!--     <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="filter-field"><i class="fa fa-search"></i></span>
                      </div>
                      <input type="text" class="form-control" placeholder="Filter" id="filter" aria-label="Filter" aria-describedby="filter-field">
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block btn-sm" id="search">Search</button>
                </div>
            </div>
            
        </div>
    </div> -->
<?php
$event = $conn->query("SELECT c.*, u.name FROM careers c INNER JOIN users u ON u.id = c.user_id ORDER BY c.id DESC");
?>

<!-- Include Bootstrap, DataTables, and FontAwesome -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<style>
    .job-card {
        background: #FBE8D3;
        border-radius: 15px;
        padding: 20px;
        position: relative;
        transition: 0.3s;
    }
    .job-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .job-date {
        position: absolute;
        top: 15px;
        left: 15px;
        background: white;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .job-logo {
        position: absolute;
        top: 15px;
        right: 15px;
        background: black;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .job-tags span {
        background: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 5px;
    }
    .job-salary {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .details-btn {
        background: black;
        color: white;
        border-radius: 12px;
        padding: 8px 15px;
        font-size: 14px;
        transition: 0.3s;
    }
    .details-btn:hover {
        background: #333;
    }
</style>

<div class="container mt-4">
    <h2 class="text-center mb-4">Job Listings</h2>

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="jobSearch" class="form-control" placeholder="Search for a job..." />
    </div>

    <!-- Job Listings -->
    <div class="row" id="jobsContainer">
        <?php while ($row = $event->fetch_assoc()): 
            $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
            unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
            $desc = strtr(html_entity_decode($row['description']), $trans);
            $desc = str_replace(["<li>", "</li>"], ["", ","], $desc);
            $date_posted = date("d M, Y", strtotime($row['date_created'])); // Format date
        ?>
        <div class="col-md-4 mb-4 job-card-container">
            <div class="job-card">
                <span class="job-date"><?php echo $date_posted; ?></span>
                <div class="job-logo">
                    <i class="fa fa-building"></i>
                </div>
                <h6 class="mt-4 text-muted"><?php echo ucwords($row['company']); ?></h6>
                <h5 class="fw-bold"><?php echo ucwords($row['job_title']); ?></h5>
                <div class="job-tags mb-2">
                    <span>Part-time</span>
                    <span>Senior Level</span>
                    <span>Remote</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="job-salary">$250/hr</span>
                    <!-- <button class="btn btn-primary float-right read_more" data-id="<?php echo $row['id'] ?>">Read More</button> -->
                    <button class="details-btn read_more" data-id="<?php echo $row['id']; ?>">Details</button>
                </div>
                <p class="text-muted mt-1"><?php echo ucwords($row['location']); ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Include jQuery and DataTables -->

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    // Implement search filtering
    $("#jobSearch").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $(".job-card-container").filter(function () {
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
    $('#new_career').click(function(){
        uni_modal("New Job Hiring","manage_career.php",'mid-large')
    })
    $('.read_more').click(function(){
        uni_modal("Career Opportunity","view_jobs.php?id="+$(this).attr('data-id'),'mid-large')
    })
    $('.gallery-img img').click(function(){
        viewer_modal($(this).attr('src'))
    })

   $('#filter').keypress(function(e){
    if(e.which == 13)
        $('#search').trigger('click')
   })
    $('#search').click(function(){
        var txt = $('#filter').val()
        start_load()
        if(txt == ''){
        $('.job-list').show()
        end_load()
        return false;
        }
        $('.job-list').each(function(){
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