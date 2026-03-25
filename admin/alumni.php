<?php include('db_connect.php'); include('log_activity.php'); ?>

<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <div class="col-lg-12">
        <?php 
            // reliable total count
            $total = 0;
            $cnt = $conn->query("SELECT COUNT(*) as total FROM alumnus_bio");
            if ($cnt && $row_cnt = $cnt->fetch_assoc()) {
                $total = isset($row_cnt['total']) ? intval($row_cnt['total']) : 0;
            }
        ?>
        <div class="row mb-4 mt-4">
            <div class="col-md-12"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div><b>List of Alumni</b></div>
                        <div>
                            <span class="badge badge-info mr-2">Total: <?php echo $total ?></span>
                            <button class="btn btn-sm btn-outline-success" id="print-btn">
                                <i class="fa fa-print"></i> Print
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered table-hover" id="alumniTable" style="width:100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Image</th>
                                        <th>Name</th>
                                        <th>Course / Strand</th>
                                        <th class="text-center">Batch</th>
                                        <th>Email</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>  
</div>

<style>
    td{ vertical-align: middle !important; }
    td p{ margin: unset }
    @media (max-width: 768px) {
        td, th { font-size: 12px; }
        .card-header { flex-direction: column; align-items: flex-start; gap: .5rem; }
        .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
    }
    .btn-sm i { font-size: 0.8rem; }
    .me-1 { margin-right: 0.25rem !important; }
</style>

<script>
    $(document).ready(function(){
        var table = $('#alumniTable').DataTable({
            ajax: {
                url: 'fetch_alumni.php',
                dataSrc: ''
            },
            columns: [
                { data: null, className: 'text-center', render: function(data, type, row, meta){ return meta.row + 1; } },
                { data: 'img', className: 'text-center', orderable: false, render: function(d){ return '<img src="'+d+'" alt="img" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">'; } },
                { data: 'fullname', render: function(d, t, row){ var id = row.alumni_id || ''; return '<strong>'+d+'</strong><br><small>ID: '+id+'</small>'; } },
                { data: 'course_strand' },
                { data: 'batch', className: 'text-center' },
                { data: 'email' },
                { data: 'status', className: 'text-center', render: function(d){ if (parseInt(d) === 1) return '<span style="color: #28a745; font-weight: bold;">Validated</span>'; return '<span style="color: #6c757d;">Not Validated</span>'; } }
            ],
            responsive: true,
            pageLength: 25,
            autoWidth: false
        });

        // Alumni table is now read-only for presentation purposes

        $('#print-btn').click(function() { printAlumniList(table); });
    });

    function printAlumniList(dt) {
        var rows = dt.rows({ search:'applied' }).nodes().to$().clone();
        rows.find('td:last-child, th:last-child').remove();
        rows.find('img').each(function(){ this.style.maxWidth = '60px'; this.style.height = '60px'; this.style.objectFit = 'cover'; });

        let printWindow = window.open('', '', 'height=800,width=1000');
        printWindow.document.write('<html><head><title>Alumni List</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">');
        printWindow.document.write('<style>table{width:100%;border-collapse:collapse;}th,td{border:1px solid #000;padding:6px;text-align:left;}img{max-width:60px;height:60px;object-fit:cover;border-radius:6px;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="container"><h2 class="mb-3">Alumni List</h2>');
        printWindow.document.write('<table class="table table-sm table-bordered">');
        printWindow.document.write($('#alumniTable thead').clone()[0].outerHTML);
        printWindow.document.write('<tbody>');
        rows.each(function(){ printWindow.document.write(this.outerHTML); });
        printWindow.document.write('</tbody></table>');
        printWindow.document.write('<p class="text-muted">Generated: '+ new Date().toLocaleString() +'</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('<script>window.onload=function(){setTimeout(function(){window.print();},300);};<\/script>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
    }
</script>
