<style>
  .topbar {
    position: fixed;
    top: 0;
    right: 0;
    left: 250px;
    height: 60px;
    z-index: 1040;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: left 0.3s ease;
  }

  .topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .topbar-datetime {
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 400;
  }

  .topbar-datetime i {
    margin-right: 6px;
    color: #94a3b8;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .topbar-user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 14px;
    border-radius: 10px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .topbar-user-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
    text-decoration: none;
  }

  .topbar-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: white;
    flex-shrink: 0;
  }

  .topbar .dropdown-menu {
    background: white;
    border: none;
    border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    padding: 0.5rem;
    min-width: 200px;
    margin-top: 8px;
  }

  .topbar .dropdown-item {
    color: #374151;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background 0.15s;
  }

  .topbar .dropdown-item:hover {
    background: #f3f4f6;
    color: #1a1a2e;
  }

  .topbar .dropdown-item i {
    width: 18px;
    text-align: center;
    opacity: 0.7;
  }

  .topbar .dropdown-divider {
    margin: 0.3rem 0.5rem;
    border-color: #e5e7eb;
  }

  .topbar .dropdown-item.text-danger:hover {
    background: #fef2f2;
    color: #dc2626;
  }

  @media (max-width: 991px) {
    .topbar {
      left: 0;
      padding-left: 60px;
    }
  }

  @media (max-width: 576px) {
    .topbar { padding: 0 0.75rem 0 55px; height: 52px; }
    .topbar-datetime { display: none; }
    .topbar-user-btn span.user-name-full { display: none; }
    .topbar-user-btn span.user-name-short { display: inline; }
  }

  @media (min-width: 577px) {
    .topbar-user-btn span.user-name-short { display: none; }
  }
</style>

<div class="topbar">
  <div class="topbar-left">
    <span class="topbar-datetime">
      <i class="fa-regular fa-clock"></i>
      <span id="currentDateTime"></span>
    </span>
  </div>
  <div class="topbar-right">
    <div class="dropdown">
      <a href="#" class="topbar-user-btn dropdown-toggle" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="topbar-avatar"><?php echo strtoupper(substr(htmlspecialchars($_SESSION['login_name']), 0, 1)); ?></span>
        <span class="user-name-full"><?php echo htmlspecialchars($_SESSION['login_name']); ?></span>
        <span class="user-name-short"><?php echo htmlspecialchars(explode(' ', $_SESSION['login_name'])[0]); ?></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="account_settings">
        <a class="dropdown-item" href="javascript:void(0)" id="manage_my_account">
          <i class="fa fa-user-pen"></i> Manage Account
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="logout.php">
          <i class="fa fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function updateDateTime() {
    const now = new Date();
    const formattedDateTime = now.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    document.getElementById('currentDateTime').textContent = formattedDateTime;
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>

<script>
  $('#manage_my_account').click(function(){
    uni_modal("Manage Account","manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own")
  })
</script>