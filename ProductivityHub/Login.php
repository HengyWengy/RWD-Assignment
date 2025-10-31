<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - Productivity Hub</title>
  <!-- External CSS -->
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="login-container">
    <div class="left">
      <div class="logo">Login</div>

      <input id="username" type="text" placeholder="Username" autocomplete="username">
      <input id="password" type="password" placeholder="Password" autocomplete="current-password">

      <div class="btn-row">
        <button id="loginBtn" type="button">LOGIN</button>
        <button id="signupBtn" class="signup" type="button">SIGN UP</button>
      </div>
    </div> 

    <div class="right">
      <div class="role-label">Role:</div>
      <select id="role">
        <option>Individual</option>
        <option>Team Manager</option>
      </select>
      <div class="role-buttons">
        <button type="button" onclick="setRole('Individual')">Individual</button>
        <button type="button" onclick="setRole('Team Manager')">Team Manager</button>
      </div>
    </div>
  </div>

  <!-- External JavaScript -->
  <script src="js/script.js"></script>
  
  <script>
  // Set role function
  function setRole(roleName) {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
      roleSelect.value = roleName;
      ProductivityHub.showNotification('Role set to: ' + roleName, 'info');
    }
  }

  // Login handler
  function loginHandler() {
    const u = document.getElementById('username')?.value.trim();
    const p = document.getElementById('password')?.value;
    
    if (!u || !p) { 
      alert('Enter username and password.'); 
      return; 
    }
    
    const result = ProductivityHub.loginUser(u, p);
    
    if (!result.success) {
      alert(result.message);
      return;
    }
    
    // Save role
    const roleEl = document.getElementById('role');
    if (roleEl) {
      localStorage.setItem('role', roleEl.value);
    }
    
    // Redirect to dashboard
    window.location.href = 'Dashboard.php';
  }

  // Signup handler
  function signupHandler() {
    const u = prompt('Choose a username:'); 
    if (!u) return;
    
    const p = prompt('Choose a password (min 6 chars):'); 
    if (!p) return;
    
    const roleEl = document.getElementById('role');
    const role = roleEl ? roleEl.value : 'Individual';
    
    const result = ProductivityHub.signupUser(u, p, role);
    
    if (!result.success) {
      alert(result.message);
      return;
    }
    
    alert('Signup complete. Redirecting to dashboard.');
    window.location.href = 'Dashboard.php';
  }

  // Event listeners
  document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const passwordInput = document.getElementById('password');
    
    if (loginBtn) loginBtn.addEventListener('click', loginHandler);
    if (signupBtn) signupBtn.addEventListener('click', signupHandler);
    
    // Allow Enter key to login
    if (passwordInput) {
      passwordInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          loginHandler();
        }
      });
    }
  });
  </script>
</body>
</html>

