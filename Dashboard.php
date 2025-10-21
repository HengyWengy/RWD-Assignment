<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Productivity Hub</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      display: flex;
      height: 100vh;
    }
    .toggle-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 18px;
        cursor: pointer;
        z-index: 1000;
    }

    .toggle-btn:hover {
        background: #0056b3;
    }
    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #0b111a;
      color: #fff;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      box-sizing: border-box;
      transition: width 0.3s ease, opacity 0.3s ease;
    }
    .sidebar.collapsed {
      width: 0;
      opacity: 0;
      overflow: hidden;
      padding: 0;
    }
    .sidebar img {
      width: 120px;
      margin-bottom: 30px;
    }
    .menu {
      display: flex;
      flex-direction: column;
      gap: 15px;
      width: 100%;
      padding: 0 15px;
    }
    .menu button {
      background: #007bff;
      border: none;
      color: #fff;
      padding: 12px 15px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      text-align: left;
      width: 100%;
      box-sizing: border-box;
    }
    .menu button:hover {
      background: #0056b3;
    }
    /* Main content */
    .main {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      transition: margin-left 0.3s ease;
    }
    .sidebar.collapsed + .main {
      margin-left: 0;
    }
    /* Header */
    .header {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }
    .user-name {
      font-weight: bold;
      font-size: 18px;
    }
    .avatar {
      width: 40px;
      height: 40px;
      background: #007bff;
      border-radius: 50%;
    }
    /* Dashboard sections */
    .dashboard-section {
      margin-bottom: 20px;
    }
    .task-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .task-table th, .task-table td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .task-table th {
      background: #007bff;
      color: #fff;
    }
    .progress-circle {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: conic-gradient(#007bff calc(var(--progress) * 1%), #e6e6e6 0%);
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      margin: 6px auto;
      box-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }

    .progress-text {
      position: absolute;
      font-weight: 700;
      font-size: 14px;
      color: #111;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
   <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
  <div class="sidebar">
    <img src="Logo RWD.jpeg" alt="Logo"> 
    <div class="menu">
       <a href="Task.php"><button>TASK</button></a>
      <a href="time-tracking.php"><button>TIME TRACKING</button></a>
      <a href="goals.php"><button>GOALS</button></a>
      <a href="collaboration.php"><button>COLLABORATION</button></a>
      <a href="analytics.php"><button>ANALYTICS</button></a>
      <a href="profile.php"><button>PROFILE</button></a>
    </div>
  </div>
  <!-- Main -->
  <div class="main">
    <!-- Header -->
    <div class="header">
      <!-- give this element an id so JS can update it -->
      <div class="user-name" id="userNameDisplay">USER</div>
      <div class="avatar"></div>
    </div>

    <!-- Database Task Table -->
     <div class="content">
      <h2>Task List</h2>

      <?php
      include("db_connect.php");

      //Database query
      $sql = "SELECT * FROM tasks";
      $result = mysqli_query($dbconn, $sql);

      if (!$result) {
        echo "<p>Error retrieving data: " . mysqli_error($dbconn) . "</p>";
      } else {
        if (mysqli_num_rows($result) > 0) {
          echo "<table class='task-table'>
                  <tr>
                    <th>Task</th>
                    <th>Owner</th>
                    <th>Timeline</th>
                    <th>Status</th>
                    <th>Time Tracking</th>
                    <th>Progress</th>
                  </tr>";
          while ($row = mysqli_fetch_assoc($result)) {
            $progress = isset($row['progress']) ? (int)$row['progress'] : 0;
            if ($progress < 0) $progress = 0;
            if ($progress > 100) $progress = 100;
           
            $task = htmlspecialchars($row['task'] ?? '');
    $owner = htmlspecialchars($row['owner'] ?? '');
    $timeline = htmlspecialchars($row['timeline'] ?? '');
    $status = htmlspecialchars($row['status'] ?? '');
    $time_tracking = htmlspecialchars($row['time_tracking'] ?? '');
    $progress = htmlspecialchars($progress ?? '');
    echo "<tr>
            <td>{$task}</td>
            <td>{$owner}</td>
            <td>{$timeline}</td>
            <td>{$status}</td>
            <td>{$time_tracking}</td>
            <td>
              <div class='progress-circle' style='
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: conic-gradient(#007bff {$progress}%, #ddd {$progress}%);
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 14px;
                font-weight: bold;
                color: #333;
              '>
                {$progress}%
              </div>
            </td>
          </tr>";
          }
          echo "</table>";
        } else {
          echo "<p>No tasks found.</p>";
        }
      }

      //Close the connection
      mysqli_close($dbconn);
      ?>
    </div>

    <script src="js/script.js"></script>
  </div>
</body>
<script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
    }
  document.addEventListener('DOMContentLoaded', function () {
      try {
        var stored = localStorage.getItem('username');
        if (stored) {
          var el = document.getElementById('userNameDisplay');
          if (el) el.textContent = stored;
        }
      } catch (e) {
        console.error('Failed to read username from localStorage', e);
      }
    });
</script>
</html>

