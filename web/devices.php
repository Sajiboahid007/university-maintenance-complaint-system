<?php
include 'header.php';
include 'sidebar.php';
?>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Devices List</h4>
    <button class="btn btn-primary" id="addDeviceBtn">
      <i class="bi bi-plus-circle"></i> Add Device
    </button>
  </div>

  <table id="devicesTable" class="table table-bordered table-striped text-center">
    <thead>
      <tr>
        <th>ID</th>
        <th>Room</th>
        <th>Type</th>
        <th>Identifier</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"><!-- Extra large modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deviceModalLabel">Add/Edit Device</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="deviceForm">
          <input type="hidden" id="deviceId" name="Id" />

          <div class="mb-3">
            <label for="RoomId" class="form-label">Room</label>
            <select id="RoomId" name="RoomId" class="form-select" required>
              <option value="">Select Room</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="Type" class="form-label">Type</label>
            <input type="text" id="Type" name="Type" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="Identifier" class="form-label">Identifier</label>
            <input type="text" id="Identifier" name="Identifier" class="form-control" />
          </div>

          <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <input type="text" id="Status" name="Status" class="form-control" required />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="deviceForm" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
  let table = $('#devicesTable').DataTable({
    ajax: {
      url: 'http://localhost:3000/api/devices',
      dataSrc: 'data'
    },
    columns: [
      { data: 'Id' },
      { data: 'Rooms.RoomNo' }, // assuming your API returns nested Rooms object with RoomNo
      { data: 'Type' },
      { data: 'Identifier' },
      { data: 'Status' },
      {
        data: null,
        render: function (data) {
          return `
            <button class="btn btn-sm btn-outline-success editDevice" data-id="${data.Id}"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-danger deleteDevice" data-id="${data.Id}"><i class="bi bi-trash"></i></button>
          `;
        }
      }
    ]
  });

  // Load Rooms for dropdown
  function loadRooms(selectedRoomId = null) {
    $.getJSON('http://localhost:3000/api/rooms', function (data) {
      let options = '<option value="">Select Room</option>';
      data.data.forEach(room => {
        options += `<option value="${room.Id}" ${room.Id === selectedRoomId ? 'selected' : ''}>${room.RoomNo}</option>`;
      });
      $('#RoomId').html(options);
    });
  }

  // Show add modal
  $('#addDeviceBtn').on('click', function () {
    $('#deviceForm')[0].reset();
    $('#deviceId').val('');
    loadRooms();
    $('#deviceModal').modal('show');
  });

  // Edit device
  $('#devicesTable').on('click', '.editDevice', function () {
    let id = $(this).data('id');
    $.getJSON(`http://localhost:3000/api/devices/get/${id}`, function (response) {
      $('#deviceId').val(response.data.Id);
      $('#Type').val(response.data.Type);
      $('#Identifier').val(response.data.Identifier);
      $('#Status').val(response.data.Status);
      loadRooms(response.data.RoomId);
      $('#deviceModal').modal('show');
    });
  });

  // Delete device
  $('#devicesTable').on('click', '.deleteDevice', function () {
    if (!confirm('Are you sure you want to delete this device?')) return;

    let id = $(this).data('id');
    $.ajax({
      url: `http://localhost:3000/api/devices/delete/${id}`,
      type: 'DELETE',
      success: function () {
        table.ajax.reload();
        alert('Device deleted successfully');
      },
      error: function () {
        alert('Failed to delete device');
      }
    });
  });

  // Save device (add or update)
  $('#deviceForm').on('submit', function (e) {
    e.preventDefault();
    let id = $('#deviceId').val();
    let url = id ? `http://localhost:3000/api/devices/update/${id}` : 'http://localhost:3000/api/devices/insert';
    let method = id ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      type: method,
      contentType: 'application/json',
      data: JSON.stringify({
        RoomId: parseInt($('#RoomId').val()),
        Type: $('#Type').val(),
        Identifier: $('#Identifier').val() || NULL,
        Status: $('#Status').val()
      }),
      success: function () {
        $('#deviceModal').modal('hide');
        table.ajax.reload();
        alert(id ? 'Device updated successfully' : 'Device added successfully');
      },
      error: function (xhr) {
        alert('Failed to save device: ' + (xhr.responseJSON?.message || xhr.statusText));
      }
    });
  });
});
</script>
