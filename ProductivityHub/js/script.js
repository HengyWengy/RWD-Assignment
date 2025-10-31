/**
 * PRODUCTIVITY HUB - MAIN JAVASCRIPT
 * Responsive Web Design Assignment
 * Features: Task Management, Time Tracking, Goals, Collaboration, Analytics
 */

// ===== UTILITY FUNCTIONS =====

/**
 * Escape HTML to prevent XSS attacks
 */
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
  };
  return text ? text.toString().replace(/[&<>"']/g, m => map[m]) : '';
}

/**
 * Format date to readable string
 */
function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
}

/**
 * Get initials from name
 */
function getInitials(name) {
  if (!name) return 'U';
  const parts = name.trim().split(' ');
  if (parts.length >= 2) {
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
}

// ===== SIDEBAR MANAGEMENT =====

/**
 * Toggle sidebar visibility
 */
function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.classList.toggle('collapsed');
    sidebar.classList.toggle('active');
    
    // Save state to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
  }
}

/**
 * Initialize sidebar state from localStorage
 */
function initSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  
  if (sidebar && isCollapsed) {
    sidebar.classList.add('collapsed');
  }
}

// ===== LOCAL STORAGE HELPERS =====

/**
 * Get tasks from localStorage
 */
function getTasks() {
  try {
    return JSON.parse(localStorage.getItem('tasks') || '[]');
  } catch (e) {
    console.error('Error parsing tasks:', e);
    return [];
  }
}

/**
 * Save tasks to localStorage
 */
function saveTasks(tasks) {
  try {
    localStorage.setItem('tasks', JSON.stringify(tasks));
    localStorage.setItem('tasks_last_update', Date.now().toString());
    
    // Dispatch custom event for cross-page updates
    window.dispatchEvent(new Event('tasksUpdated'));
    
    console.log('Tasks saved:', tasks.length);
  } catch (e) {
    console.error('Error saving tasks:', e);
  }
}

/**
 * Get users from localStorage
 */
function getUsers() {
  try {
    return JSON.parse(localStorage.getItem('users') || '{}');
  } catch (e) {
    console.error('Error parsing users:', e);
    return {};
  }
}

/**
 * Save users to localStorage
 */
function saveUsers(users) {
  try {
    localStorage.setItem('users', JSON.stringify(users));
  } catch (e) {
    console.error('Error saving users:', e);
  }
}

/**
 * Get current username
 */
function getCurrentUser() {
  return localStorage.getItem('username') || 'Guest';
}

/**
 * Get current user role
 */
function getCurrentRole() {
  return localStorage.getItem('role') || 'Individual';
}

// ===== TASK MANAGEMENT =====

/**
 * Add new task
 */
function addTask(taskData) {
  const tasks = getTasks();
  
  const newTask = {
    id: Date.now().toString(),
    title: taskData.title || 'Untitled Task',
    type: taskData.type || 'General',
    owner: taskData.owner || getCurrentUser(),
    deadline: taskData.deadline || '',
    status: taskData.status || 'Pending',
    progress: taskData.progress || 0,
    timeTracking: taskData.timeTracking || '0h',
    created: new Date().toISOString(),
    updated: new Date().toISOString(),
    description: taskData.description || '',
    priority: taskData.priority || 'Medium'
  };
  
  tasks.unshift(newTask);
  saveTasks(tasks);
  
  return newTask;
}

/**
 * Update existing task
 */
function updateTask(taskId, updates) {
  const tasks = getTasks();
  const index = tasks.findIndex(t => t.id === taskId);
  
  if (index !== -1) {
    tasks[index] = {
      ...tasks[index],
      ...updates,
      updated: new Date().toISOString()
    };
    saveTasks(tasks);
    return tasks[index];
  }
  
  return null;
}

/**
 * Delete task
 */
function deleteTask(taskId) {
  const tasks = getTasks();
  const filtered = tasks.filter(t => t.id !== taskId);
  saveTasks(filtered);
  return filtered.length < tasks.length;
}

/**
 * Get task by ID
 */
