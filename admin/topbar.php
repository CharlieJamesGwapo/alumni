<style>
  .logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 7px 11px;
    border-radius: 50%;
    color: #800000; /* Maroon color */
  }
  .navbar-custom {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .navbar-custom .dropdown-menu {
    background-color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .navbar-custom .dropdown-item {
    color: #800000;
    padding: 0.5rem 1.5rem;
  }
  .navbar-custom .dropdown-item:hover {
    background-color: #f8f9fa;
  }
  @media (max-width: 768px) {
    .system-name {
      font-size: 1rem;
    }
    .navbar-container {
      padding: 0.5rem;
    }
  }
</style>

<nav class="navbar navbar-light fixed-top navbar-custom" style="padding:0;min-height: 3.5rem">
  <div class="container-fluid navbar-container">
  <div class="row w-100 align-items-center">
    <div class="col-md-2">
      <!-- Reserved for logo if needed -->
    </div>
    <div class="col-md-3">
      <span class="text-white system-name">
        <strong id="currentDateTime"></strong>
      </span>
    </div>
    <div class="col-md-7 text-right">
      <div class="dropdown">
        <a href="#" class="text-white dropdown-toggle" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo $_SESSION['login_name'] ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="account_settings">
          <a class="dropdown-item" href="javascript:void(0)" id="manage_my_account">
            <i class="fa fa-cog"></i> Manage Account
          </a>
          <a class="dropdown-item" href="ajax.php?action=logout">
            <i class="fa fa-power-off"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </div>
  </div>
</nav>


<script>
function updateDateTime() {
    const now = new Date();
    const formattedDateTime = now.toLocaleString('en-US', {
        weekday: 'long',  // e.g., Monday
        year: 'numeric',
        month: 'long',    // e.g., March
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true      // 12-hour format with AM/PM
    });
    document.getElementById('currentDateTime').textContent = formattedDateTime;
}

// Update time every second
setInterval(updateDateTime, 1000);

// Initialize immediately
updateDateTime();
</script>

<script>
  $('#manage_my_account').click(function(){
  uni_modal("Manage Account","manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own")
  })
</script>