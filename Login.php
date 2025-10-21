<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #0b111a;
      display:flex;
      align-items:center;
      justify-content:center;
      height:100vh;
      margin:0;
    }
    .login-container {
      background:#fff;
      padding:30px 40px;
      border-radius:10px;
      display:flex;
      gap:40px;
      box-shadow:0 4px 20px rgba(0,0,0,0.25);
      min-width:520px;
    }
    .left { 
      display:flex; 
      flex-direction:column;
       gap:12px; 
      }

    .logo { 
      font-size:22px; 
      font-weight:700; 
      color:#007bff; 
    }
    
    input[type="text"], input[type="password"], select {
      width:220px; 
      padding:10px; 
      font-size:14px; 
      border:2px solid #ccc; 
      border-radius:6px;
    }

    .btn-row { 
      display:flex; 
      gap:10px; 
      margin-top:6px; 
    }
    
    button { 
      padding:10px 14px; 
      border:0; 
      border-radius:6px; 
      cursor:pointer; 
      background:#007bff; 
      color:#fff;
      font-weight: bold; 
    }
    
    button.signup { 
      background:#28a745; 
    }
    
    .right { 
      display:flex; 
      flex-direction:column; 
      gap:12px; 
    }
    .welcome { 
      font-weight:600; 
      color:#0b111a; 
      display:none; 
      margin-top:8px; 
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      body {
        padding:20px;
        height:auto;
      }

      .login-container {
        flex-direction: column;
        gap: 20px;
        min-width: auto;
        width: 100%;
        max-width: 400px;
        box-shadows: 0 3px 15px rgba(0,0,0,0.2);
      }

      input[type="text"], input[type="password"], select {
        width: 100%;
        font-size: 16px;
        padding: 12px;
    }

    .btn-row {
      flex-direction: column;
      gap: 12px;
    }

    button {
      width: 100%;
      font-size: 16px;
      padding: 12px;
    }

    .logo {
      text-align: center;
      font-size: 26px;
    }

    .right {
      align-items: center;
    }
  </style>
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
      <div style="font-weight:bold;color:#0b111a;">Role:</div>
      <select id="role">
        <option>Individual</option>
        <option>Team Manager</option>
      </select>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
        <button type="button" onclick="setRole('Individual')">Individual</button>
        <button type="button" onclick="setRole('Team Manager')">Team Manager</button>
      </div>
    </div>
  </div>

  <script>
  // login/signup logic (same as yours)
  function _getUsers() {
    try { return JSON.parse(localStorage.getItem('users') || '{}'); }
    catch (e) { console.error('users parse error', e); return {}; }
  }
  function _saveUsers(obj) { localStorage.setItem('users', JSON.stringify(obj)); }

  function loginHandler() {
    const u = document.getElementById('username')?.value.trim();
    const p = document.getElementById('password')?.value;
    if (!u || !p) { alert('Enter username and password.'); return; }
    const users = _getUsers();
    if (!users[u]) { alert('User not found. Please sign up.'); return; }
    try {
      if (users[u] !== btoa(p)) { alert('Incorrect password.'); return; }
    } catch (e) { alert('Password encoding error. Use regular characters.'); return; }
    localStorage.setItem('username', u);
    const roleEl = document.getElementById('role');
    if (roleEl) localStorage.setItem('role', roleEl.value);
    window.location.href = 'Dashboard.php';
  }

  function signupHandler() {
    const u = prompt('Choose a username:'); if (!u) return;
    const p = prompt('Choose a password (min 6 chars):'); if (!p) return;
    if (p.length < 6) { alert('Password must be at least 6 characters.'); return; }
    const users = _getUsers();
    if (users[u]) { alert('Username already exists.'); return; }
    users[u] = btoa(p); _saveUsers(users);
    localStorage.setItem('username', u);
    const roleEl = document.getElementById('role'); if (roleEl) localStorage.setItem('role', roleEl.value);
    alert('Signup complete. Redirecting to dashboard.');
    window.location.href = 'Dashboard.php';
  }

  document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    loginBtn.addEventListener('click', loginHandler);
    signupBtn.addEventListener('click', signupHandler);
  });
  </script>
</body>
</html>