function getTaskById(taskId) {
  const tasks = getTasks();
  return tasks.find(t => t.id === taskId);
}

/**
 * Filter tasks by criteria
 */
function filterTasks(criteria) {
  const tasks = getTasks();
  
  return tasks.filter(task => {
    if (criteria.status && task.status !== criteria.status) return false;
    if (criteria.type && task.type !== criteria.type) return false;
    if (criteria.owner && task.owner !== criteria.owner) return false;
    if (criteria.priority && task.priority !== criteria.priority) return false;
    return true;
  });
}

// ===== CHAT FUNCTIONALITY =====

/**
 * Get chat messages
 */
function getChatMessages() {
  try {
    return JSON.parse(localStorage.getItem('chatMessages') || '[]');
  } catch (e) {
    console.error('Error parsing chat messages:', e);
    return [];
  }
}

/**
 * Save chat messages
 */
function saveChatMessages(messages) {
  try {
    localStorage.setItem('chatMessages', JSON.stringify(messages));
    window.dispatchEvent(new Event('chatUpdated'));
  } catch (e) {
    console.error('Error saving chat messages:', e);
  }
}

/**
 * Send chat message
 */
function sendChatMessage(text) {
  if (!text || !text.trim()) return;
  
  const messages = getChatMessages();
  const newMessage = {
    id: Date.now().toString(),
    user: getCurrentUser(),
    text: text.trim(),
    timestamp: new Date().toISOString()
  };
  
  messages.push(newMessage);
  saveChatMessages(messages);
  
  return newMessage;
}

/**
 * Render chat messages
 */
function renderChatMessages() {
  const chatBox = document.getElementById('chatBox');
  if (!chatBox) return;
  
  const messages = getChatMessages();
  chatBox.innerHTML = '';
  
  messages.forEach(msg => {
    const div = document.createElement('div');
    div.className = 'chat-message';
    div.innerHTML = `
      <strong>${escapeHtml(msg.user)}:</strong> 
      ${escapeHtml(msg.text)}
      <small style="color: #999; margin-left: 10px;">${formatDate(msg.timestamp)}</small>
    `;
    chatBox.appendChild(div);
  });
  
  // Scroll to bottom
  chatBox.scrollTop = chatBox.scrollHeight;
}

/**
 * Initialize chat functionality
 */
function initChat() {
  const chatInput = document.getElementById('chatInput');
  const sendBtn = document.getElementById('sendBtn');
  
  if (!chatInput || !sendBtn) return;
  
  // Send message on button click
  sendBtn.addEventListener('click', () => {
    const text = chatInput.value;
    if (text.trim()) {
      sendChatMessage(text);
      chatInput.value = '';
      renderChatMessages();
    }
  });
  
  // Send message on Enter key
  chatInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      sendBtn.click();
    }
  });
  
  // Listen for chat updates from other tabs
  window.addEventListener('storage', (e) => {
    if (e.key === 'chatMessages') {
      renderChatMessages();
    }
  });
  
  // Listen for custom chat update events
  window.addEventListener('chatUpdated', renderChatMessages);
  
  // Initial render
  renderChatMessages();
}

// ===== GOAL MANAGEMENT =====

/**
 * Render goals (tasks displayed as goals)
 */
