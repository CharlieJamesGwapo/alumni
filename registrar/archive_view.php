<?php
session_start();
include '../admin/db_connect.php';

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    header("Location: login.php");
    exit();
}

// Fetch archived alumni data
$query = "SELECT * FROM archive_alumni ORDER BY archived_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOIST ONLINE ALUMNI TRACKING - Archives</title>
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <style>
        :root {
            --primary: #800000;
            --primary-dark: #600000;
        }
        .navbar {
            background-color: var(--primary);
        }
        .btn-restore {
            background-color: #28a745;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-restore:hover {
            background-color: #218838;
            color: white;
            transform: translateY(-2px);
        }
        .archive-stats {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="alumni.php">
                <img src="../assets/img/logo.png" alt="MOIST Logo" height="40">
                <span>MOIST Alumni Archives</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="alumni.php"><i class="fas fa-arrow-left"></i> Back to Alumni List</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Stats Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="archive-stats">
                    <h6 class="mb-2">Total Archived Alumni</h6>
                    <div class="stats-number" id="totalArchived">0</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="archive-stats">
                    <h6 class="mb-2">Archive Date Range</h6>
                    <div class="stats-number" id="dateRange">-</div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-archive me-2"></i>Archived Alumni Records</h5>
                    <div>
                        <button id="restoreSelectedArchived" class="btn btn-success btn-sm"><i class="fas fa-undo"></i> Restore Selected</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="archivedTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="archSelectAll"></th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Email</th>
                            <th>Archive Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $row_number = 1;
                        while ($row = $result->fetch_assoc()): 
                            $fullname = $row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname'];
                        ?>
                        <tr>
                            <td><input type="checkbox" class="arch-row-chk" data-id="<?php echo $row['id']; ?>"></td>
                            <td><?php echo $row_number++; ?></td>
                            <td><?php echo htmlspecialchars(ucwords($fullname)); ?></td>
                            <td><?php 
                                // Safely resolve course name. Some archived rows (SHS) may have course_id = 0 or NULL.
                                $course_name = 'N/A';
                                $course_id = isset($row['course_id']) ? intval($row['course_id']) : 0;
                                if ($course_id > 0) {
                                    $course_query = $conn->query("SELECT course FROM courses WHERE id = " . $course_id . " LIMIT 1");
                                    if ($course_query && $course_query->num_rows > 0) {
                                        $course_row = $course_query->fetch_assoc();
                                        $course_name = $course_row['course'] ?? 'N/A';
                                    }
                                }
                                echo htmlspecialchars($course_name);
                            ?></td>
                            <td><?php echo htmlspecialchars($row['batch'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['archived_date'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-restore restore-btn" data-id="<?php echo $row['id']; ?>">
                                    <i class="fas fa-undo-alt"></i> Restore
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#archivedTable').DataTable({
                // archive date is column index 6 (0-based: 0 checkbox,1#,2name,3course,4batch,5email,6date)
                "order": [[6, "desc"]], // Sort by archive date by default
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            });

            // Update stats
            function updateStats() {
                const totalArchived = table.rows().count();
                $('#totalArchived').text(totalArchived);

                // Get date range (archive date column index is 6 in table)
                if(totalArchived > 0) {
                    // Use the rendered cells for the archive date column (index 6)
                    const dateCells = table.column(6, {search:'applied'}).nodes().to$();
                    const dates = [];
                    dateCells.each(function(){
                        const txt = $(this).text().trim();
                        const d = new Date(txt);
                        if (!isNaN(d)) dates.push(d);
                    });
                    if (dates.length > 0) {
                        dates.sort(function(a,b){ return a - b; });
                        const earliest = dates[0].toLocaleDateString('en-US', {month: 'short', year: 'numeric'});
                        const latest = dates[dates.length-1].toLocaleDateString('en-US', {month: 'short', year: 'numeric'});
                        $('#dateRange').text(`${earliest} - ${latest}`);
                    } else {
                        $('#dateRange').text('-');
                    }
                }
            }

            // Initialize stats
            updateStats();

            // Handle restore button click
            $('.restore-btn').click(function() {
                const btn = $(this);
                const row = btn.closest('tr');
                const id = btn.data('id');
                // Name is column index 2
                const name = row.find('td').eq(2).text().trim();

                Swal.fire({
                    title: 'Restore Alumni',
                    text: `Are you sure you want to restore ${name} from archives?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'restore_alumni.php',
                            type: 'POST',
                            data: { id: id },
                            success: function(response) {
                                let result;
                                try { result = (typeof response === 'string') ? JSON.parse(response) : response; } catch(e){ result = response; }
                                if(result && result.status === 'success') {
                                    // Remove via DataTables API
                                    table.row(row).remove().draw(false);
                                    updateStats();
                                    Swal.fire('Restored!', `${name} has been restored successfully.`, 'success');
                                } else {
                                    Swal.fire('Error!', (result && result.message) ? result.message : 'There was an error restoring the alumni.', 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Select all handling
            $('#archSelectAll').on('change', function(){
                const checked = $(this).prop('checked');
                $('.arch-row-chk').prop('checked', checked);
            });

            // Keep header checkbox state in sync
            $(document).on('change', '.arch-row-chk', function(){
                const all = $('.arch-row-chk').length;
                const checked = $('.arch-row-chk:checked').length;
                $('#archSelectAll').prop('checked', all === checked && all > 0);
            });

            // Bulk restore
            $('#restoreSelectedArchived').click(function(){
                const ids = $('.arch-row-chk:checked').map(function(){ return $(this).data('id'); }).get();
                if (ids.length === 0) {
                    Swal.fire('No selection', 'Please select archived alumni to restore', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Restore selected',
                    text: `Are you sure you want to restore ${ids.length} alumni from archives?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore',
                    confirmButtonColor: '#28a745'
                }).then((res) => {
                    if (!res.isConfirmed) return;
                    const promises = ids.map(id => $.post('restore_alumni.php', { id: id }));
                    Promise.all(promises).then(function(results){
                        let successCount = 0;
                        results.forEach(function(r, idx){
                            let data;
                            try { data = (typeof r === 'string') ? JSON.parse(r) : r; } catch(e){ data = r; }
                            if (data && data.status === 'success') {
                                successCount++;
                            } else {
                                console.warn('Restore failed for id', ids[idx], data);
                            }
                        });
                        // Remove restored rows from table (only those checked)
                        $('.arch-row-chk:checked').each(function(){
                            table.row($(this).closest('tr')).remove();
                        });
                        table.draw(false);
                        updateStats();
                        Swal.fire('Restored', `${successCount} alumni restored`, 'success');
                    }).catch(function(err){ console.error(err); Swal.fire('Error', 'Some restores failed', 'error'); });
                });
            });
        });
    </script>
</body>
</html>
