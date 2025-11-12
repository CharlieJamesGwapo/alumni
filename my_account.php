<?php 
include 'admin/db_connect.php';

// Check if user is logged in and bio exists
if(!isset($_SESSION['bio']) || !isset($_SESSION['login_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

// Initialize default values
$bio = $_SESSION['bio'] ?? [];
$default = [
    'lastname' => '',
    'firstname' => '',
    'middlename' => '',
    'suffixname' => '',
    'birthdate' => '',
    'address' => '',
    'gender' => '',
    'batch' => '',
    'course_id' => '',
    'connected_to' => '',
    'contact_no' => '',
    'company_name' => '',
    'company_address' => '',
    'company_email' => '',
    'email' => '',
    'avatar' => 'no-image.jpg' // default image
];

// Merge default values with bio data
$bio = array_merge($default, $bio);

?>
<style>
    .masthead{
        min-height: 23vh !important;
        height: 23vh !important;
        background-color: #800000 !important; /* Added maroon background */
    }
    .masthead:before{
        min-height: 23vh !important;
        height: 23vh !important;
    }
    img#cimg{
        max-height: 10vh;
        max-width: 6vw;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .control-label {
        font-weight: bold;
        color: #800000; /* Maroon color for labels */
    }
    .btn-primary {
        background-color: #800000 !important; /* Maroon background */
        border-color: #800000 !important;
        color: white !important;
    }
    .btn-primary:hover {
        background-color: #600000 !important; /* Darker maroon on hover */
        border-color: #600000 !important;
    }
    .divider {
        background-color: #800000;
        height: 2px;
        opacity: 0.8;
    }
    select.custom-select:focus,
    input.form-control:focus,
    textarea.form-control:focus {
        border-color: #800000;
        box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.25);
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #800000;
    }
    h5.control-label {
        color: #800000;
        font-weight: bold;
        border-bottom: 2px solid #800000;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    /* Responsive styles */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }
        .card {
            margin-bottom: 15px;
        }
        .col-md-4, .col-md-6 {
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
            margin-top: 10px;
        }
        img#cimg {
            max-width: 100%;
            height: auto;
        }
    }
    @media (max-width: 576px) {
        .masthead h3 {
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: 0.5rem;
        }
    }
</style>
        <header class="masthead">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Manage Account</h3>
                        <hr class="divider my-4" />

                    <div class="col-md-12 mb-2 justify-content-center">
                    </div>                        
                    </div>
                    
                </div>
            </div>
        </header>
            <div class="container mt-3 pt-2">
               <div class="col-lg-12">
                   <div class="card mb-4">
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="col-md-12">
                                    <form action="" id="update_account">
                                        <!-- Personal Info Section -->
                                        <h5 class="control-label">PERSONAL INFO</h5>
                                        <div class="row form-group">
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Last Name</label>
                                                <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($bio['lastname']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">First Name</label>
                                                <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($bio['firstname']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Middle Name</label>
                                                <input type="text" class="form-control" name="middlename" value="<?php echo htmlspecialchars($bio['middlename']); ?>" >
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Suffix Name</label>
                                                <input type="text" class="form-control" name="suffixname" value="<?php echo htmlspecialchars($bio['suffixname']); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Birthdate</label>
                                                <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($bio['birthdate']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Address</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($bio['address']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Gender</label>
                                                <select class="custom-select" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" <?php echo $bio['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                                    <option value="Female" <?php echo $bio['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Batch</label>
                                                <input type="input" class="form-control datepickerY" name="batch" value="<?php echo htmlspecialchars($bio['batch']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Course Graduated</label>
                                                <select class="custom-select select2" name="course_id" required>
                                                    <option value="">Select Course</option>
                                                    <?php 
                                                    $course = $conn->query("SELECT * FROM courses order by course asc");
                                                    while($row=$course->fetch_assoc()):
                                                    ?>
                                                        <option value="<?php echo $row['id'] ?>"  <?php echo $bio['course_id'] ==$row['id'] ? 'selected' : '' ?>><?php echo htmlspecialchars($row['course']); ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Employment Info Section -->
                                        <h5 class="control-label mt-4">EMPLOYMENT INFO</h5>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Currently Connected To</label>
                                                <textarea name="connected_to" id="" cols="30" rows="3" class="form-control"><?php echo htmlspecialchars($bio['connected_to']); ?></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Contact No</label>
                                                <input type="text" class="form-control" name="contact_no" value="<?php echo htmlspecialchars($bio['contact_no']); ?>">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Company Name</label>
                                                <input type="text" class="form-control" name="company_name" value="<?php echo htmlspecialchars($bio['company_name']); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Company Address</label>
                                                <input type="text" class="form-control" name="company_address" value="<?php echo htmlspecialchars($bio['company_address']); ?>">
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Company Email</label>
                                                <input type="email" class="form-control" name="company_email" value="<?php echo htmlspecialchars($bio['company_email']); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Profile Image</label>
                                                <input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
                                                <img src="admin/assets/uploads/<?php echo htmlspecialchars($bio['avatar']); ?>" alt="" id="cimg">
                                            </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-md-4">
                                                <label for="" class="control-label">Email</label>
                                                <input type="email" class="form-control" name="email"  value="<?php echo htmlspecialchars($bio['email']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="control-label">Password</label>
                                                <input type="password" class="form-control" name="password">
                                                <small><i>Leave this blank if you dont want to change your password</i></small>
                                            </div>
                                        </div>
                                        <div id="msg">
                                            
                                        </div>
                                        <hr class="divider">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <!-- Single Update Account button -->
                                                <button type="submit" class="btn btn-primary px-5" style="background-color: #800000; border-color: #800000;">
                                                    Update Account
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                   </div>
               </div>
                
            </div>


<script>
   $('.datepickerY').datepicker({
        format: " yyyy", 
        viewMode: "years", 
        minViewMode: "years"
   })
   $('.select2').select2({
    placeholder:"Please Select Here",
    width:"100%"
   })
   function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$('#update_account').submit(function(e){
    e.preventDefault()
    start_load()
    $.ajax({
        url:'admin/ajax.php?action=update_account',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success:function(resp){
            if(resp == 1){
                alert_toast("Account successfully updated.",'success');
                setTimeout(function(){
                 location.reload()
                },700)
            }else{
                $('#msg').html('<div class="alert alert-danger">email already exist.</div>')
                end_load()
            }
        }
    })
})
</script>