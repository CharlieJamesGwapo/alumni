<?php
 include('db_connect.php');?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div><b>Archived Alumni</b></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered table-hover" id="archiveTable" style="width:100%;">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Image</th>
                                <th>Name</th>
                                <th>Course / Strand</th>
                                <th class="text-center">Batch</th>
                                <th>Email</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var table = $('#archiveTable').DataTable({
        ajax: { url: 'fetch_alumni.php?source=archive', dataSrc: '' },
        columns: [
            { data: null, className: 'text-center', render: function(d,t,row,meta){ return meta.row +1; } },
            { data: 'img', className: 'text-center', orderable: false, render: function(d){ return '<img src="'+d+'" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">'; } },
            { data: 'fullname', render: function(d,t,row){ return '<strong>'+d+'</strong><br><small>ID: '+(row.alumni_id||'')+'</small>'; } },
            { data: 'course_strand' },
            { data: 'batch', className: 'text-center' },
            { data: 'email' },
            { data: 'status', className: 'text-center', render: function(d){ return parseInt(d)===1?'<span class="badge badge-primary">Validated</span>':'<span class="badge badge-secondary">Not Validated</span>'; } },
            { data: null, className: 'text-center', orderable: false, render: function(d){ 
                return '<button class="btn btn-sm btn-outline-success restore_alumni" data-id="'+d.id+'">Restore</button> <button class="btn btn-sm btn-outline-danger perma_delete" data-id="'+d.id+'">Delete</button>';
            } }
        ],
        responsive:true
    });

    $('#archiveTable').on('click', '.restore_alumni', function(){
        var id = $(this).data('id');
        Swal.fire({ title:'Restore?', icon:'question', showCancelButton:true }).then((r)=>{
            if(r.isConfirmed){
                $.post('ajax.php?action=restore_alumni',{id:id}).done(function(resp){
                    if (resp.toString().trim()=='1') { Swal.fire({icon:'success',timer:800,showConfirmButton:false}); table.ajax.reload(null,false); }
                    else Swal.fire({icon:'error',title:'Error'});
                });
            }
        });
    });

    $('#archiveTable').on('click', '.perma_delete', function(){
        var id = $(this).data('id');
        Swal.fire({ title:'Delete permanently?', text:'Cannot be undone', icon:'warning', showCancelButton:true }).then((r)=>{
            if(r.isConfirmed){
                $.post('ajax.php?action=perma_delete_archive',{id:id}).done(function(resp){
                    if (resp.toString().trim()=='1') { Swal.fire({icon:'success',timer:800,showConfirmButton:false}); table.ajax.reload(null,false); }
                    else Swal.fire({icon:'error',title:'Error'});
                });
            }
        });
    });
});
</script>