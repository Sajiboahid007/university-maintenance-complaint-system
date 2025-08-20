<?php
include 'header.php';
include 'sidebar.php';
?>

<style>
  #techniciansTable td, #techniciansTable th {
    text-align: center;
  }
</style>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">Technicians List</h2>
    <button class="btn btn-success" id="addTechnicianBtn">
      <i class="bi bi-plus-lg"></i> Add Technician
    </button>
  </div>

  <table id="techniciansTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Assigned Area</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="technicianModal" tabindex="-1" aria-labelledby="technicianModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"><!-- modal-xl for extra large -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="technicianModalLabel">Add/Edit Technician</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="technicianForm">
          <input type="hidden" id="technicianId" name="Id" />

          <div class="mb-3">
            <label for="technicianName" class="form-label">Name</label>
            <input type="text" id="technicianName" name="Name" class="form-control" required />
          </div>

          <div class="mb-3">
            <label for="technicianPhone" class="form-label">Phone</label>
            <input type="text" id="technicianPhone" name="Phone" class="form-control" />
          </div>

          <div class="mb-3">
            <label for="technicianArea" class="form-label">Assigned Area</label>
            <input type="text" id="technicianArea" name="AssignedArea" class="form-control" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="technicianForm" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<?php include 'footer.php'; ?>

<script>
$(function () {
  const API_BASE = 'http://localhost:3000';

  const table = $('#techniciansTable').DataTable({
    ajax: {
      url: `${API_BASE}/api/technicians`,
      dataSrc: function (json) {
        if (!json) return [];
        return json.data || json;
      },
      error: function(xhr) {
        console.error('Failed to load technicians:', xhr.responseText);
        alert('Failed to load technicians data.');
      }
    },
    columns: [
      { data: 'Id' },
      { data: 'Name' },
      { data: 'Phone' },
      { data: 'AssignedArea' },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (row) {
          return `
            <button class="btn btn-sm btn-info edit-btn me-1" data-id="${row.Id}">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.Id}">
              <i class="bi bi-trash"></i>
            </button>
          `;
        }
      }
    ],
    order: [[0, 'desc']]
  });

  // Open Add modal
  $('#addTechnicianBtn').on('click', function () {
    $('#technicianForm')[0].reset();
    $('#technicianId').val('');
    $('#technicianModalLabel').text('Add Technician');
    const modal = new bootstrap.Modal(document.getElementById('technicianModal'));
    modal.show();
  });

  // Open Edit modal and fill data
  $('#techniciansTable tbody').on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    $.getJSON(`${API_BASE}/api/technicians/get/${id}`)
      .done(function (res) {
        const tech = res.data || res;
        if (!tech) return alert('Technician data not found.');

        $('#technicianId').val(tech.Id);
        $('#technicianName').val(tech.Name);
        $('#technicianPhone').val(tech.Phone || '');
        $('#technicianArea').val(tech.AssignedArea || '');

        $('#technicianModalLabel').text('Edit Technician');
        const modal = new bootstrap.Modal(document.getElementById('technicianModal'));
        modal.show();
      })
      .fail(function () {
        alert('Failed to fetch technician data.');
      });
  });

  // Delete
  $('#techniciansTable tbody').on('click', '.delete-btn', function () {
    const id = $(this).data('id');
    if (!confirm('Are you sure you want to delete this technician?')) return;

    $.ajax({
      url: `${API_BASE}/api/technicians/delete/${id}`,
      type: 'DELETE'
    }).done(function (res) {
      alert(res.message || 'Deleted');
      table.ajax.reload(null, false);
    }).fail(function () {
      alert('Delete failed.');
    });
  });

  // Save (Add or Update)
  $('#technicianForm').on('submit', function (e) {
    e.preventDefault();

    const id = $('#technicianId').val();
    const data = {
      Name: $('#technicianName').val().trim(),
      Phone: $('#technicianPhone').val().trim(),
      AssignedArea: $('#technicianArea').val().trim()
    };

    if (!data.Name) {
      return alert('Name is required.');
    }

    const url = id ? `${API_BASE}/api/technicians/update/${id}` : `${API_BASE}/api/technicians/insert`;
    const method = id ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      type: method,
      contentType: 'application/json',
      data: JSON.stringify(data)
    }).done(function (res) {
      alert(res.message || (id ? 'Updated' : 'Created'));
      $('#technicianModal').modal('hide');
      table.ajax.reload(null, false);
    }).fail(function (xhr) {
      const err = xhr.responseJSON?.message || 'Save failed';
      alert(err);
    });
  });
});
</script>