function renderGoals() {
  const goalsBody = document.getElementById('goalsBody');
  const emptyMessage = document.getElementById('empty');
  
  if (!goalsBody) return;
  
  const tasks = getTasks();
  goalsBody.innerHTML = '';
  
  if (tasks.length === 0) {
    if (emptyMessage) emptyMessage.style.display = 'block';
    return;
  }
  
  if (emptyMessage) emptyMessage.style.display = 'none';
  
  tasks.forEach(task => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${escapeHtml(task.title)}</td>
      <td>${escapeHtml(task.type)}</td>
      <td>${formatDate(task.deadline)}</td>
      <td>${escapeHtml(task.owner)}</td>
    `;
    goalsBody.appendChild(tr);
  });
}

// ===== TIME TRACKING =====

/**
 * Parse time string (e.g., "2h 30m" or "150m")
 */
function parseTimeString(timeStr) {
  if (!timeStr) return 0;
  
  let totalMinutes = 0;
  const hourMatch = timeStr.match(/(\d+)h/);
  const minMatch = timeStr.match(/(\d+)m/);
  
  if (hourMatch) totalMinutes += parseInt(hourMatch[1]) * 60;
  if (minMatch) totalMinutes += parseInt(minMatch[1]);
  
  return totalMinutes;
}

/**
 * Format minutes to time string
 */
function formatTimeString(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  
  if (hours > 0 && mins > 0) return `${hours}h ${mins}m`;
  if (hours > 0) return `${hours}h`;
  if (mins > 0) return `${mins}m`;
  return '0m';
}

/**
 * Add time to task
 */
function addTimeToTask(taskId, minutes) {
  const task = getTaskById(taskId);
  if (!task) return;
  
  const currentMinutes = parseTimeString(task.timeTracking);
  const newMinutes = currentMinutes + minutes;
  
  updateTask(taskId, {
    timeTracking: formatTimeString(newMinutes)
  });
}

// ===== ANALYTICS =====

/**
 * Calculate productivity statistics
 */
function getProductivityStats() {
  const tasks = getTasks();
  
  const stats = {
    total: tasks.length,
    completed: tasks.filter(t => t.status === 'Completed').length,
    pending: tasks.filter(t => t.status === 'Pending').length,
    inProgress: tasks.filter(t => t.status === 'In Progress').length,
    overdue: 0,
    totalTime: 0,
    averageProgress: 0
  };
  
  // Calculate overdue tasks
  const now = new Date();
  stats.overdue = tasks.filter(t => {
    if (!t.deadline || t.status === 'Completed') return false;
    return new Date(t.deadline) < now;
  }).length;
  
  // Calculate total time
  tasks.forEach(t => {
    stats.totalTime += parseTimeString(t.timeTracking);
  });
  
  // Calculate average progress
  if (tasks.length > 0) {
    const totalProgress = tasks.reduce((sum, t) => sum + (t.progress || 0), 0);
    stats.averageProgress = Math.round(totalProgress / tasks.length);
  }
  
  return stats;
}

/**
 * Get tasks by type distribution
 */
function getTaskTypeDistribution() {
  const tasks = getTasks();
  const distribution = {};
  
  tasks.forEach(task => {
    const type = task.type || 'General';
    distribution[type] = (distribution[type] || 0) + 1;
  });
  
  return distribution;
}

// ===== USER MANAGEMENT =====

/**
 * Login user
 */
function loginUser(username, password) {
  const users = getUsers();
  
  if (!users[username]) {
    return { success: false, message: 'User not found. Please sign up.' };
  }
  
  try {
    const encodedPassword = btoa(password);
    if (users[username] !== encodedPassword) {
      return { success: false, message: 'Incorrect password.' };
    }
  } catch (e) {
    return { success: false, message: 'Password encoding error.' };
  }
  
  localStorage.setItem('username', username);
  return { success: true, message: 'Login successful!' };
}

/**
 * Sign up new user
 */
function signupUser(username, password, role = 'Individual') {
  const users = getUsers();
  
  if (users[username]) {
    return { success: false, message: 'Username already exists.' };
  }
  
  if (password.length < 6) {
    return { success: false, message: 'Password must be at least 6 characters.' };
  }
  
  users[username] = btoa(password);
  saveUsers(users);
  
  localStorage.setItem('username', username);
  localStorage.setItem('role', role);
  
  return { success: true, message: 'Signup successful!' };
}

/**
 * Logout user
 */
function logoutUser() {
  localStorage.removeItem('username');
  localStorage.removeItem('role');
  window.location.href = 'Login.php';
}

/**
 * Update user display name
 */
function updateUserDisplay() {
  const userNameDisplay = document.getElementById('userNameDisplay');
  if (userNameDisplay) {
    const username = getCurrentUser();
    userNameDisplay.textContent = username;
  }
  
  // Update avatar with initials
  const avatar = document.querySelector('.avatar');
  if (avatar) {
    const username = getCurrentUser();
    avatar.textContent = getInitials(username);
  }
}

// ===== MODAL MANAGEMENT =====

/**
 * Show modal
 */
function showModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'flex';
    modal.classList.add('show');
  }
}

/**
 * Hide modal
 */
function hideModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
    modal.classList.remove('show');
  }
}

/**
 * Initialize modal close on outside click
 */
function initModals() {
  window.addEventListener('click', (event) => {
    if (event.target.classList.contains('modal')) {
      event.target.style.display = 'none';
      event.target.classList.remove('show');
    }
  });
}

// ===== NOTIFICATION SYSTEM =====

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: slideInRight 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// ===== SEARCH & FILTER =====

/**
 * Search tasks by keyword
 */
function searchTasks(keyword) {
  if (!keyword) return getTasks();
  
  const tasks = getTasks();
  const lowerKeyword = keyword.toLowerCase();
  
  return tasks.filter(task => {
    return (
      task.title.toLowerCase().includes(lowerKeyword) ||
      task.description.toLowerCase().includes(lowerKeyword) ||
      task.owner.toLowerCase().includes(lowerKeyword) ||
      task.type.toLowerCase().includes(lowerKeyword)
    );
  });
}

// ===== EXPORT FUNCTIONALITY =====

/**
 * Export tasks to CSV
 */
function exportTasksToCSV() {
  const tasks = getTasks();
  
  if (tasks.length === 0) {
    alert('No tasks to export!');
    return;
  }
  
  // CSV header
  let csv = 'Title,Type,Owner,Deadline,Status,Progress,Time Tracking\n';
  
  // CSV rows
  tasks.forEach(task => {
    csv += `"${task.title}","${task.type}","${task.owner}","${task.deadline}","${task.status}","${task.progress}%","${task.timeTracking}"\n`;
  });
  
  // Download CSV
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `tasks_${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
}

// ===== INITIALIZATION =====

/**
 * Initialize all functionality on page load
 */
document.addEventListener('DOMContentLoaded', () => {
  console.log('Productivity Hub initialized');
  
  // Initialize sidebar
  initSidebar();
  
  // Update user display
  updateUserDisplay();
  
  // Initialize modals
  initModals();
  
  // Initialize chat if on collaboration page
  if (document.getElementById('chatBox')) {
    initChat();
  }
  
  // Initialize goals if on goals page
  if (document.getElementById('goalsBody')) {
    renderGoals();
    
    // Listen for task updates
    window.addEventListener('tasksUpdated', renderGoals);
    window.addEventListener('storage', (e) => {
      if (e.key === 'tasks' || e.key === 'tasks_last_update') {
        renderGoals();
      }
    });
  }
  
  // Check if user is logged in
  const currentPage = window.location.pathname.split('/').pop();
  const username = getCurrentUser();
  
  if (!username || username === 'Guest') {
    if (currentPage !== 'Login.php' && currentPage !== '') {
      console.log('User not logged in, redirecting...');
      // Uncomment to enforce login
      // window.location.href = 'Login.php';
    }
  }
});

// ===== EXPORT FUNCTIONS FOR GLOBAL USE =====
window.ProductivityHub = {
  // Sidebar
  toggleSidebar,
  
  // Tasks
  getTasks,
  saveTasks,
  addTask,
  updateTask,
  deleteTask,
  getTaskById,
  filterTasks,
  searchTasks,
  
  // Chat
  getChatMessages,
  sendChatMessage,
  renderChatMessages,
  
  // Goals
  renderGoals,
  
  // Time
  addTimeToTask,
  parseTimeString,
  formatTimeString,
  
  // Analytics
  getProductivityStats,
  getTaskTypeDistribution,
  
  // User
  loginUser,
  signupUser,
  logoutUser,
  getCurrentUser,
  getCurrentRole,
  
  // UI
  showModal,
  hideModal,
  showNotification,
  
  // Export
  exportTasksToCSV,
  
  // Utilities
  escapeHtml,
  formatDate,
  getInitials
};

