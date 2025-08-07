<?php include('./header.php') ?>
<?php include('./sidebar.php') ?>

      <!-- Main Content -->
      <div class="main">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="mb-0">Complaints List</h2>
          <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addComplaintModal"
          >
            <i class="bi bi-plus-circle me-1"></i> Add Complaint
          </button>
        </div>

        <table id="complaintsTable" class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Device</th>
              <th>Description</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Sample Data -->
            <tr>
              <td>1</td>
              <td>John Doe</td>
              <td>Fan - Room 101</td>
              <td>Fan not working</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td>2025-08-05</td>
              <td>
                <button
                  class="btn btn-sm btn-outline-primary me-1"
                  data-bs-toggle="tooltip"
                  title="Edit"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="tooltip"
                  title="Delete"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Jane Smith</td>
              <td>Projector - Room 204</td>
              <td>No signal from HDMI</td>
              <td><span class="badge bg-primary">In Progress</span></td>
              <td>2025-08-06</td>
              <td>
                <button
                  class="btn btn-sm btn-outline-primary me-1"
                  data-bs-toggle="tooltip"
                  title="Edit"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="tooltip"
                  title="Delete"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>Sajib Oahid</td>
              <td>Light - Room 303</td>
              <td>Light flickering</td>
              <td><span class="badge bg-success">Resolved</span></td>
              <td>2025-08-07</td>
              <td>
                <button
                  class="btn btn-sm btn-outline-primary me-1"
                  data-bs-toggle="tooltip"
                  title="Edit"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="tooltip"
                  title="Delete"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

<?php include('./footer.php') ?>