<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tasks - Productivity Hub</title>
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
      <a href="Task.php"><button class="active">TASK</button></a>
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
      <h1>📋 Task Management</h1>
      <div class="user-info">
        <div class="user-name" id="userNameDisplay">USER</div>
        <div class="avatar"></div>
      </div>
    </div>

    <!-- Task Actions -->
    <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
      <button class="btn btn-primary" id="addTaskBtn">➕ ADD TASK</button>
      <button class="btn btn-secondary" id="filterBtn">🔍 FILTER</button>
      <button class="btn btn-success" onclick="ProductivityHub.exportTasksToCSV()">📊 EXPORT CSV</button>
    </div>

    <!-- Task List -->
    <div class="card">
      <h2>All Tasks</h2>
      <div id="taskList"></div>
    </div>
  </div>

  <!-- Add/Edit Task Modal -->
  <div class="modal" id="taskModal">
    <div class="modal-content">
      <h3>Add New Task</h3>
      
      <div class="form-group">
        <label for="taskName">Task Name *</label>
        <input type="text" id="taskName" placeholder="Enter task name">
      </div>

      <div class="form-group">
        <label for="taskOwner">Owner *</label>
        <input type="text" id="taskOwner" placeholder="Owner name">
      </div>

      <div class="form-group">
        <label for="taskDate">Deadline *</label>
        <input type="date" id="taskDate">
      </div>

      <div class="form-group">
        <label for="taskLabel">Type *</label>
        <select id="taskLabel">
          <option value="Reminder">Reminder</option>
          <option value="Assignment">Assignment</option>
          <option value="Research">Research</option>
          <option value="Marketing">Marketing</option>
          <option value="Development">Development</option>
          <option value="Meeting">Meeting</option>
          <option value="General">General</option>
        </select>
      </div>

      <div class="form-group">
        <label for="taskStatus">Status</label>
        <select id="taskStatus">
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
      </div>

      <div class="form-group">
        <label for="taskProgress">Progress (%)</label>
        <input type="number" id="taskProgress" min="0" max="100" value="0">
      </div>

      <div class="form-group">
        <label for="taskDescription">Description</label>
        <textarea id="taskDescription" rows="3" placeholder="Task description (optional)"></textarea>
      </div>

      <div style="display: flex; gap: 10px; margin-top: 20px;">
        <button class="btn btn-primary" id="saveTask">Save Task</button>
        <button class="btn btn-secondary" onclick="document.getElementById('taskModal').style.display='none'">Cancel</button>
      </div>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
  <script src="js/tasks.js"></script>
  
  <script>
    // Enhanced save task handler
    document.addEventListener('DOMContentLoaded', () => {
      const saveTaskBtn = document.getElementById('saveTask');
      const addTaskBtn = document.getElementById('addTaskBtn');
      const taskModal = document.getElementById('taskModal');

      // Open modal for new task
      if (addTaskBtn) {
        addTaskBtn.addEventListener('click', () => {
          // Reset form
          document.getElementById('taskName').value = '';
          document.getElementById('taskOwner').value = ProductivityHub.getCurrentUser();
          document.getElementById('taskDate').value = '';
          document.getElementById('taskLabel').value = 'Reminder';
          document.getElementById('taskStatus').value = 'Pending';
          document.getElementById('taskProgress').value = '0';
          document.getElementById('taskDescription').value = '';

          // Reset save button
          saveTaskBtn.textContent = 'Save Task';
          delete saveTaskBtn.dataset.editMode;
          delete saveTaskBtn.dataset.taskId;

          taskModal.style.display = 'flex';
        });
      }

      // Save task handler
      if (saveTaskBtn) {
        saveTaskBtn.addEventListener('click', () => {
          const title = document.getElementById('taskName').value.trim();
          const owner = document.getElementById('taskOwner').value.trim();
          const deadline = document.getElementById('taskDate').value;
          const type = document.getElementById('taskLabel').value;
          const status = document.getElementById('taskStatus').value;
          const progress = parseInt(document.getElementById('taskProgress').value) || 0;
          const description = document.getElementById('taskDescription').value.trim();

          if (!title) {
            alert('Please enter a task name!');
            return;
          }

          if (!owner) {
            alert('Please enter an owner name!');
            return;
          }

          if (!deadline) {
            alert('Please select a deadline!');
            return;
          }

          const taskData = {
            title: title,
            owner: owner,
            deadline: deadline,
            type: type,
            status: status,
            progress: progress,
            description: description
          };

          // Check if we're editing or creating
          if (saveTaskBtn.dataset.editMode === 'true') {
            const taskId = saveTaskBtn.dataset.taskId;
            ProductivityHub.updateTask(taskId, taskData);
            ProductivityHub.showNotification('Task updated successfully!', 'success');
          } else {
            ProductivityHub.addTask(taskData);
            ProductivityHub.showNotification('Task created successfully!', 'success');
          }

          // Close modal and refresh list
          taskModal.style.display = 'none';
          TaskManager.renderAllTasks();
        });
      }
    });
  </script>
</body>
</html>

