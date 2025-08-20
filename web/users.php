<?php
include 'header.php';
include 'sidebar.php';
?>

<style>
  #usersTable td, #usersTable th {
    text-align: center;
  }
</style>

<div class="container mt-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Manage Users</h4>
    <button class="btn btn-primary" id="addUserBtn">Add User</button>
  </div>

  <table id="usersTable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Phone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm">
        <div class="modal-header">
          <h5 class="modal-title">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="userId">

          <div class="mb-3">
            <label>Name</label>
            <input type="text" id="name" name="Name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" id="email" name="Email" class="form-control" required>
          </div>

          <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="Role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="technicians">Technicians</option>
                </select>
          </div>

          <div class="mb-3">
            <label>Phone</label>
            <input type="text" id="phone" name="Phone" class="form-control">
          </div>

          <div class="mb-3 password-field">
            <label>Password</label>
            <input type="password" id="password" name="Password" class="form-control" required>
          </div>


        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
  const apiUrl = "http://localhost:3000/api/users";

  // Load users
  function loadUsers() {
    $.get(apiUrl, function (data) {
      let rows = "";
      data.data.forEach(user => {
        rows += `
          <tr>
            <td>${user.Name}</td>
            <td>${user.Email}</td>
            <td>${user.Role}</td>
            <td>${user.Phone ?? ""}</td>
            <td>
              <button class="btn btn-warning btn-sm editUser" data-id="${user.Id}"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-danger btn-sm deleteUser" data-id="${user.Id}"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `;
      });
      $("#usersTable tbody").html(rows);
    });
  }
  loadUsers();

  // Open modal for adding
  $("#addUserBtn").click(function () {
    $("#userId").val("");
    $("#userForm")[0].reset();
    $(".modal-title").text("Add User");
     $(".password-field").show();               // SHOW password field
    $("#password").attr("required", true);
    $("#userModal").modal("show");
  });

  // Save user
  $("#userForm").submit(function (e) {
    e.preventDefault();
    const id = $("#userId").val();
    const formData = {
      Name: $("#name").val(),
      Email: $("#email").val(),
      Role: $("#role").val(),
      Phone: $("#phone").val(),
      Password: $("#password").val()
    };

    if (id) {
      $.ajax({
        url: `${apiUrl}/update/${id}`,
        type: "PUT",
        contentType: "application/json",
        data: JSON.stringify(formData),
        success: function () {
          $("#userModal").modal("hide");
          loadUsers();
        }
      });
    } else {
      $.ajax({
        url: `${apiUrl}/insert`,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(formData),
        success: function () {
          $("#userModal").modal("hide");
          loadUsers();
        }
      });
    }
  });

  // Edit user
  $(document).on("click", ".editUser", function () {
    const id = $(this).data("id");
    $.get(`${apiUrl}/get/${id}`, function (user) {
      $("#userId").val(user.data.Id);
      $("#name").val(user.data.Name);
      $("#email").val(user.data.Email);
      $("#role").val(user.data.Role);
      $("#phone").val(user.data.Phone); // Password not fetched for security
      $(".modal-title").text("Edit User");
      $(".password-field").hide();  
      $("#userModal").modal("show");
    });
  });

  // Delete user
  $(document).on("click", ".deleteUser", function () {
    if (confirm("Are you sure?")) {
      const id = $(this).data("id");
      $.ajax({
        url: `${apiUrl}/delete/${id}`,
        type: "DELETE",
        success: function () {
          loadUsers();
        }
      });
    }
  });

});
</script>
