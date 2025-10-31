<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Goals - Productivity Hub</title>
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
      <a href="Dashboard.php"><button>DASHBOARD</button></a>
      <a href="Task.php"><button>TASK</button></a>
      <a href="time-tracking.php"><button>TIME TRACKING</button></a>
      <a href="goals.php"><button class="active">GOALS</button></a>
      <a href="collaboration.php"><button>COLLABORATION</button></a>
      <a href="analytics.php"><button>ANALYTICS</button></a>
      <a href="profile.php"><button>PROFILE</button></a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main">
    <!-- Header -->
    <div class="header">
      <h1>🎯 Goal Setting</h1>
      <div class="user-info">
        <div class="user-name" id="userNameDisplay">USER</div>
        <div class="avatar"></div>
      </div>
    </div>

    <!-- Goals Section -->
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Your Goals</h2>
        <a href="Task.php" class="btn btn-primary">➕ Add Goal (Create Task)</a>
      </div>

      <table class="task-table" id="goalsTable" aria-live="polite">
        <thead>
          <tr>
            <th>Goal</th>
            <th>Type</th>
            <th>Deadline</th>
            <th>Owner</th>
            <th>Status</th>
            <th>Progress</th>
          </tr>
        </thead>
        <tbody id="goalsBody"></tbody>
      </table>

      <div id="empty" class="text-center" style="display:none; padding: 40px; color: #999;">
        <p style="font-size: 18px;">📝 No goals yet.</p>
        <p>Add tasks on the Tasks page and they will appear here as goals.</p>
      </div>
    </div>

    <!-- Goal Statistics -->
    <div class="card">
      <h2>📊 Goal Statistics</h2>
      <div id="goalStats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <!-- Stats will be populated by JavaScript -->
      </div>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
  
  <script>
    // Render goals with enhanced information
    function renderGoals() {
      const list = ProductivityHub.getTasks();
      const body = document.getElementById('goalsBody');
      const empty = document.getElementById('empty');
      
      body.innerHTML = '';
      
      if (!list.length) {
        empty.style.display = 'block';
        document.getElementById('goalsTable').style.display = 'none';
        return;
      }
      
      empty.style.display = 'none';
      document.getElementById('goalsTable').style.display = 'table';
      
      list.forEach(t => {
        const tr = document.createElement('tr');
        
        // Determine status color
        let statusColor = '#6c757d';
        if (t.status === 'Completed') statusColor = '#28a745';
        else if (t.status === 'In Progress') statusColor = '#007bff';
        else if (t.status === 'Pending') statusColor = '#ffc107';
        
        tr.innerHTML = `
          <td><strong>${ProductivityHub.escapeHtml(t.title)}</strong></td>
          <td>${ProductivityHub.escapeHtml(t.type)}</td>
          <td>${ProductivityHub.formatDate(t.deadline)}</td>
          <td>${ProductivityHub.escapeHtml(t.owner || '-')}</td>
          <td>
            <span style="background: ${statusColor}; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">
              ${ProductivityHub.escapeHtml(t.status || 'Pending')}
            </span>
          </td>
          <td>
            <div style="width: 100%; background: #e9ecef; border-radius: 10px; height: 24px; overflow: hidden;">
              <div style="width: ${t.progress || 0}%; background: ${statusColor}; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold; transition: width 0.3s ease;">
                ${t.progress || 0}%
              </div>
            </div>
          </td>
        `;
        body.appendChild(tr);
      });
      
      // Update statistics
      updateGoalStats(list);
    }

    // Update goal statistics
    function updateGoalStats(tasks) {
      const statsDiv = document.getElementById('goalStats');
      if (!statsDiv) return;

      const stats = ProductivityHub.getProductivityStats();
      
      const statsHTML = `
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border-left: 4px solid #007bff;">
          <div style="font-size: 32px; font-weight: bold; color: #007bff;">${stats.total}</div>
          <div style="color: #666; margin-top: 5px;">Total Goals</div>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border-left: 4px solid #28a745;">
          <div style="font-size: 32px; font-weight: bold; color: #28a745;">${stats.completed}</div>
          <div style="color: #666; margin-top: 5px;">Completed</div>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border-left: 4px solid #ffc107;">
          <div style="font-size: 32px; font-weight: bold; color: #ffc107;">${stats.inProgress}</div>
          <div style="color: #666; margin-top: 5px;">In Progress</div>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border-left: 4px solid #17a2b8;">
          <div style="font-size: 32px; font-weight: bold; color: #17a2b8;">${stats.averageProgress}%</div>
          <div style="color: #666; margin-top: 5px;">Avg Progress</div>
        </div>
      `;
      
      statsDiv.innerHTML = statsHTML;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', renderGoals);

    // Listen for task updates
    window.addEventListener('storage', function(e) {
      if (e.key === 'tasks' || e.key === 'tasks_last_update') {
        renderGoals();
      }
    });

    window.addEventListener('tasksUpdated', function() { 
      renderGoals(); 
    });
  </script>
</body>
</html>

