<?php
include 'header.php';
include 'sidebar.php';
?>

<div class="main">
  <h4>Complaint Logs</h4>

  <table id="complaintLogsTable" class="table table-bordered table-striped text-center">
    <thead>
      <tr>
        <th>Room</th>
        <th>Unit</th>
        <th>Device</th>
        <th>Description</th>
        <th>Status</th>
        <th>Complaint Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Edit Complaint Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="editStatusForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Complaint Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editComplaintId" name="Id" />
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
          <button type="submit" class="btn btn-primary">Update Status</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
  const API_BASE_URL = 'http://localhost:3000/api';
  const currentUserId = parseInt($('#currentUserId').val()); // make sure you have this hidden input

  const table = $('#complaintLogsTable').DataTable({
    ajax: {
      url: `${API_BASE_URL}/complaintLogs`,
      dataSrc: 'data'
    },
    columns: [
      { data: 'Complaints.Devices.Rooms.RoomNo', defaultContent: 'N/A' },
      { data: 'Complaints.Devices.Rooms.Units.Name', defaultContent: 'N/A' },
      { 
        data: 'Complaints.Devices',
        render: d => d ? `${d.Type} (${d.Identifier})` : 'N/A'
      },
      { data: 'Complaints.Description', defaultContent: 'N/A' },
      { 
        data: 'Complaints.Status', 
        render: d => `<span class="status-${d}">${d}</span>` 
      },
      { 
        data: 'Complaints.CreatedAt', 
        render: d => d ? new Date(d).toLocaleString() : 'N/A' 
      },
      {
        data: null,
        render: function (data) {
          // Only show edit button if current user owns the complaint
          let buttons = '';
          if (data.Complaints.UserId === currentUserId) {
            buttons += `<button class="btn btn-sm btn-warning editStatus" data-id="${data.Complaints.Id}">
                          <i class="bi bi-pencil"></i>
                        </button>`;
          }
          return buttons;
        }
      }
    ],
    order: [[5, 'desc']]
  });

  // Edit status button click
  $('#complaintLogsTable').on('click', '.editStatus', function () {
    const id = $(this).data('id');
    $.get(`${API_BASE_URL}/complaints/get/${id}`, function(res) {
      const c = res.data;
      $('#editComplaintId').val(c.Id);
      $('#editComplaintStatus').val(c.Status);
      $('#editStatusModal').modal('show');
    });
  });

  // Update status form submit
  $('#editStatusForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editComplaintId').val();
    const status = $('#editComplaintStatus').val();
    $.ajax({
      url: `${API_BASE_URL}/complaints/update/${id}`,
      type: 'PUT',
      contentType: 'application/json',
      data: JSON.stringify({ Status: status }),
      success: function() {
        $('#editStatusModal').modal('hide');
        table.ajax.reload();
        alert('Complaint status updated successfully');
      },
      error: function(xhr) {
        alert('Failed to update status: ' + (xhr.responseJSON?.message || xhr.statusText));
      }
    });
  });
});
</script>

