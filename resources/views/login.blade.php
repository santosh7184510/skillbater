<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<h2>Login Page</h2>

<input type="text" id="email" placeholder="Email"><br><br>
<input type="password" id="password" placeholder="Password"><br><br>

<button onclick="login()">Login</button>

<script>
function login() {
    fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        window.location.href = '/dashboard';
    });
}
</script>

</body>
</html>