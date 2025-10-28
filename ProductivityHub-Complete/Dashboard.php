<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Productivity Hub</title>
  <!-- External CSS -->
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <!-- Sidebar Toggle Button -->
  <button class="toggle-btn" onclick="ProductivityHub.toggleSidebar()">☰</button>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="Logo RWD.jpeg" alt="Logo"> 
    <div class="menu">
      <a href="Dashboard.php"><button class="active">DASHBOARD</button></a>
      <a href="Task.php"><button>TASK</button></a>
      <a href="time-tracking.php"><button>TIME TRACKING</button></a>
      <a href="goals.php"><button>GOALS</button></a>
      <a href="collaboration.php"><button>COLLABORATION</button></a>
      <a href="analytics.php"><button>ANALYTICS</button></a>
      <a href="profile.php"><button>PROFILE</button></a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main">
    <!-- Header -->
    <div class="header">
      <h1>📊 Dashboard</h1>
      <div class="user-info">
        <div class="user-name" id="userNameDisplay">USER</div>
        <div class="avatar"></div>
      </div>
    </div>

    <!-- Database Task Table -->
    <div class="card">
      <h2>Task List</h2>

      <?php
      include("db_connect.php");

      // Database query
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

      // Close the connection
      mysqli_close($dbconn);
      ?>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
</body>
</html>

