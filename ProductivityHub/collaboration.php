<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Collaboration - Productivity Hub</title>
  <!-- External CSS -->
  <link rel="stylesheet" href="css/styles.css">
  <style>
    /* Additional styles specific to collaboration page */
    #membersList {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }

    .shared-task-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f8f9fa;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 10px;
      border-left: 4px solid #007bff;
    }

    .shared-task-item button {
      background: #28a745;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .shared-task-item button:hover {
      background: #218838;
    }
  </style>
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
      <a href="goals.php"><button>GOALS</button></a>
      <a href="collaboration.php"><button class="active">COLLABORATION</button></a>
      <a href="analytics.php"><button>ANALYTICS</button></a>
      <a href="profile.php"><button>PROFILE</button></a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main">
    <!-- Header -->
    <div class="header">
      <h1>🤝 Team Collaboration</h1>
      <div class="user-info">
        <div class="user-name" id="userNameDisplay">USER</div>
        <div class="avatar"></div>
      </div>
    </div>

    <!-- Project Members Section -->
    <div class="card">
      <h2>👥 Project Members</h2>
      <div id="membersList"></div>
      <button class="btn btn-primary" style="margin-top: 15px;" onclick="addMember()">➕ Add Member</button>
    </div>

    <!-- Team Chat Section -->
    <div class="card">
      <h2>💬 Team Chat</h2>
      <div id="chatBox"></div>
      <div style="margin-top:15px; display: flex; gap: 10px;">
        <input id="chatInput" type="text" placeholder="Type your message..." style="flex: 1; padding: 12px; border: 2px solid #ddd; border-radius: 6px;">
        <button id="sendBtn" class="btn btn-primary">Send</button>
      </div>
    </div>

    <!-- Shared Workspace Section -->
    <div class="card">
      <h2>📂 Shared Workspace</h2>
      <p style="color: #666; margin-bottom: 15px;">Tasks shared with your team. Click ✅ to mark as completed.</p>
      <div id="sharedWorkspace"></div>
    </div>

    <!-- File Sharing Section (Placeholder) -->
    <div class="card">
      <h2>📎 File Sharing</h2>
      <p style="color: #666;">File sharing feature coming soon! You'll be able to upload and share documents with your team.</p>
      <button class="btn btn-secondary" disabled>📤 Upload File (Coming Soon)</button>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
  
  <script>
    // Render project members
    function renderMembers() {
      const membersList = document.getElementById('membersList');
      if (!membersList) return;

      // Get unique owners from tasks
      const tasks = ProductivityHub.getTasks();
      const members = new Set();
      
      tasks.forEach(task => {
        if (task.owner) members.add(task.owner);
      });

      // Add current user if not in list
      const currentUser = ProductivityHub.getCurrentUser();
      members.add(currentUser);

      membersList.innerHTML = '';
      
      if (members.size === 0) {
        membersList.innerHTML = '<p style="color: #999;">No team members yet.</p>';
        return;
      }

      members.forEach(member => {
        const memberDiv = document.createElement('div');
        memberDiv.className = 'member';
        memberDiv.textContent = member;
        membersList.appendChild(memberDiv);
      });
    }

    // Add new member
    function addMember() {
      const memberName = prompt('Enter team member name:');
      if (!memberName || !memberName.trim()) return;

      // Create a placeholder task for the new member
      ProductivityHub.addTask({
        title: `Welcome ${memberName}`,
        type: 'General',
        owner: memberName.trim(),
        deadline: new Date().toISOString().split('T')[0],
        status: 'Pending',
        progress: 0,
        description: 'Welcome to the team!'
      });

      renderMembers();
      ProductivityHub.showNotification(`${memberName} added to the team!`, 'success');
    }

    // Render shared workspace tasks
    function renderSharedWorkspace() {
      const workspace = document.getElementById('sharedWorkspace');
      if (!workspace) return;

      const tasks = ProductivityHub.getTasks();
      workspace.innerHTML = '';

      if (tasks.length === 0) {
        workspace.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No shared tasks yet.</p>';
        return;
      }

      tasks.forEach((task, index) => {
        const taskDiv = document.createElement('div');
        taskDiv.className = 'shared-task-item';
        
        const isCompleted = task.status === 'Completed';
        
        taskDiv.innerHTML = `
          <div>
            <strong style="${isCompleted ? 'text-decoration: line-through; color: #999;' : ''}">${ProductivityHub.escapeHtml(task.title)}</strong>
            <div style="font-size: 12px; color: #666; margin-top: 4px;">
              Owner: ${ProductivityHub.escapeHtml(task.owner)} | 
              Type: ${ProductivityHub.escapeHtml(task.type)} | 
              Progress: ${task.progress}%
            </div>
          </div>
          <button onclick="markTaskCompleted('${task.id}')" ${isCompleted ? 'disabled' : ''} title="Mark as completed">
            ${isCompleted ? '✅ Completed' : '✅ Complete'}
          </button>
        `;
        
        workspace.appendChild(taskDiv);
      });
    }

    // Mark task as completed
    function markTaskCompleted(taskId) {
      const task = ProductivityHub.getTaskById(taskId);
      if (!task) return;

      if (confirm(`Mark "${task.title}" as completed?`)) {
        ProductivityHub.updateTask(taskId, {
          status: 'Completed',
          progress: 100
        });
        
        renderSharedWorkspace();
        ProductivityHub.showNotification('Task marked as completed! ✅', 'success');
        
        // Send chat notification
        ProductivityHub.sendChatMessage(`✅ ${ProductivityHub.getCurrentUser()} completed: ${task.title}`);
        ProductivityHub.renderChatMessages();
      }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', () => {
      renderMembers();
      renderSharedWorkspace();
      ProductivityHub.renderChatMessages();

      // Listen for task updates
      window.addEventListener('tasksUpdated', () => {
        renderMembers();
        renderSharedWorkspace();
      });

      window.addEventListener('storage', (e) => {
        if (e.key === 'tasks' || e.key === 'tasks_last_update') {
          renderMembers();
          renderSharedWorkspace();
        }
      });

      // Setup chat
      const chatInput = document.getElementById('chatInput');
      const sendBtn = document.getElementById('sendBtn');

      if (sendBtn) {
        sendBtn.addEventListener('click', () => {
          const text = chatInput.value;
          if (text.trim()) {
            ProductivityHub.sendChatMessage(text);
            chatInput.value = '';
            ProductivityHub.renderChatMessages();
          }
        });
      }

      if (chatInput) {
        chatInput.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            sendBtn.click();
          }
        });
      }

      // Listen for chat updates
      window.addEventListener('chatUpdated', ProductivityHub.renderChatMessages);
      window.addEventListener('storage', (e) => {
        if (e.key === 'chatMessages') {
          ProductivityHub.renderChatMessages();
        }
      });
    });
  </script>
</body>
</html>

