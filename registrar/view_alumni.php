<?php include 'db_connect.php' ?>
<?php
// Helper to resolve profile image path or return inline SVG placeholder
function resolve_profile_image_from_row($row)
{
    $filename = '';
    if (!empty($row['avatar'])) $filename = $row['avatar'];
    if (empty($filename) && !empty($row['img'])) $filename = $row['img'];

    if (!empty($filename)) {
        $candidates = [
            __DIR__ . '/../admin/assets/uploads/' . $filename,
            __DIR__ . '/../assets/uploads/' . $filename,
            __DIR__ . '/uploads/' . $filename,
            __DIR__ . '/../assets/img/' . $filename,
            __DIR__ . '/../uploads/' . $filename
        ];
        foreach ($candidates as $p) {
            if (is_file($p) && file_exists($p)) {
                $web = str_replace('\\', '/', str_replace(__DIR__, '', $p));
                if ($web === '' || $web[0] !== '.') {
                    $web = '.' . $web;
                }
                return $web;
            }
        }
        if (stripos($filename, 'http://') === 0 || stripos($filename, 'https://') === 0) {
            return $filename;
        }
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">'
         . '<rect width="100%" height="100%" fill="#f3f4f6"/>'
         . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#9ca3af" font-family="Arial,Helvetica,sans-serif" font-size="16">No Image</text>'
         . '</svg>';
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}

if(isset($_GET['id'])){
    $qry = $conn->query("SELECT a.*,c.course,Concat(a.lastname,', ',a.firstname,' ',a.middlename) as name 
                         FROM alumnus_bio a 
                         INNER JOIN courses c ON c.id = a.course_id 
                         WHERE a.id = ".$_GET['id']);
    foreach($qry->fetch_array() as $k => $val){
        $$k = $val;
    }
    // compute resolved image URL for this record
    $resolved_img = resolve_profile_image_from_row(get_defined_vars());
}
?>

<div class="container-fluid">
    <div class="card profile-card">
        <div class="card-body">
            <div class="row">
                <!-- Avatar Section -->
                <div class="col-md-4 text-center profile-avatar">
                    <img src="<?php echo htmlspecialchars($resolved_img ?? ($avatar ?? '')) ?>" 
                         alt="Avatar of <?php echo htmlspecialchars($name ?? '') ?>" class="img-fluid rounded-circle img-thumbnail" loading="lazy" width="150" height="150" style="object-fit:cover;">
                    <h4 class="mt-3"><?php echo htmlspecialchars($name ?? '') ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($course ?? '') ?> - Batch <?php echo htmlspecialchars($batch ?? '') ?></p>
                </div>

                <!-- Details Section -->
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo $email ?></p>
                            <p><strong>Birthdate:</strong> <?php echo $birthdate ?></p>
                            <p><strong>Gender:</strong> <?php echo $gender ?></p>
                            <p><strong>Address:</strong> <?php echo $address ?></p>
                            <p><strong>Suffix Name:</strong> <?php echo $suffixname ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact_no ?? '') ?></p>
                            <p><strong>Type of Industry:</strong> <?php echo htmlspecialchars($connected_to ?? 'Not specified') ?></p>
                            <p><strong>Company Email:</strong> <?php echo htmlspecialchars($company_email ?? '') ?></p>
                            <p><strong>Company Address:</strong> <?php echo htmlspecialchars($company_address ?? '') ?></p>
                            <p><strong>Status:</strong> 
                                <?php if($status == 1): ?>
                                    <span class="badge badge-verified">Verified</span>
                                <?php else: ?>
                                    <span class="badge badge-unverified">Unverified</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer text-right">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="alumni_list" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $alumni = $conn->query("SELECT a.*, c.course as course FROM alumnus_bio a 
                                      INNER JOIN courses c ON c.id = a.course_id 
                                      ORDER BY a.lastname ASC");
                while($row = $alumni->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo ucwords($row['lastname'].', '.$row['firstname'].' '.$row['middlename']) ?></td>
                    <td><?php echo $row['course'] ?></td>
                    <td><?php echo $row['batch'] ?></td>
                    <td><?php echo $row['email'] ?></td>
                    <td>
                        <?php if($row['status'] == 1): ?>
                            <span class="badge badge-success">Validated</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Pending Validation</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm view_alumni" data-id="<?php echo $row['id'] ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if($row['status'] == 0): ?>
                            <button type="button" class="btn btn-success btn-sm validate_alumni" data-id="<?php echo $row['id'] ?>">
                                <i class="fas fa-check"></i> Validate
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
   /* Dark Themed Background */
body {
    background: linear-gradient(135deg, #001f3f, #003366, #004080);
    background-attachment: fixed;
    background-size: cover;
    color: white;
}

/* Glassmorphic Profile Card */
.profile-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 20px;
    max-width: 800px;
    margin: auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
}

/* Avatar Styling */
.profile-avatar img {
    width: 150px;
    height: 150px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
}

/* Profile Info */
p {
    margin-bottom: 8px;
    padding-bottom: 5px;
    font-size: 1rem;
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

/* Name Heading */
h4 {
    color: white;
    font-weight: bold;
}

/* Status Badge */
.badge-verified {
    background: #004080;
    color: white;
    padding: 5px 10px;
    border-radius: 12px;
}

.badge-unverified {
    background: #6c757d;
    color: white;
    padding: 5px 10px;
    border-radius: 12px;
}

/* Responsive Fix */
@media (max-width: 768px) {
    .profile-avatar img {
        width: 120px;
        height: 120px;
    }
}


    /* Responsive Fix */
    @media (max-width: 768px) {
        .profile-avatar img {
            width: 120px;
            height: 120px;
        }
    }
</style>

<script>
    $('.validate_alumni').click(function(){
        _conf("Are you sure to validate this alumni?","validate_alumni",[$(this).attr('data-id')])
    })

    function validate_alumni($id){
        start_load()
        $.ajax({
            url:'admin/ajax.php?action=validate_alumni',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp == 1){
                    alert_toast("Alumni successfully validated",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    }
</script>
