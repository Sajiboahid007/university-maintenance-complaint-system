<?php
include 'header.php';
include 'sidebar.php';
?>

<style>
  #unitsTable td, #unitsTable th {
    text-align: center;
  }
</style>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">Units List</h2>
    <button class="btn btn-success" id="addUnitBtn">
      <i class="bi bi-plus-lg"></i> Add Unit
    </button>
  </div>

  <table id="unitsTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
      <tr>
        <th>Id</th>
        <th>Level</th>
        <th>Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-xl modal-dialog-centered">
    <form id="addUnitForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUnitModalLabel">Add New Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="addUnitLevelId" class="form-label">Level</label>
            <select class="form-select" id="addUnitLevelId" name="LevelId" required>
              <option value="">Select Level</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="addUnitName" class="form-label">Unit Name</label>
            <input type="text" class="form-control" id="addUnitName" name="Name" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Unit</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="editUnitForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editUnitId" name="Id" />
          <div class="mb-3">
            <label for="editUnitLevelId" class="form-label">Level</label>
            <select class="form-select" id="editUnitLevelId" name="LevelId" required>
              <option value="">Select Level</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editUnitName" class="form-label">Unit Name</label>
            <input type="text" class="form-control" id="editUnitName" name="Name" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
  $(document).ready(function () {
    // Load levels for select dropdowns
    function loadLevels(selectElement) {
      $.ajax({
        url: 'http://localhost:3000/api/levels',
        method: 'GET',
        success: function (res) {
          const data = res.data || [];
          selectElement.empty();
          selectElement.append('<option value="">Select Level</option>');
          data.forEach(level => {
            selectElement.append(`<option value="${level.Id}">${level.Name}</option>`);
          });
        },
        error: function () {
          alert('Failed to load levels');
        }
      });
    }

    loadLevels($('#addUnitLevelId'));
    loadLevels($('#editUnitLevelId'));

    const table = $('#unitsTable').DataTable({
      ajax: {
        url: 'http://localhost:3000/api/unit',
        dataSrc: 'data'
      },
      columns: [
        { data: 'Id' },
        { data: 'Levels.Name', defaultContent: 'N/A' }, // assuming backend joins level info as Level.Name
        { data: 'Name' },
        {
          data: null,
          render: function (data, type, row) {
            return `
              <button class="btn btn-sm btn-info edit-btn" 
                      data-id="${row.Id}" 
                      data-name="${row.Name}" 
                      data-levelid="${row.LevelId}"
                      title="Edit">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-danger delete-btn" data-id="${row.Id}" title="Delete">
                <i class="bi bi-trash"></i>
              </button>
            `;
          },
          orderable: false,
          searchable: false
        }
      ],
      order: [[0, 'desc']]
    });

    // Show Add Modal
    $('#addUnitBtn').click(() => {
      $('#addUnitName').val('');
      $('#addUnitLevelId').val('');
      const addModal = new bootstrap.Modal(document.getElementById('addUnitModal'));
      addModal.show();
    });

    // Handle Add Unit submit
    $('#addUnitForm').submit(function (e) {
      e.preventDefault();
      const data = {
        Name: $('#addUnitName').val(),
        LevelId: Number($('#addUnitLevelId').val())
      };

      $.ajax({
        url: 'http://localhost:3000/api/unit/insert',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (response) {
          $('#addUnitModal').modal('hide');
          table.ajax.reload(null, false);
          alert(response.message);
        },
        error: function (xhr) {
          alert('Add failed: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
      });
    });

    // Open edit modal and populate data
    $('#unitsTable tbody').on('click', '.edit-btn', function () {
      const id = $(this).data('id');
      const name = $(this).data('name');
      const levelId = $(this).data('levelid');

      $('#editUnitId').val(id);
      $('#editUnitName').val(name);
      $('#editUnitLevelId').val(levelId);

      const editModal = new bootstrap.Modal(document.getElementById('editUnitModal'));
      editModal.show();
    });

    // Handle edit form submit
    $('#editUnitForm').submit(function (e) {
      e.preventDefault();

      const id = $('#editUnitId').val();
      const data = {
        Name: $('#editUnitName').val(),
        LevelId: Number($('#editUnitLevelId').val())
      };

      $.ajax({
        url: `http://localhost:3000/api/unit/update/${id}`,
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function (response) {
          $('#editUnitModal').modal('hide');
          table.ajax.reload(null, false);
          alert(response.message);
        },
        error: function (xhr) {
          alert('Update failed: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
      });
    });

    // Handle delete button click
    $('#unitsTable tbody').on('click', '.delete-btn', function () {
      if (!confirm('Are you sure you want to delete this unit?')) return;

      const id = $(this).data('id');

      $.ajax({
        url: `http://localhost:3000/api/unit/delete/${id}`,
        type: 'DELETE',
        success: function (response) {
          table.ajax.reload(null, false);
          alert(response.message);
        },
        error: function (xhr) {
          alert('Delete failed: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
      });
    });
  });
</script>
