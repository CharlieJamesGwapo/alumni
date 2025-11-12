<?php 
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php'); 

// Check if user is logged in and has admin privileges
if(!isset($_SESSION['login_id']) || ($_SESSION['login_type'] != 1 && $_SESSION['login_type'] != 4)){
    header('location:login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Management</title>
    <?php include 'header.php'; ?>
    <!-- SweetAlert2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<style>
    /* Global Styles */
    :root {
        --primary-color: #000000; /* Black */
        --secondary-color: #ffffff; /* White */
    }

    body {
        font-family: Arial, sans-serif;
    }

    .card {
        border-color: var(--primary-color);
    }

    .card-header {
        background-color: var(--secondary-color);
        color: var(--primary-color);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--secondary-color);
    }

    .btn-primary:hover {
        background-color: #333333;
        border-color: #333333;
    }

    .table thead th {
        background-color: var(--secondary-color);
        color: var(--primary-color);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    /* Form Label and Text */
    label {
        color: var(--primary-color);
    }

    input[type="text"], input[type="hidden"] {
        color: var(--primary-color);
    }

    /* Table Row */
    td {
        vertical-align: middle !important;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .col-md-4, .col-md-8 {
            margin-bottom: 20px;
        }

        .btn {
            margin: 2px;
            padding: 5px 10px;
        }
    }
</style>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <!-- FORM Panel -->
            <div class="col-md-4">  
                <form action="" id="manage-course">
                    <div class="card">
                        <div class="card-header">
                            Course Form
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id">
                            <div class="form-group">
                                <label class="control-label">Course</label>
                                <input type="text" class="form-control" name="course" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">About</label>
                                <textarea class="form-control" name="about" rows="3" placeholder="Brief description or program overview"></textarea>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
                                        <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="$('#manage-course').get(0).reset()"> Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- FORM Panel -->

            <!-- Table Panel -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <b>Course List</b>
                    </div>
                    <div class="card-body">
                        <table id="courseTable" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Course</th>
                                    <th class="text-center">About</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $course = $conn->query("SELECT * FROM courses ORDER BY id ASC");
                                while($row = $course->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td><?php echo htmlspecialchars($row['course']) ?></td>
                                    <td style="max-width:400px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?php echo htmlspecialchars($row['about']) ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_course" type="button"
                                            data-id="<?php echo $row['id'] ?>"
                                            data-course="<?php echo htmlspecialchars($row['course'], ENT_QUOTES) ?>"
                                            data-about="<?php echo htmlspecialchars($row['about'], ENT_QUOTES) ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete_course" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
        </table>
    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>  
</div>

</body>
</html>

<script>
    // Use DataTable with responsive
    $(document).ready(function(){
        $('#courseTable').DataTable({
            responsive: true,
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: 3 } // action column index changed to 3
            ]
        });
    });

    $('#manage-course').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var courseVal = $.trim(form.find("[name='course']").val());
        if(courseVal == ''){
            Swal.fire({
                icon: 'warning',
                title: 'Empty field',
                text: 'Please enter a course name.'
            });
            return;
        }
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_course',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                end_load();
                resp = resp.toString().trim();
                if (resp == '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Course successfully added',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else if (resp == '2') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Course successfully updated',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else if (resp == '3') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate',
                        text: 'This course already exists.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Try again.'
                    });
                }
            },
            error: function(){
                end_load();
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not reach server.'
                });
            }
        });
    });

    $(document).on('click', '.edit_course', function() {
        start_load();
        var cat = $('#manage-course');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='course']").val($(this).attr('data-course'));
        cat.find("[name='about']").val($(this).attr('data-about'));
        end_load();
        $('html, body').animate({ scrollTop: 0 }, 'fast'); // bring form into view on small devices
    });

    $(document).on('click', '.delete_course', function() {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the course.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_course(id);
            }
        });
    });

    function delete_course(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_course',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                end_load();
                resp = resp.toString().trim();
                if (resp == '1') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Course successfully deleted',
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not delete. Try again.'
                    });
                }
            },
            error: function(){
                end_load();
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not reach server.'
                });
            }
        });
    }
</script>
