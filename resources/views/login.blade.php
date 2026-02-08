<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Skill Bater</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background:#f5f7fb; overflow-x:hidden; }
.hero-section { min-height:100vh; display:flex; align-items:center; justify-content:center; }
.auth-card { background:#fff; padding:40px 30px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.1); max-width:400px; width:100%; }
.form-control { border-radius:30px; padding:12px 20px; }
.btn-main { border-radius:30px; padding:12px; background:#0d6efd; color:#fff; font-weight:500; width:100%; }
.btn-main:hover { background:#0b5ed7; }
.toggle-btn { cursor:pointer; color:#0d6efd; }
.hidden { display:none; }
.alert { border-radius:15px; padding:10px 20px; font-size:14px; margin-bottom:15px; }
</style>
</head>
<body>

<section class="hero-section">
  <div class="auth-card">

    <!-- Messages -->
    <div id="successMessage" class="alert alert-success hidden"></div>
    <div id="errorMessage" class="alert alert-danger hidden"></div>

    <h4 class="text-center mb-4" id="formTitle">Register</h4>

    <!-- REGISTER FORM -->
    <form id="registerForm">
      @csrf
      <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
      <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
      <input type="text" name="mobile" class="form-control mb-3" placeholder="Mobile Number" required>
      <input type="text" name="gender" class="form-control mb-3" placeholder="Gender">
      <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
      <input type="text" name="captcha" class="form-control mb-3" placeholder="Enter Captcha" required>
      <div id="captchaCode" class="mb-3 fw-bold"></div>
      <button type="button" class="btn-main mb-2" onclick="registerUser()">Register</button>
    </form>

    <!-- LOGIN FORM -->
    <form id="loginForm" class="hidden">
      @csrf
      <input type="text" class="form-control mb-3" name="user_id_or_email" placeholder="User ID or Email">
      <input type="password" class="form-control mb-3" name="password" placeholder="Password">
      <button type="button" class="btn btn-main w-100" onclick="loginUser(event)">Login</button>
      <p class="text-center mt-2">
        <a href="{{ route('forgot.password') }}" class="text-primary">Forgot Password?</a>
      </p>
    </form>

    <!-- FORGOT PASSWORD FORM -->
    <form id="forgotForm" class="hidden">
      @csrf
      <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
      <button type="button" class="btn-main mb-2" onclick="forgotPassword()">Send Reset Link</button>
      <p class="text-center mt-2"><span class="toggle-btn" onclick="toggleLogin()">Back to Login</span></p>
    </form>

    <p class="text-center mt-3">
      <span class="toggle-btn" onclick="toggleForm()">Login</span>
    </p>

  </div>
</section>

<script>
let isRegister = true;

// Toggle between Register and Login forms
function toggleForm() {
    isRegister = !isRegister;
    document.getElementById('formTitle').innerText = isRegister ? 'Register' : 'Login';
    document.getElementById('registerForm').classList.toggle('hidden', !isRegister);
    document.getElementById('loginForm').classList.toggle('hidden', isRegister);
    document.getElementById('forgotForm').classList.add('hidden');
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.innerText = isRegister ? 'Login' : 'Register';
    });
    clearMessages();
}

// Toggle Forgot Password form
function toggleForgot() {
    document.getElementById('loginForm').classList.add('hidden');
    document.getElementById('forgotForm').classList.remove('hidden');
    document.getElementById('registerForm').classList.add('hidden');
    clearMessages();
}

// Back to Login
function toggleLogin() {
    document.getElementById('loginForm').classList.remove('hidden');
    document.getElementById('forgotForm').classList.add('hidden');
    clearMessages();
}

// Show message
function showMessage(msg, type = 'error') {
    clearMessages();
    const el = type === 'error' ? document.getElementById('errorMessage') : document.getElementById('successMessage');
    el.innerHTML = msg;
    el.classList.remove('hidden');
}

// Clear all messages
function clearMessages() {
    document.getElementById('successMessage').classList.add('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
}

// -------- AJAX Registration --------
function registerUser() {
    const form = document.getElementById('registerForm');
    const data = new FormData(form);

    fetch('{{ route("register") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showMessage(`🎉 ${res.message}<br>User ID: <b>${res.user_id}</b>`, 'success');
            form.reset();
            loadCaptcha(); // refresh captcha after registration
        } else {
            let msg = typeof res.message === 'object' ? Object.values(res.message).flat().join('<br>') : res.message;
            showMessage(msg, 'error');
            loadCaptcha();
        }
    })
    .catch(err => showMessage('Server error', 'error'));
}

// -------- AJAX Login --------
function loginUser(e) {
    e.preventDefault();

    const form = document.getElementById("loginForm");
    const userIdOrEmail = form.querySelector('input[name="user_id_or_email"]').value.trim();
    const password = form.querySelector('input[name="password"]').value.trim();

    if (!userIdOrEmail || !password) {
        return showMessage('Enter User ID/Email and password', 'error');
    }

    fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id_or_email: userIdOrEmail,
            password: password
        })
    })
    .then(async res => {
        const data = await res.json().catch(() => ({ success: false, message: 'Server error' }));
        if (res.ok && data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => window.location.href = '/dashboard', 1000);
        } else {
            showMessage(data.message || 'Invalid credentials', 'error');
        }
    })
    .catch(err => {
        console.error('Login fetch error:', err);
        showMessage('Server error', 'error');
    });
}

// -------- AJAX Forgot Password --------
function forgotPassword() {
    const form = document.getElementById('forgotForm');
    const data = new FormData(form);

    fetch('{{ route("forgot.password") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            showMessage(res.message, 'success');
            form.reset();
        } else {
            let msg = typeof res.message === 'object' ? Object.values(res.message).flat().join('<br>') : res.message;
            showMessage(msg, 'error');
        }
    })
    .catch(err => showMessage('Server error', 'error'));
}

// -------- CAPTCHA Handling --------
async function loadCaptcha() {
    try {
        const res = await fetch('/captcha'); // Ensure this route exists
        const data = await res.json();
        document.getElementById('captchaCode').innerText = data.captcha;
    } catch (err) {
        console.error('Captcha fetch error', err);
        document.getElementById('captchaCode').innerText = 'Error loading captcha';
    }
}

function refreshCaptcha() {
    loadCaptcha();
}

// Load captcha on page load
window.addEventListener('DOMContentLoaded', () => {
    loadCaptcha();
});

// -------- ENTER KEY SUBMIT --------
window.addEventListener('keydown', function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        if (!document.getElementById('registerForm').classList.contains('hidden')) {
            registerUser();
        } else if (!document.getElementById('loginForm').classList.contains('hidden')) {
            loginUser(e);
        } else if (!document.getElementById('forgotForm').classList.contains('hidden')) {
            forgotPassword();
        }
    }
});
</script>


</body>
</html>
