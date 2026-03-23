<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillBarter | Learn Without Money</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Inter', sans-serif;
}

body{
    background:#f8fafc;
    color:#1e293b;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 8%;
    background:white;
    box-shadow:0 2px 15px rgba(0,0,0,.05);
    position:sticky;
    top:0;
    z-index:100;
}

.logo{
    font-size:22px;
    font-weight:800;
    color:#2563eb;
}

.nav-links a{
    text-decoration:none;
    margin-left:25px;
    color:#334155;
    font-weight:500;
}

.nav-btn{
    background:#2563eb;
    color:white;
    padding:10px 22px;
    border-radius:25px;
}

/* HERO */
.hero{
    padding:120px 8%;
    text-align:center;
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:white;
}

.hero h1{
    font-size:56px;
    font-weight:800;
    margin-bottom:20px;
}

.hero p{
    font-size:20px;
    max-width:650px;
    margin:auto;
    opacity:0.9;
}

.hero .btn{
    display:inline-block;
    margin-top:35px;
    padding:14px 32px;
    background:white;
    color:#2563eb;
    font-weight:600;
    border-radius:30px;
    text-decoration:none;
    transition:0.3s;
}

.hero .btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

/* SECTION */
.section{
    padding:100px 8%;
    text-align:center;
}

.section h2{
    font-size:36px;
    margin-bottom:60px;
}

/* FEATURES */
.features{
    display:flex;
    justify-content:center;
    gap:40px;
    flex-wrap:wrap;
}

.card{
    background:white;
    padding:35px;
    width:280px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-8px);
    box-shadow:0 20px 40px rgba(0,0,0,.12);
}

.card h3{
    margin-bottom:15px;
    font-size:20px;
}

.card p{
    color:#64748b;
    font-size:15px;
}

/* CTA */
.cta{
    background:#1e293b;
    color:white;
    text-align:center;
    padding:80px 8%;
}

.cta h2{
    font-size:36px;
    margin-bottom:20px;
}

.cta a{
    display:inline-block;
    margin-top:25px;
    padding:14px 30px;
    background:#2563eb;
    border-radius:30px;
    text-decoration:none;
    color:white;
    font-weight:600;
}

/* FOOTER */
.footer{
    background:#0f172a;
    color:#94a3b8;
    text-align:center;
    padding:30px;
    font-size:14px;
}

@media(max-width:768px){
    .hero h1{
        font-size:38px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">SkillBarter</div>
    <div class="nav-links">
        <a href="#">Features</a>
        <a href="#">How It Works</a>
        <a href="/login" class="nav-btn">Login</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>Exchange Skills. Grow Together.</h1>
    <p>
        A modern platform where people teach what they know and 
        learn what they need — without spending money.
    </p>
    <a href="/login" class="btn">Get Started Free</a>
</div>

<!-- FEATURES -->
<div class="section">
    <h2>Powerful Features</h2>

    <div class="features">
        <div class="card">
            <h3>🎓 Learn New Skills</h3>
            <p>Discover experts ready to teach what you want to learn.</p>
        </div>

        <div class="card">
            <h3>🤝 Skill Exchange</h3>
            <p>Trade knowledge instead of money and grow faster.</p>
        </div>

        <div class="card">
            <h3>💬 Real-time Chat</h3>
            <p>Connect instantly and schedule learning sessions.</p>
        </div>

        <div class="card">
            <h3>⭐ Reputation System</h3>
            <p>Earn ratings and build a trusted learning profile.</p>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="cta">
    <h2>Ready to Start Learning?</h2>
    <p>Join the SkillBarter community today.</p>
    <a href="/register">Create Free Account</a>
</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 SkillBarter Network. All rights reserved.
</div>

</body>
</html>
