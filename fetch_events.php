<?php
include_once 'admin/db_connect.php';

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$year = isset($_POST['year']) ? $_POST['year'] : 'all';
$itemsPerPage = 6;
$offset = ($page - 1) * $itemsPerPage;

$where = "";
if($year != 'all') {
    $where = " AND YEAR(schedule) = '$year'";
}

$today = date('Y-m-d');
$query = $conn->query("SELECT * FROM events WHERE schedule < '$today' $where ORDER BY schedule DESC LIMIT $itemsPerPage OFFSET $offset");

if($query->num_rows > 0):
    while($row = $query->fetch_assoc()):
?>
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 shadow-sm hover-card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <div class="event-date text-center me-3 p-2 bg-secondary text-white rounded">
                    <div class="h5 mb-0"><?php echo date('d', strtotime($row['schedule'])) ?></div>
                    <small><?php echo date('M', strtotime($row['schedule'])) ?></small>
                </div>
                <h5 class="card-title mb-0"><?php echo $row['title'] ?></h5>
            </div>
            <p class="card-text text-muted"><?php echo substr($row['description'], 0, 150) ?>...</p>
            <div class="mt-3">
                <i class="fas fa-map-marker-alt text-secondary"></i>
                <small class="text-muted ms-2"><?php echo $row['venue'] ?></small>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            <a href="view_event.php?id=<?php echo $row['id'] ?>" class="btn btn-outline-secondary btn-sm">View Details</a>
        </div>
    </div>
</div>
<?php 
    endwhile;
endif;
?>
