 <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">UniRepair</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#topNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link active" href="#">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Complaints</a>
            </li>
             
            <li class="nav-item">
               <!-- Topbar with greeting and dropdown -->
                <div id="topbar" class="d-flex align-items-center">
                  <div class="dropdown">
                    <button
                      id="userDropdown"
                      class="btn btn-link dropdown-toggle text-white"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false"
                    >
                      Hello, <span id="topbarUsername">User</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                      <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                      <li><hr class="dropdown-divider" /></li>
                      <li><a href="./index.php" class="dropdown-item text-danger">Logout</a></li>
                    </ul>
                  </div>
                </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Wrapper -->
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar">
        <h5 class="p-3">Menu</h5>
        <a href="dashboard.php">Dashboard</a>
        <a href="./levels.php">Levels</a>
        <a href="./units.php">Units</a>
        <a href="./rooms.php">Rooms</a>
        <a href="./devices.php">Devices</a>
        <a href="./technicians.php">Technicians</a>
        <a href="./users.php">Users</a>
        <a href="./complaint.php">Complaint</a>
        <a href="./complaintLogs.php">Complaint Logs</a>
        <a href="#">Settings</a>
      </div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const userInfoStr = localStorage.getItem("userInfo");
    
    if (userInfoStr) {
      const userInfo = JSON.parse(userInfoStr); // convert back to object
      if (userInfo.name) {
        document.getElementById("topbarUsername").textContent = userInfo.name;
      }
    }
  });
</script>

