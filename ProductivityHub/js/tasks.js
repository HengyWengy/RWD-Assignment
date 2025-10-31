/**
 * TASK MANAGEMENT MODULE
 * Handles task creation, display, and management
 */

(function() {
  'use strict';

  // ===== TASK RENDERING =====

  /**
   * Render all tasks to the task list
   */
  function renderAllTasks() {
    const taskList = document.getElementById('taskList');
    if (!taskList) return;

    const tasks = window.ProductivityHub.getTasks();
    taskList.innerHTML = '';

    if (tasks.length === 0) {
      taskList.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">No tasks yet. Click "ADD TASK" to create one!</p>';
      return;
    }

    tasks.forEach(task => {
      addTaskToDOM(task);
    });
  }

  /**
   * Add a single task to the DOM
   */
  function addTaskToDOM(task) {
    const taskList = document.getElementById('taskList');
    if (!taskList) return;

    const div = document.createElement('div');
    div.className = 'task-item';
    div.dataset.taskId = task.id;
    
    // Determine status color
    let statusColor = '#6c757d';
    if (task.status === 'Completed') statusColor = '#28a745';
    else if (task.status === 'In Progress') statusColor = '#007bff';
    else if (task.status === 'Pending') statusColor = '#ffc107';

    div.innerHTML = `
      <div class="task-info">
        <div class="task-avatar" title="${window.ProductivityHub.escapeHtml(task.owner)}">
          ${window.ProductivityHub.getInitials(task.owner)}
        </div>
        <div>
          <div class="task-title">${window.ProductivityHub.escapeHtml(task.title)}</div>
          <small style="color: #666;">Owner: ${window.ProductivityHub.escapeHtml(task.owner)} | 
          Deadline: ${window.ProductivityHub.formatDate(task.deadline)}</small>
        </div>
      </div>
      <div class="labels">
        <span style="background: ${statusColor};">${window.ProductivityHub.escapeHtml(task.status || 'Pending')}</span>
        <span>${window.ProductivityHub.escapeHtml(task.type)}</span>
      </div>
    `;

    // Add click event to view/edit task
    div.addEventListener('click', () => {
      showTaskDetails(task.id);
    });

    taskList.appendChild(div);
  }

  /**
   * Show task details in a modal
   */
  function showTaskDetails(taskId) {
    const task = window.ProductivityHub.getTaskById(taskId);
    if (!task) return;

    // Create or get details modal
    let modal = document.getElementById('taskDetailsModal');
    if (!modal) {
      modal = createTaskDetailsModal();
    }

    // Populate modal with task details
    document.getElementById('detailTitle').textContent = task.title;
    document.getElementById('detailType').textContent = task.type;
    document.getElementById('detailOwner').textContent = task.owner;
    document.getElementById('detailDeadline').textContent = window.ProductivityHub.formatDate(task.deadline);
    document.getElementById('detailStatus').textContent = task.status;
    document.getElementById('detailProgress').textContent = `${task.progress}%`;
    document.getElementById('detailTime').textContent = task.timeTracking;
    document.getElementById('detailDescription').textContent = task.description || 'No description provided.';

    // Set up edit and delete buttons
    document.getElementById('editTaskBtn').onclick = () => {
      modal.style.display = 'none';
      editTask(taskId);
    };

    document.getElementById('deleteTaskBtn').onclick = () => {
      if (confirm(`Are you sure you want to delete "${task.title}"?`)) {
        window.ProductivityHub.deleteTask(taskId);
        modal.style.display = 'none';
        renderAllTasks();
        window.ProductivityHub.showNotification('Task deleted successfully!', 'success');
      }
    };

    modal.style.display = 'flex';
  }

  /**
   * Create task details modal
   */
  function createTaskDetailsModal() {
    const modal = document.createElement('div');
    modal.id = 'taskDetailsModal';
    modal.className = 'modal';
    modal.innerHTML = `
      <div class="modal-content">
        <h3 id="detailTitle"></h3>
        <div style="margin: 15px 0;">
          <p><strong>Type:</strong> <span id="detailType"></span></p>
          <p><strong>Owner:</strong> <span id="detailOwner"></span></p>
          <p><strong>Deadline:</strong> <span id="detailDeadline"></span></p>
          <p><strong>Status:</strong> <span id="detailStatus"></span></p>
          <p><strong>Progress:</strong> <span id="detailProgress"></span></p>
          <p><strong>Time Tracked:</strong> <span id="detailTime"></span></p>
          <p><strong>Description:</strong></p>
          <p id="detailDescription" style="color: #666;"></p>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
          <button id="editTaskBtn" class="btn btn-primary">Edit Task</button>
          <button id="deleteTaskBtn" class="btn btn-danger">Delete Task</button>
          <button onclick="this.closest('.modal').style.display='none'" class="btn btn-secondary">Close</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    return modal;
  }

  /**
   * Edit task
   */
  function editTask(taskId) {
    const task = window.ProductivityHub.getTaskById(taskId);
    if (!task) return;

    // Populate the add task form with existing data
    document.getElementById('taskName').value = task.title;
    document.getElementById('taskOwner').value = task.owner;
    document.getElementById('taskDate').value = task.deadline;
    document.getElementById('taskLabel').value = task.type;

    // Change the save button to update mode
    const saveBtn = document.getElementById('saveTask');
    saveBtn.textContent = 'Update Task';
    saveBtn.dataset.editMode = 'true';
    saveBtn.dataset.taskId = taskId;

    // Show the modal
    document.getElementById('taskModal').style.display = 'flex';
  }

  // ===== TASK FORM HANDLING =====

  /**
   * Initialize task form
   */
  function initTaskForm() {
    const addTaskBtn = document.getElementById('addTaskBtn');
    const saveTaskBtn = document.getElementById('saveTask');
    const taskModal = document.getElementById('taskModal');

    if (!addTaskBtn || !saveTaskBtn || !taskModal) return;

    // Open modal for new task
    addTaskBtn.addEventListener('click', () => {
      // Reset form
      document.getElementById('taskName').value = '';
      document.getElementById('taskOwner').value = window.ProductivityHub.getCurrentUser();
      document.getElementById('taskDate').value = '';
      document.getElementById('taskLabel').value = 'Reminder';

      // Reset save button
      saveTaskBtn.textContent = 'Save Task';
      delete saveTaskBtn.dataset.editMode;
      delete saveTaskBtn.dataset.taskId;

      taskModal.style.display = 'flex';
    });

    // Save or update task
    saveTaskBtn.addEventListener('click', () => {
      const title = document.getElementById('taskName').value.trim();
      const owner = document.getElementById('taskOwner').value.trim();
      const deadline = document.getElementById('taskDate').value;
      const type = document.getElementById('taskLabel').value;

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
        type: type
      };

      // Check if we're editing or creating
      if (saveTaskBtn.dataset.editMode === 'true') {
        const taskId = saveTaskBtn.dataset.taskId;
        window.ProductivityHub.updateTask(taskId, taskData);
        window.ProductivityHub.showNotification('Task updated successfully!', 'success');
      } else {
        window.ProductivityHub.addTask(taskData);
        window.ProductivityHub.showNotification('Task created successfully!', 'success');
      }

      // Close modal and refresh list
      taskModal.style.display = 'none';
      renderAllTasks();

      // Reset form
      document.getElementById('taskName').value = '';
      document.getElementById('taskOwner').value = '';
      document.getElementById('taskDate').value = '';
    });

    // Close modal on outside click
    window.addEventListener('click', (event) => {
      if (event.target === taskModal) {
        taskModal.style.display = 'none';
      }
    });
  }

  // ===== TASK FILTERING =====

  /**
   * Initialize filter functionality
   */
  function initTaskFilter() {
    const filterBtn = document.getElementById('filterBtn');
    if (!filterBtn) return;

    filterBtn.addEventListener('click', () => {
      const filterOptions = prompt('Filter by:\n1. Status (Pending/In Progress/Completed)\n2. Type\n3. Owner\n\nEnter filter type (1-3):');
      
      if (!filterOptions) return;

      let tasks = window.ProductivityHub.getTasks();

      switch(filterOptions) {
        case '1':
          const status = prompt('Enter status (Pending/In Progress/Completed):');
          if (status) {
            tasks = window.ProductivityHub.filterTasks({ status: status });
          }
          break;
        case '2':
          const type = prompt('Enter type:');
          if (type) {
            tasks = window.ProductivityHub.filterTasks({ type: type });
          }
          break;
        case '3':
          const owner = prompt('Enter owner name:');
          if (owner) {
            tasks = window.ProductivityHub.filterTasks({ owner: owner });
          }
          break;
      }

      // Render filtered tasks
      const taskList = document.getElementById('taskList');
      if (taskList) {
        taskList.innerHTML = '';
        tasks.forEach(addTaskToDOM);
      }
    });
  }

  // ===== INITIALIZATION =====

  document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if we're on the tasks page
    if (document.getElementById('taskList')) {
      renderAllTasks();
      initTaskForm();
      initTaskFilter();

      // Listen for task updates
      window.addEventListener('tasksUpdated', renderAllTasks);
      window.addEventListener('storage', (e) => {
        if (e.key === 'tasks' || e.key === 'tasks_last_update') {
          renderAllTasks();
        }
      });
    }
  });

  // Export functions for external use
  window.TaskManager = {
    renderAllTasks,
    addTaskToDOM,
    showTaskDetails,
    editTask
  };

})();

