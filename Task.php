<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tasks - Productivity Hub</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      display: flex;
      height: 100vh;
    }
    /* Sidebar Toggle Button */
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
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      text-align: left;
    }
    .menu button:hover {
      background: #0056b3;
    }

    /* Main Content */
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
    .sidebar.collapsed + .main .task-header {
      padding-left: 80px;
    }

    .task-header {
      display: flex;
      justify-content: flex-start;
      gap: 15px;
      margin-bottom: 20px;
      padding-left: 20px;
    }
    .task-header button {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      background: #007bff;
      color: #fff;
    }
    .task-header button:hover {
      background: #0056b3;
    }

    /* Task Groups */
    .task-group {
      background: #e3e3ff;
      margin-bottom: 20px;
      border-radius: 10px;
      overflow: hidden;
    }
    .task-group h3 {
      margin: 0;
      padding: 10px;
      background: #6c63ff;
      color: #fff;
    }
    .task-list {
      padding: 10px;
    }
    .task-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      padding: 10px;
      margin-bottom: 8px;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .task-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .task-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #007bff;
    }
    .labels span {
      background: #007bff;
      color: #fff;
      padding: 3px 8px;
      border-radius: 5px;
      font-size: 12px;
      margin-left: 5px;
    }

    /* Modal Form */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      width: 350px;
    }
    .modal-content input, .modal-content select {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .modal-content button {
      background: #007bff;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .modal-content button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
  <div class="sidebar">
    <img src="Logo RWD.jpeg" alt="Logo"> 
    <div class="menu">
      <button>TASK</button>
      <button>TIME TRACKING</button>
      <button type="button" onclick="window.location.href='goals.php'">GOALS</button>
      <button type="button" onclick="window.location.href='collaboration.php'">COLLABORATION</button>
      <button>ANALYTICS</button>
      <button>PROFILE</button>
    </div>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="task-header">
      <button id="addTaskBtn">ADD TASK</button>
      <button>FILTER</button>
    </div>

    <div class="task-group">
      <h3>Task List</h3>
      <div class="task-list" id="taskList"></div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="taskModal">
    <div class="modal-content">
      <h3>Add Task</h3>
      <input type="text" id="taskName" placeholder="Task Name">
      <input type="text" id="taskOwner" placeholder="Owner Name">
      <input type="date" id="taskDate">
      <select id="taskLabel">
        <option value="Reminder">Reminder</option>
        <option value="Assignment">Assignment</option>
        <option value="Research">Research</option>
        <option value="Marketing">Marketing</option>
      </select>
      <button id="saveTask">Save Task</button>
    </div>
  </div>

  <!-- JS -->
  <script>
    /* Sidebar toggle */
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    }

    /* Local Storage Helpers */
    function _loadTasks() {
      try { return JSON.parse(localStorage.getItem('tasks') || '[]'); }
      catch (e) { console.error('parse tasks', e); return []; }
    }
    function _saveTasks(list) {
      try {
        localStorage.setItem('tasks', JSON.stringify(list));
        localStorage.setItem('tasks_last_update', Date.now().toString());
        window.dispatchEvent(new Event('tasksUpdated'));
        console.log('Tasks saved ->', list);
      } catch (e) { console.error('save tasks', e); }
    }

    /* Modal and Task Management */
    const modal = document.getElementById('taskModal');
    const addTaskBtn = document.getElementById('addTaskBtn');
    const saveTaskBtn = document.getElementById('saveTask');
    const taskList = document.getElementById('taskList');

    addTaskBtn.onclick = () => { modal.style.display = 'flex'; };
    window.onclick = (event) => { if (event.target == modal) modal.style.display = "none"; };

    /* Save new task */
    saveTaskBtn.onclick = () => {
      const name = document.getElementById('taskName').value.trim();
      const owner = document.getElementById('taskOwner').value.trim();
      const date = document.getElementById('taskDate').value;
      const label = document.getElementById('taskLabel').value;

      if (!name || !owner || !date) {
        alert("Please fill all fields!");
        return;
      }

      const task = {
        title: name,
        type: label,
        owner: owner,
        deadline: date,
        created: new Date().toISOString()
      };

      // Save to localStorage
      const arr = _loadTasks();
      arr.unshift(task);
      _saveTasks(arr);

      // Add to UI immediately
      addTaskToDOM(task);

      modal.style.display = 'none';
      document.getElementById('taskName').value = '';
      document.getElementById('taskOwner').value = '';
      document.getElementById('taskDate').value = '';
    };

    /* Display task in DOM */
    function addTaskToDOM(task) {
      const div = document.createElement('div');
      div.className = 'task-item';
      div.innerHTML = `
        <div class="task-info">
          <div class="task-avatar"></div>
          <span class="task-title">${task.title} (${task.owner})</span>
        </div>
        <div class="labels">
          <span>${task.type}</span>
        </div>
      `;
      taskList.appendChild(div);
    }

    /* Render all saved tasks when page loads */
    function renderAllTasks() {
      const tasks = _loadTasks();
      taskList.innerHTML = '';
      tasks.forEach(addTaskToDOM);
    }

    document.addEventListener('DOMContentLoaded', renderAllTasks);
  </script>
</body>
</html>
