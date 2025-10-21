<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Goals - Productivity Hub</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      display: flex;
      height: 100vh;
      overflow-x: hidden;
    }

    /* Sidebar Toggle Button */
    .toggle-btn {
      position: fixed;
      top: 15px;
      left: 15px;
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 18px;
      cursor: pointer;
      z-index: 1001;
    }
    .toggle-btn:hover {
      background: #0056b3;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #0b111a;
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      box-sizing: border-box;
      transition: width 0.3s ease, opacity 0.3s ease;
      height: 100vh;
      position: fixed;
      top: 0;
        left: 0;
        z-index: 1000;
    }
    .sidebar.collapsed {
      transform: translateX(-100%);
      opacity: 0;
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
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      text-align: left;
      width: 100%;
    }
    .menu button:hover {
      background: #0056b3;
    }

    /* Main Content */
    .main {
      flex: 1;
      padding: 40px;
      margin-left: 220px;
      transition: margin-left 0.3s ease;
      width: 100%
    }
    .sidebar.collapsed + .main {
      margin-left: 0;
    }

    /* Goals Page Styling */
    .card {
      max-width: 900px;
      margin: 0 auto;
      padding: 30px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }
    h1 {
      text-align: center;
      margin: 0 0 18px 0;
      font-size: 36px;
    }
    .add-btn {
      display: block;
      margin: 12px auto 18px auto;
      padding: 10px 18px;
      border-radius: 8px;
      background: #007bff;
      border: none;
      color: #fff;
      text-decoration: none;
      text-align: center;
      font-weight: bold;
      cursor: pointer;
    }
    .add-btn:hover {
      background: #0056b3;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      font-size: 16px;
    }
    .empty {
      color: #666;
      text-align: center;
      padding: 30px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        body {
            flex-direction: column;
        }

        .sidebar {
            transform: translateX(-100%);
            opacity: 0;
            position: fixed;
            width: 200px;
        }

        .sidebar.active {
        transform: translateX(0);
        opacity: 1;
        }

        .main {
            margin-left: 0;
            padding: 20px;
        }

        h1 {
            font-size: 28px;
        }

        table, th, td {
            font-size: 14px;
        }

        .add-btn {
            width: 100%;
            font-size: 15px;
        }

        .toggle-btn {
            top: 10px;
            left: 10px;
            padding: 8px 12px;
        }

    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
  <div class="sidebar">
    <img src="Logo RWD.jpeg" alt="Logo"> 
    <div class="menu">
      <button type="button" onclick="window.location.href='Task.php'">TASK</button>
      <button>TIME TRACKING</button>
      <button type="button" onclick="window.location.href='goals.php'">GOALS</button>
      <button type="button" onclick="window.location.href='collaboration.php'">COLLABORATION</button>
      <button>ANALYTICS</button>
      <button>PROFILE</button>
    </div>
  </div>

  <!-- Main Goals Section -->
  <div class="main">
    <div class="card">
      <h1>Goal Setting</h1>
      <button class="add-btn" onclick="window.location.href='Task.php'">+ Add Goal (create task)</button>

      <table id="goalsTable" aria-live="polite">
        <thead>
          <tr><th>Goal</th><th>Type</th><th>Deadline</th><th>Owner</th></tr>
        </thead>
        <tbody id="goalsBody"></tbody>
      </table>

      <div id="empty" class="empty" style="display:none">
        No goals yet. Add tasks on the Tasks page and they will appear here.
      </div>
    </div>
  </div>

  <script>
    /* Sidebar toggle */
    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('collapsed');
        sidebar.classList.toggle('active');
    }

    const KEY = 'tasks';

    function _loadTasks() {
      try { return JSON.parse(localStorage.getItem(KEY) || '[]'); }
      catch (e) { console.error('parse tasks', e); return []; }
    }

    function escapeHtml(s) {
      return (s||'').toString().replace(/[&<>"']/g, c => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
      }[c]));
    }

    function renderGoals() {
      const list = _loadTasks();
      const body = document.getElementById('goalsBody');
      const empty = document.getElementById('empty');
      body.innerHTML = '';
      if (!list.length) {
        empty.style.display = 'block';
        return;
      }
      empty.style.display = 'none';
      list.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${escapeHtml(t.title)}</td>
          <td>${escapeHtml(t.type)}</td>
          <td>${t.deadline ? escapeHtml(t.deadline) : '-'}</td>
          <td>${t.owner ? escapeHtml(t.owner) : '-'}</td>`;
        body.appendChild(tr);
      });
    }

    document.addEventListener('DOMContentLoaded', renderGoals);

    window.addEventListener('storage', function(e) {
      if (e.key === KEY || e.key === 'tasks_last_update') {
        renderGoals();
      }
    });

    window.addEventListener('tasksUpdated', function() { renderGoals(); });
  </script>
</body>
</html>
