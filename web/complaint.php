<?php
include 'header.php';
include 'sidebar.php';
?>

<style>
  #complaintsTable td, #complaintsTable th {
    text-align: center;
  }
  .status-pending { color: #ffc107; font-weight: bold; }
  .status-resolved { color: #28a745; font-weight: bold; }
  .status-in-progress { color: #17a2b8; font-weight: bold; }
  .status-rejected { color: #dc3545; font-weight: bold; }
</style>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">Complaints</h2>
    <button class="btn btn-primary" id="addComplaintBtn">
      <i class="bi bi-plus-lg"></i> Add Complaint
    </button>
  </div>

  <table id="complaintsTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
      <tr>
        <th>User</th>
        <th>Room</th>
        <th>Unit</th>
        <th>Device</th>
        <th>Description</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Add Complaint Modal -->
<div class="modal fade" id="addComplaintModal" tabindex="-1" aria-labelledby="addComplaintModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="addComplaintForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Complaint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Hidden input for logged-in user -->
          <input type="hidden" id="currentUserId" name="UserId" value="<?php echo $_SESSION['userId']; ?>">

          <div class="mb-3">
            <label class="form-label">Device</label>
            <select class="form-select" id="complaintDeviceId" name="DeviceId" required>
              <option value="">Select Device</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="complaintDescription" name="Description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="complaintStatus" name="Status" required>
              <option value="pending" selected>Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Complaint</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Edit Complaint Modal -->
<div class="modal fade" id="editComplaintModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="editComplaintForm">
      <div class="modal-content modal-xl">
        <div class="modal-header">
          <h5 class="modal-title">Edit Complaint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
           <input type="hidden" id="currentUserId" name="UserId" value="<?php echo $_SESSION['userId']; ?>">
          <input type="hidden" id="editComplaintId" name="Id" />
          <div class="mb-3">
            <label class="form-label">Device</label>
            <input type="text" id="editComplaintDevice" class="form-control" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="editComplaintDescription" name="Description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="editComplaintStatus" name="Status" required>
              <option value="pending">Pending</option>
              <option value="in-progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Complaint</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
  const API_BASE_URL = 'http://localhost:3000/api';
  const currentUserId = parseInt($('#currentUserId').val()); // logged-in user

  // Initialize DataTable
  const table = $('#complaintsTable').DataTable({
    ajax: {
      url: `${API_BASE_URL}/complaints`,
      dataSrc: 'data'
    },
    columns: [
      { data: 'Users', render: d => d ? d.Name : 'N/A' },
      { data: 'Devices.Rooms.RoomNo', defaultContent: 'N/A' },
      { data: 'Devices.Rooms.Units.Name', defaultContent: 'N/A' },
      { data: 'Devices', render: d => d ? `${d.Type} (${d.Identifier})` : 'N/A' },
      { data: 'Description', render: d => d ? (d.length > 50 ? d.substr(0,50)+'...' : d) : 'N/A' },
      { data: 'Status', render: d => `<span class="status-${d}">${d}</span>` },
      { data: 'CreatedAt', render: d => d ? new Date(d).toLocaleString() : 'N/A' },
      {
        data: null,
        render: function (data) {
          let buttons = '';
          if (data.UserId === currentUserId) {
            buttons += `<button class="btn btn-sm btn-warning editComplaint" data-id="${data.Id}">
                          <i class="bi bi-pencil"></i>
                        </button>`;
          }
          buttons += ` <button class="btn btn-sm btn-danger deleteComplaint" data-id="${data.Id}">
                          <i class="bi bi-trash"></i>
                        </button>`;
          return buttons;
        }
      }
    ],
    order: [[6, 'desc']]
  });

  // Load devices for Add/Edit dropdown
  function loadDevices(selectedId = null) {
    $.get(`${API_BASE_URL}/devices`, function(res) {
      let options = '<option value="">Select Device</option>';
      res.data.forEach(d => {
        options += `<option value="${d.Id}" ${d.Id === selectedId ? 'selected' : ''}>${d.Type} (${d.Identifier})</option>`;
      });
      $('#complaintDeviceId').html(options);
    });
  }

  // Show Add Modal
  $('#addComplaintBtn').click(function() {
    loadDevices();
    $('#addComplaintForm')[0].reset();
    $('#addComplaintModal').modal('show');
  });

  // Add Complaint
  $('#addComplaintForm').submit(function(e) {
    e.preventDefault();
    const data = {
      UserId: currentUserId,
      DeviceId: parseInt($('#complaintDeviceId').val()),
      Description: $('#complaintDescription').val(),
      Status: $('#complaintStatus').val()
    };

    if (!data.DeviceId) {
      alert('Please select a valid device.');
      return;
    }

    $.ajax({
      url: `${API_BASE_URL}/complaints/insert`,
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: res => {
        $('#addComplaintModal').modal('hide');
        table.ajax.reload();
        alert(res.message);
      },
      error: xhr => {
        alert('Failed to add complaint: ' + (xhr.responseJSON?.message || xhr.statusText));
      }
    });
  });

  // Edit Complaint
  $('#complaintsTable').on('click', '.editComplaint', function() {
    const id = $(this).data('id');
    $.get(`${API_BASE_URL}/complaints/get/${id}`, function(res) {
      const c = res.data;
      $('#editComplaintId').val(c.Id);

      // Safe device text
      const deviceText = c.Devices
        ? `${c.Devices.Type || 'N/A'} (${c.Devices.Identifier || 'N/A'})` +
          ` - Room ${c.Devices.Rooms?.RoomNo || 'N/A'} / ${c.Devices.Rooms?.Units?.Name || 'N/A'}`
        : 'N/A';

      $('#editComplaintDevice').val(deviceText);
      $('#editComplaintDescription').val(c.Description || '');
      $('#editComplaintStatus').val(c.Status || 'pending');
      $('#editComplaintModal').modal('show');
    });
  });

  // Update Complaint
  $('#editComplaintForm').submit(function(e) {
    e.preventDefault();
    const id = parseInt($('#editComplaintId').val());
    const data = {
      Description: $('#editComplaintDescription').val(),
      Status: $('#editComplaintStatus').val()
    };

    $.ajax({
      url: `${API_BASE_URL}/complaints/update/${id}`,
      type: 'PUT',
      contentType: 'application/json',
      data: JSON.stringify(data),
      success: res => {
        $('#editComplaintModal').modal('hide');
        table.ajax.reload();
        alert(res.message);
      },
      error: xhr => {
        alert('Failed to update complaint: ' + (xhr.responseJSON?.message || xhr.statusText));
      }
    });
  });

  // Delete Complaint
  $('#complaintsTable').on('click', '.deleteComplaint', function() {
    if (!confirm('Are you sure you want to delete this complaint?')) return;
    const id = $(this).data('id');
    $.ajax({
      url: `${API_BASE_URL}/complaints/delete/${id}`,
      type: 'DELETE',
      success: res => {
        table.ajax.reload();
        alert(res.message);
      },
      error: xhr => {
        alert('Failed to delete complaint: ' + (xhr.responseJSON?.message || xhr.statusText));
      }
    });
  });
});
</script>


