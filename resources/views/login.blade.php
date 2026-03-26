<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Skill Bater</title>

<!-- ✅ CSRF TOKEN (VERY IMPORTANT) -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f5f7fb; font-family:Arial; }
.auth-card { max-width:400px; margin:auto; margin-top:80px; padding:30px; background:#fff; border-radius:15px; }
.hidden { display:none; }
</style>
</head>

<body>

<div class="auth-card">

    <h3 id="title">Register</h3>

    <!-- MESSAGE -->
    <div id="msg" style="color:red;"></div>

    <!-- REGISTER -->
    <form id="registerForm">
        @csrf
        <input type="text" name="username" placeholder="Username" class="form-control mb-2">
        <input type="email" name="email" placeholder="Email" class="form-control mb-2">
        <input type="password" name="password" placeholder="Password" class="form-control mb-2">
        <button type="button" class="btn btn-primary w-100" onclick="registerUser()">Register</button>
    </form>

    <!-- LOGIN -->
    <form id="loginForm" class="hidden">
        @csrf
        <input type="text" name="user_id_or_email" placeholder="User ID or Email" class="form-control mb-2">
        <input type="password" name="password" placeholder="Password" class="form-control mb-2">
        <button type="button" class="btn btn-success w-100" onclick="loginUser()">Login</button>
    </form>

    <p class="text-center mt-3">
        <span onclick="toggle()" style="cursor:pointer;color:blue;">Switch</span>
    </p>

</div>

<script>

// 🔁 Toggle
function toggle(){
    const reg = document.getElementById('registerForm');
    const log = document.getElementById('loginForm');
    const title = document.getElementById('title');

    reg.classList.toggle('hidden');
    log.classList.toggle('hidden');

    title.innerText = reg.classList.contains('hidden') ? 'Login' : 'Register';
}

// 🔐 GET CSRF TOKEN
function getToken(){
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// 📝 REGISTER
function registerUser(){

    const form = document.getElementById('registerForm');
    const data = new FormData(form);

    fetch('/register', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getToken()
        },
        body: data
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('msg').innerText = data.message;
    })
    .catch(() => {
        document.getElementById('msg').innerText = 'Server error';
    });
}

// 🔑 LOGIN (FIXED)
function loginUser(){

    const form = document.getElementById('loginForm');

    const user = form.querySelector('[name="user_id_or_email"]').value;
    const pass = form.querySelector('[name="password"]').value;

    fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id_or_email: user,
            password: pass
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            window.location.href = '/dashboard';
        }else{
            document.getElementById('msg').innerText = data.message;
        }
    })
    .catch(() => {
        document.getElementById('msg').innerText = 'Server error';
    });
}

</script>

</body>
</html>