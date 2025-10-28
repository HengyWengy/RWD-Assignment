<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - Productivity Hub</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .analytics-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .chart-container {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
    }

    .chart-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #0b111a;
      border-bottom: 2px solid #007bff;
      padding-bottom: 10px;
    }

    .export-section {
      display: flex;
      gap: 15px;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }
  </style>
</head>
<body>
  <!-- Sidebar Toggle Button -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

  <!-- Sidebar -->
  <div class="sidebar">
    <img src="Logo RWD.jpeg" alt="Logo"> 
    <div class="menu">
      <a href="Dashboard.php"><button>DASHBOARD</button></a>
      <a href="Task.php"><button>TASK</button></a>
      <button>TIME TRACKING</button>
      <a href="goals.php"><button>GOALS</button></a>
      <a href="collaboration.php"><button>COLLABORATION</button></a>
      <a href="analytics.php"><button class="active">ANALYTICS</button></a>
      <button>PROFILE</button>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="analytics-container">
      <!-- Header -->
      <div class="header">
        <h1>📊 Productivity Analytics</h1>
        <div class="user-info">
          <div class="user-name" id="userNameDisplay">USER</div>
          <div class="avatar"></div>
        </div>
      </div>

      <!-- Export Section -->
      <div class="export-section">
        <button class="btn btn-primary" id="exportReportBtn">📄 Export Report</button>
        <button class="btn btn-success" onclick="ProductivityHub.exportTasksToCSV()">📊 Export Tasks CSV</button>
        <button class="btn btn-secondary" onclick="location.reload()">🔄 Refresh Data</button>
      </div>

      <!-- Statistics Cards -->
      <div id="statsCards"></div>

      <!-- Charts Section -->
      <div class="chart-container">
        <div class="chart-title">📈 Task Status Distribution</div>
        <div id="statusChart"></div>
      </div>

      <div class="chart-container">
        <div class="chart-title">📊 Task Type Distribution</div>
        <div id="taskTypeChart"></div>
      </div>

      <div class="chart-container">
        <div class="chart-title">⏱️ Time Tracking by Team Member (Hours)</div>
        <div id="timeChart"></div>
      </div>

      <!-- Recent Activity -->
      <div class="chart-container">
        <div class="chart-title">📋 Recent Tasks</div>
        <div id="recentTasks">
          <table class="task-table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Owner</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Deadline</th>
              </tr>
            </thead>
            <tbody id="recentTasksBody"></tbody>
          </table>
        </div>
      </div>

      <!-- Productivity Insights -->
      <div class="chart-container">
        <div class="chart-title">💡 Productivity Insights</div>
        <div id="insights" style="line-height: 1.8; color: #333;">
          <p>Loading insights...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="js/script.js"></script>
  <script src="js/analytics.js"></script>
  <script>
    // Update user display
    document.addEventListener('DOMContentLoaded', function() {
      try {
        const stored = localStorage.getItem('username');
        if (stored) {
          const el = document.getElementById('userNameDisplay');
          if (el) el.textContent = stored;
        }
      } catch (e) {
        console.error('Failed to read username from localStorage', e);
      }

      // Render recent tasks
      renderRecentTasks();

      // Generate insights
      generateInsights();
    });

    // Render recent tasks table
    function renderRecentTasks() {
      const tbody = document.getElementById('recentTasksBody');
      if (!tbody) return;

      const tasks = ProductivityHub.getTasks().slice(0, 10);
      tbody.innerHTML = '';

      if (tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No tasks found</td></tr>';
        return;
      }

      tasks.forEach(task => {
        const tr = document.createElement('tr');
        
        // Status color
        let statusColor = '#6c757d';
        if (task.status === 'Completed') statusColor = '#28a745';
        else if (task.status === 'In Progress') statusColor = '#007bff';
        else if (task.status === 'Pending') statusColor = '#ffc107';

        tr.innerHTML = `
          <td>${ProductivityHub.escapeHtml(task.title)}</td>
          <td>${ProductivityHub.escapeHtml(task.owner)}</td>
          <td><span style="background: ${statusColor}; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">${ProductivityHub.escapeHtml(task.status)}</span></td>
          <td>
            <div style="width: 100%; background: #e9ecef; border-radius: 10px; height: 20px; overflow: hidden;">
              <div style="width: ${task.progress}%; background: ${statusColor}; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 11px; font-weight: bold;">
                ${task.progress}%
              </div>
            </div>
          </td>
          <td>${ProductivityHub.formatDate(task.deadline)}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    // Generate productivity insights
    function generateInsights() {
      const insightsDiv = document.getElementById('insights');
      if (!insightsDiv) return;

      const stats = ProductivityHub.getProductivityStats();
      const tasks = ProductivityHub.getTasks();

      let insights = '';

      // Completion rate insight
      const completionRate = stats.total > 0 ? Math.round((stats.completed / stats.total) * 100) : 0;
      if (completionRate >= 75) {
        insights += '<p>✅ <strong>Excellent work!</strong> You have a completion rate of ' + completionRate + '%. Keep up the great productivity!</p>';
      } else if (completionRate >= 50) {
        insights += '<p>👍 <strong>Good progress!</strong> Your completion rate is ' + completionRate + '%. Consider focusing on completing pending tasks.</p>';
      } else if (completionRate > 0) {
        insights += '<p>⚠️ <strong>Needs attention!</strong> Your completion rate is ' + completionRate + '%. Try to prioritize task completion.</p>';
      } else {
        insights += '<p>📝 <strong>Getting started!</strong> No tasks completed yet. Start working on your pending tasks!</p>';
      }

      // Overdue tasks insight
      if (stats.overdue > 0) {
        insights += '<p>⏰ <strong>Alert!</strong> You have ' + stats.overdue + ' overdue task' + (stats.overdue > 1 ? 's' : '') + '. Review and update deadlines or complete them soon.</p>';
      } else if (stats.pending > 0 || stats.inProgress > 0) {
        insights += '<p>✨ <strong>Great!</strong> No overdue tasks. You\'re staying on track!</p>';
      }

      // Average progress insight
      if (stats.averageProgress >= 70) {
        insights += '<p>📈 <strong>Strong momentum!</strong> Your average task progress is ' + stats.averageProgress + '%. You\'re making excellent headway!</p>';
      } else if (stats.averageProgress >= 40) {
        insights += '<p>📊 <strong>Steady progress!</strong> Your average task progress is ' + stats.averageProgress + '%. Keep pushing forward!</p>';
      } else if (stats.total > 0) {
        insights += '<p>🎯 <strong>Room for improvement!</strong> Your average task progress is ' + stats.averageProgress + '%. Focus on advancing your tasks.</p>';
      }

      // Time tracking insight
      if (stats.totalTime > 0) {
        const hours = Math.floor(stats.totalTime / 60);
        const minutes = stats.totalTime % 60;
        insights += '<p>⏱️ <strong>Time invested:</strong> You\'ve tracked ' + hours + 'h ' + minutes + 'm across all tasks. Time tracking helps improve productivity!</p>';
      } else {
        insights += '<p>⏱️ <strong>Tip:</strong> Start tracking time on your tasks to better understand where your effort goes!</p>';
      }

      // Task distribution insight
      const typeDistribution = ProductivityHub.getTaskTypeDistribution();
      const types = Object.keys(typeDistribution);
      if (types.length > 3) {
        insights += '<p>🎨 <strong>Diverse workload!</strong> You\'re working on ' + types.length + ' different types of tasks. Good variety!</p>';
      }

      insightsDiv.innerHTML = insights || '<p>No insights available yet. Create some tasks to see your productivity insights!</p>';
    }
  </script>
</body>
</html>

