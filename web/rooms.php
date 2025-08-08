<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Rooms List</h4>
        <button class="btn btn-primary" id="addRoomBtn">
            <i class="bi bi-plus-circle"></i> Add Room
        </button>
    </div>

    <table id="roomsTable" class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Unit</th>
                <th>Room No</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered"><!-- Larger modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roomForm">
                    <input type="hidden" id="roomId" name="Id">

                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <select id="UnitId" name="UnitId" class="form-select" required>
                            <option value="">Select Unit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room No</label>
                        <input type="text" id="RoomNo" name="RoomNo" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function () {
    let table = $('#roomsTable').DataTable({
        ajax: {
            url: 'http://localhost:3000/api/rooms',
            dataSrc: 'data'
        },
        columns: [
            { data: 'Id' },
            { data: 'Units.Name' }, // Adjust if your API structure differs
            { data: 'RoomNo' },
            {
                data: null,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-warning editRoom" data-id="${data.Id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger deleteRoom" data-id="${data.Id}"><i class="bi bi-trash"></i></button>
                    `;
                },

                orderable: false,
                searchable: false
            },
        ],
        order: [[0, 'desc']]
    });

    // Fetch Units for dropdown
    function loadUnits() {
        $.getJSON('http://localhost:3000/api/unit', function (data) {
            let options = '<option value="">Select Unit</option>';
            data.data.forEach(unit => {
                options += `<option value="${unit.Id}">${unit.Name}</option>`;
            });
            $('#UnitId').html(options);
        });
    }

    // Add Room
    $('#addRoomBtn').on('click', function () {
        $('#roomForm')[0].reset();
        $('#roomId').val('');
        loadUnits();
        $('#roomModal').modal('show');
    });

    // Edit Room
    $('#roomsTable').on('click', '.editRoom', function () {
        let id = $(this).data('id');
        $.getJSON(`http://localhost:3000/api/rooms/${id}`, function (response) {
            $('#roomModal').modal('show');
            $('#roomId').val(response.data.Id);
            $('#RoomNo').val(response.data.RoomNo);
            loadUnits();
            setTimeout(() => {
                $('#UnitId').val(response.data.UnitId);
            }, 200);
        });
    });

    // Delete Room
    $('#roomsTable').on('click', '.deleteRoom', function () {
        if (confirm('Are you sure you want to delete this room?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `http://localhost:3000/api/rooms/delete/${id}`,
                type: 'DELETE',
                success: function () {
                    table.ajax.reload();
                }
            });
        }
    });

    // Save Room
    $('#roomForm').on('submit', function (e) {
        e.preventDefault();
        let id = $('#roomId').val();
        let url = id ? `http://localhost:3000/api/rooms/update/${id}` : 'http://localhost:3000/api/rooms/insert';
        let method = id ? 'PUT' : 'POST';
        $.ajax({
            url: url,
            type: method,
            contentType: 'application/json',
            data: JSON.stringify({
                UnitId: $('#UnitId').val(),
                RoomNo: $('#RoomNo').val()
            }),
            success: function () {
                $('#roomModal').modal('hide');
                table.ajax.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
});
</script>


