<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <!-- ✅ CSRF TOKEN (VERY IMPORTANT) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<h2>Login Page</h2>

<input type="text" id="email" placeholder="Email"><br><br>
<input type="password" id="password" placeholder="Password"><br><br>

<button onclick="login()">Login</button>

<!-- ✅ ADD JS HERE -->
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
    .then(async res => {
        const data = await res.json();

        if (!res.ok) {
            alert(data.message || "Invalid credentials");
            return;
        }

        alert(data.message);
        window.location.href = '/dashboard';
    })
    .catch(err => {
        alert("Something went wrong");
    });
}
</script>

</body>
</html>