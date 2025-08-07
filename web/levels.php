<?php
include 'header.php';
include 'sidebar.php';
?>

<style>
  #levelsTable td, #levelsTable th {
    text-align: center;
  }
</style>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0">Levels List</h2>
    <button class="btn btn-success" id="addLevelBtn">
      <i class="bi bi-plus-lg"></i> Add Level
    </button>
  </div>

  <table id="levelsTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- Add Level Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-labelledby="addLevelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="addLevelForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addLevelModalLabel">Add New Level</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="addLevelName" class="form-label">Level Name</label>
            <input type="text" class="form-control" id="addLevelName" name="Name" required />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Level</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Level Modal -->
<div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="editLevelForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editLevelModalLabel">Edit Level</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editLevelId" name="Id" />
          <div class="mb-3">
            <label for="editLevelName" class="form-label">Name</label>
            <input type="text" class="form-control" id="editLevelName" name="Name" required />
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
    const table = $('#levelsTable').DataTable({
      ajax: {
        url: 'http://localhost:3000/api/levels',
        dataSrc: 'data'
      },
      columns: [
        { data: 'Id' },
        { data: 'Name' },
        {
          data: null,
          render: function (data, type, row) {
            return `
              <button class="btn btn-sm btn-info edit-btn" data-id="${row.Id}" data-name="${row.Name}" title="Edit">
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
    $('#addLevelBtn').click(() => {
      $('#addLevelName').val('');
      const addModal = new bootstrap.Modal(document.getElementById('addLevelModal'));
      addModal.show();
    });

    // Handle Add Level submit
    $('#addLevelForm').submit(function (e) {
      e.preventDefault();
      const name = $('#addLevelName').val();

      $.ajax({
        url: 'http://localhost:3000/api/levels/insert',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ Name: name }),
        success: function (response) {
          $('#addLevelModal').modal('hide');
          table.ajax.reload(null, false);
          alert(response.message);
        },
        error: function (xhr) {
          alert('Add failed: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
      });
    });

    // Open edit modal and populate data
    $('#levelsTable tbody').on('click', '.edit-btn', function () {
      const id = $(this).data('id');
      const name = $(this).data('name');

      $('#editLevelId').val(id);
      $('#editLevelName').val(name);
      const editModal = new bootstrap.Modal(document.getElementById('editLevelModal'));
      editModal.show();
    });

    // Handle edit form submit
    $('#editLevelForm').submit(function (e) {
      e.preventDefault();

      const id = $('#editLevelId').val();
      const name = $('#editLevelName').val();

      $.ajax({
        url: `http://localhost:3000/api/levels/update/${id}`,
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({ Name: name }),
        success: function (response) {
          $('#editLevelModal').modal('hide');
          table.ajax.reload(null, false);
          alert(response.message);
        },
        error: function (xhr) {
          alert('Update failed: ' + (xhr.responseJSON?.message || xhr.statusText));
        }
      });
    });

    // Handle delete button click
    $('#levelsTable tbody').on('click', '.delete-btn', function () {
      if (!confirm('Are you sure you want to delete this level?')) return;

      const id = $(this).data('id');

      $.ajax({
        url: `http://localhost:3000/api/levels/delete/${id}`,
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
