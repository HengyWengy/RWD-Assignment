<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Collaboration</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      background-color: #f8f9fa;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #0b111a;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }
    .sidebar h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }
    .sidebar a {
      color: white;
      padding: 10px;
      text-decoration: none;
      border-radius: 6px;
      margin: 4px 0;
      display: block;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background: #007bff;
    }

    /* Main content */
    .main-content {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
      margin-top: 0;
      color: #0b111a;
    }

    /* Project Members */
    #membersList {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .member {
      background: #007bff;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
    }

    /* Chat */
    #chatBox {
      border: 1px solid #ccc;
      border-radius: 6px;
      height: 200px;
      overflow-y: auto;
      padding: 10px;
      background: #fafafa;
    }
    #chatInput {
      width: 80%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    #sendBtn {
      padding: 8px 14px;
      border: none;
      background: #007bff;
      color: white;
      border-radius: 6px;
      cursor: pointer;
    }

    /* Shared Workspace */
    #workspace {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .task-item {
      display: flex;
      justify-content: space-between;
      background: #e9ecef;
      padding: 10px;
      border-radius: 6px;
      align-items: center;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      body { flex-direction: column; }
      .sidebar {
        width: 100%;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
        padding: 10px;
        overflow-x: auto;
      }
      .sidebar h2 { display: none; }
      .sidebar a {
        flex: 1;
        text-align: center;
        margin: 0 5px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Menu</h2>
    <a href="Dashboard.php">Dashboard</a>
    <a href="Task.php">Tasks</a>
    <a href="goals.php">Goals</a>
    <a href="collaboration.php" class="active">Collaboration</a>
    <a href= "analytics.php">Analytics</a>
  </div>

  <div class="main-content">
    <!-- Project Members -->
    <div class="section">
      <h2>Project Members</h2>
      <div id="membersList"></div>
    </div>

    <!-- Chat Box -->
    <div class="section">
      <h2>Team Chat</h2>
      <div id="chatBox"></div>
      <div style="margin-top:10px;">
        <input id="chatInput" type="text" placeholder="Type your message...">
        <button id="sendBtn">Send</button>
      </div>
    </div>

    <!-- Shared Workspace -->
    <div class="section">
      <h2>Shared Workspace</h2>
      <ul id="sharedWorkspaceList"></ul>
      <div id="workspace"></div>
    </div>
  </div>

  <script>
   // Load tasks from localStorage (existing workspace list)
  const sharedWorkspaceList = document.getElementById('sharedWorkspaceList');
  let tasks = JSON.parse(localStorage.getItem('tasks')) || [];

  function renderTasks() {
    sharedWorkspaceList.innerHTML = '';
    tasks.forEach((task, index) => {
      const li = document.createElement('li');
      li.innerHTML = `
        <span>${task.title || 'Untitled Task'}</span>
        <button onclick="markCompleted(${index})" title="Mark as completed ✅">
          ✅
        </button>
      `;
      sharedWorkspaceList.appendChild(li);
    });
  }

  // Mark task as completed
  function markCompleted(index) {
    const completedTask = tasks[index];
    alert(`Task "${completedTask.title}" marked as completed ✅`);
    tasks.splice(index, 1);
    localStorage.setItem('tasks', JSON.stringify(tasks));
    renderTasks();
  }

  // Initialize tasks on load
  renderTasks();

 
  // Chat functionality
  
  const chatBox = document.getElementById('chatBox');
  const chatInput = document.getElementById('chatInput');
  const sendBtn = document.getElementById('sendBtn');
  const CHAT_KEY = 'chatMessages';

  function loadMessages() {
    try { return JSON.parse(localStorage.getItem(CHAT_KEY) || '[]'); }
    catch (e) { console.error('chat parse error', e); return []; }
  }
  function saveMessages(msgs) {
    try { localStorage.setItem(CHAT_KEY, JSON.stringify(msgs)); }
    catch (e) { console.error('chat save error', e); }
  }
  function renderMessages() {
    const msgs = loadMessages();
    chatBox.innerHTML = '';
    msgs.forEach(m => {
      const p = document.createElement('div');
      p.style.marginBottom = '8px';
      p.innerHTML = `<strong>${escapeHtml(m.user || 'Me')}:</strong> ${escapeHtml(m.text)}`;
      chatBox.appendChild(p);
    });
    // scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  function escapeHtml(s){ return (s||'').toString().replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  function sendMessage() {
    const text = (chatInput.value || '').trim();
    if (!text) return;
    const user = localStorage.getItem('username') || 'Anonymous';
    const msgs = loadMessages();
    msgs.push({ user, text, time: Date.now() });
    saveMessages(msgs);
    renderMessages();
    chatInput.value = '';
    // notify other tabs/pages
    window.dispatchEvent(new Event('chatUpdated'));
  }

  // wire button + Enter key
  sendBtn.addEventListener('click', sendMessage);
  chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }
  });

  // react to storage events (other tabs)
  window.addEventListener('storage', (e) => {
    if (e.key === CHAT_KEY) renderMessages();
  });
  // custom event for same-tab notifications
  window.addEventListener('chatUpdated', renderMessages);

  // render on load
  document.addEventListener('DOMContentLoaded', () => {
    renderMessages();
  });

  </script>
</body>
</html>
