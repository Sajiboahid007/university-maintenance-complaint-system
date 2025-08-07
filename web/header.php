<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>University Maintenance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5 CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- DataTables CSS -->
    <link
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />

    <style>
      body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      .wrapper {
        display: flex;
        flex: 1;
      }

      .sidebar {
        width: 250px;
        background-color: #343a40;
        min-height: 100vh;
        color: white;
      }

      .sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        display: block;
      }

      .sidebar a:hover {
        background-color: #495057;
      }

      .main {
        flex: 1;
        padding: 20px;
        background-color: #f8f9fa;
      }

      .navbar-brand {
        font-weight: bold;
      }

      table th, td  {
        text-align: center;
      }
    </style>
  </head>
  <body>