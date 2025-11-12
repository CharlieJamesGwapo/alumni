<?php
session_start();
include '../admin/db_connect.php';

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    header("Location: ../login.php");
    exit();
}

// Fetch all alumni (no program filter)
$q = "SELECT ab.*, ab.strand_id, ai.program_type, s.name AS strand_name, c.course AS course_name
      FROM alumnus_bio ab
      LEFT JOIN alumni_ids ai ON ab.id = ai.alumni_id
      LEFT JOIN strands s ON ab.strand_id = s.id
      LEFT JOIN courses c ON ab.course_id = c.id
      ORDER BY ab.lastname ASC, ab.firstname ASC";
$res = $conn->query($q);

// Fetch distinct courses for filter
$courses = [];
$cr = $conn->query("SELECT id, course FROM courses ORDER BY course ASC");
if ($cr) {
    while ($r = $cr->fetch_assoc()) $courses[] = $r;
}

// Fetch distinct batches for filter
$batches = [];
$br = $conn->query("SELECT DISTINCT batch FROM alumnus_bio WHERE batch IS NOT NULL AND batch <> '' ORDER BY batch DESC");
if ($br) {
    while ($r = $br->fetch_assoc()) $batches[] = $r['batch'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>All Alumni — MOIST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root{
            --moist-red:#800000;
            --accent:#600000;
            --muted:#6c757d;
        }
        body{background:#f8f9fa;font-family:system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial}
        .topbar{background:linear-gradient(135deg,var(--moist-red) 0%,var(--accent) 100%);padding:14px 0;color:#fff}
        .brand{display:flex;align-items:center;gap:12px}
        .brand img{height:48px;object-fit:contain}
        .page-header{background:#fff;padding:14px;border-radius:8px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 6px rgba(0,0,0,.06)}
        .controls { gap:10px; display:flex; flex-wrap:wrap; align-items:center; margin-bottom:10px }
        .table-wrap{background:#fff;padding:12px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.06)}
        .btn-program .btn{min-width:90px}
        @media (max-width:576px){
            .page-header{flex-direction:column;gap:10px;align-items:flex-start}
            .controls { flex-direction:column; align-items:stretch; }
        }

        /* Print styles - match pasted image header / table look */
        @media print {
            body { 
                background: #fff;
                margin: 0;
                padding: 20px;
            }
            .topbar,
            .page-header,
            .controls,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate,
            .dt-buttons,
            .no-print {
                display: none !important;
            }
            .table-wrap {
                box-shadow: none;
                padding: 0;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                background-color: #fff !important;
                border: 1px solid #ddd !important;
                padding: 8px !important;
            }
            .badge {
                border: 1px solid #ddd !important;
                padding: 4px 8px !important;
                font-weight: normal !important;
            }
        }

        /* Custom styles for Alumni Directory */
        .program-filter {
            cursor: pointer;
        }
        .program-filter.active {
            background-color: var(--moist-red);
            color: white;
        }
        .input-group {
            max-width: 360px;
        }
        .input-group label {
            min-width: 120px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 90%;
        }
    </style>
</head>
<body>
<header class="topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="brand">
      <img src="../assets/img/logo.png" alt="MOIST Logo">
      <div>
        <div style="font-weight:800;font-size:18px">MOIST</div>
        <div style="font-size:13px;opacity:.9">Alumni Management</div>
      </div>
    </div>
    <div class="no-print">
        <a href="alumni.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
    </div>
  </div>
</header>

<div class="container my-4">
    <div class="page-header">
        <div>
            <h5 class="mb-0">Alumni Directory</h5>
            <small class="text-muted">View and manage alumni records</small>
        </div>
        <div class="no-print d-flex align-items-center gap-3">
            <!-- Changed to only show College/SHS toggle -->
            <div class="btn-program btn-group" role="group" aria-label="program switch">
                <button type="button" class="btn btn-outline-secondary active program-filter" data-filter="college">College</button>
                <button type="button" class="btn btn-outline-secondary program-filter" data-filter="shs">SHS</button>
            </div>
            
            <!-- Added rows selector -->
            <div class="d-flex align-items-center">
                <label class="me-2 small">Rows:</label>
                <select id="rowCount" class="form-select form-select-sm" style="width:100px">
                    <option value="25">25 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                    <option value="500">500 rows</option>
                    <option value="-1">All rows</option>
                </select>
            </div>
        </div>
    </div>

    <div class="controls no-print">
        <div class="input-group" style="max-width:360px;">
            <label class="input-group-text">Course</label>
            <select id="courseFilter" class="form-select form-select-sm">
                <option value="">All Courses / Strands</option>
                <?php foreach($courses as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['course'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($c['course'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-group" style="max-width:220px;">
            <label class="input-group-text">Batch</label>
            <select id="batchFilter" class="form-select form-select-sm">
                <option value="">All Batches</option>
                <?php foreach($batches as $b): ?>
                    <option value="<?php echo htmlspecialchars($b, ENT_QUOTES); ?>"><?php echo htmlspecialchars($b, ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ms-auto d-flex gap-2">
            <button id="clearFilters" class="btn btn-sm btn-outline-secondary">Clear Filters</button>
        </div>
    </div>

    <div class="table-wrap">
        <table id="allAlumniTable" class="table table-hover table-striped w-100">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Name</th>
                    <th class="college-only">Course Graduated</th>
                    <th class="shs-only d-none">Strand Graduated</th>
                    <th>Batch</th>
                    <th>Email</th>
                    <th class="college-only">Employment Status</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; while($row = $res->fetch_assoc()):
                    $fullname = trim($row['firstname'].' '.($row['middlename'] ? $row['middlename'].' ' : '').$row['lastname']);
                    $level = !empty($row['strand_id']) ? 'shs' : 'college';
                ?>
                <tr data-level="<?php echo $level; ?>">
                    <td><?php echo $i++; ?></td>
                    <td><?php echo ucwords(htmlspecialchars($fullname, ENT_QUOTES)); ?></td>
                    <td class="college-only"><?php echo htmlspecialchars($row['course_name'] ?? '—', ENT_QUOTES); ?></td>
                    <td class="shs-only d-none"><?php echo htmlspecialchars($row['strand_name'] ?? '—', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['batch'] ?? '—', ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? '—', ENT_QUOTES); ?></td>
                    <td class="college-only"><?php echo htmlspecialchars($row['employment_status'] ?? '—', ENT_QUOTES); ?></td>
                    <td><?php echo ($row['status'] == 1) ? '<span class="badge bg-success">Validated</span>' : '<span class="badge bg-warning text-dark">Not Validated</span>'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(function(){
    // Initialize DataTable
    const table = $('#allAlumniTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthChange: false,
        order: [[1,'asc']],
        dom: '<"row mb-2"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rt<"row mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success me-2',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: {
                    columns: [0,1,2,4,5,6,7]
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger me-2',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                exportOptions: {
                    columns: [0,1,2,4,5,6,7]
                }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-print"></i> Print',
                exportOptions: {
                    columns: [0,1,2,4,5,6,7]
                },
                customize: function(win) {
                    $(win.document.body).css('font-family', 'system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial');
                    
                    $(win.document.body).prepend(`
                        <div style="text-align:center;margin-bottom:20px;">
                            <img src="../assets/img/logo.png" style="height:80px;margin-bottom:10px;"><br>
                            <div style="font-size:20px;font-weight:bold;color:#800000;margin-bottom:5px;">
                                Misamis Oriental Institute of Science and Technology
                            </div>
                            <div style="font-size:14px;margin-bottom:5px;">
                                Sta. Cruz, Cogon, Balingasag, Misamis Oriental
                            </div>
                            <div style="font-size:16px;font-weight:bold;margin:15px 0;">
                                Alumni Directory Report
                            </div>
                            <div style="font-size:13px;color:#666;">
                                College Alumni | 
                                ${$('#courseFilter').val() || 'All Courses'} | 
                                Batch: ${$('#batchFilter').val() || 'All Batches'}
                            </div>
                        </div>
                    `);

                    $(win.document.body).append(`
                        <div style="text-align:center;margin-top:20px;font-size:12px;color:#666;">
                            Generated on: ${new Date().toLocaleString()}<br>
                            MOIST Alumni Management System
                        </div>
                    `);

                    $(win.document.body).find('table').addClass('table table-bordered')
                        .css({
                            'width': '100%',
                            'border-collapse': 'collapse',
                            'font-size': '12px'
                        });

                    $(win.document.body).find('th').css({
                        'text-align': 'left',
                        'background-color': '#f8f9fa',
                        'border': '1px solid #dee2e6',
                        'padding': '8px',
                        'font-weight': 'bold'
                    });

                    $(win.document.body).find('td').css({
                        'border': '1px solid #dee2e6',
                        'padding': '8px'
                    });
                }
            }
        ]
    });

    // Program switch (College/SHS)
    $('.program-filter').on('click', function() {
        $('.program-filter').removeClass('active');
        $(this).addClass('active');
        
        const isCollege = $(this).data('filter') === 'college';
        
        // Toggle columns and update filter options
        $('.college-only').toggleClass('d-none', !isCollege);
        $('.shs-only').toggleClass('d-none', isCollege);
        
        // Clear filters when switching views
        $('#courseFilter, #batchFilter').val('');
        
        // Filter rows based on program type
        table.rows().every(function() {
            const rowLevel = $(this.node()).data('level');
            $(this.node()).toggle(rowLevel === (isCollege ? 'college' : 'shs'));
        });
        
        table.draw();
        updateFilterOptions(isCollege);
    });

    // Update filter options based on view
    function updateFilterOptions(isCollege) {
        const courseSelect = $('#courseFilter');
        courseSelect.empty().append('<option value="">All ' + (isCollege ? 'Courses' : 'Strands') + '</option>');
        
        // Get unique values from visible rows
        const uniqueValues = new Set();
        table.rows(':visible').every(function() {
            const value = this.data()[2]; // Course/Strand column
            if(value && value !== '—') uniqueValues.add(value);
        });
        
        // Add options
        Array.from(uniqueValues).sort().forEach(value => {
            courseSelect.append(`<option value="${value}">${value}</option>`);
        });
    }

    // Course filter
    $('#courseFilter').on('change', function() {
        const course = $(this).val();
        table.column(2).search(course ? '^' + escapeRegExp(course) + '$' : '', true, false).draw();
    });

    // Batch filter
    $('#batchFilter').on('change', function() {
        const batch = $(this).val();
        table.column(4).search(batch ? '^' + escapeRegExp(batch) + '$' : '', true, false).draw();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#courseFilter, #batchFilter').val('');
        table.search('').columns().search('').draw();
    });

    // Helper function to escape special characters in regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Initialize with college view
    $('.program-filter[data-filter="college"]').trigger('click');
});
</script>
</body>
</html>