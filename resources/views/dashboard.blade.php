<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Skill-Barter Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { 
    background: linear-gradient(135deg,#eef2f7,#dde3ea); 
    font-family: 'Segoe UI', system-ui; 
    margin:0;
}
/* SIDEBAR */
.sidebar { 
    width:260px; 
    background: linear-gradient(180deg,#3b82f6,#2563eb); 
    color:white; 
    border-radius:22px; 
    min-height:100vh; 
    padding:2rem 1.2rem; 
    position:fixed;
}
.sidebar h5 { font-weight:700;margin-bottom:2rem; }
.sidebar .nav-link { color:white; padding:12px 16px; border-radius:14px; transition:.3s; display:block;}
.sidebar .nav-link:hover,.sidebar .nav-link.active { background:rgba(255,255,255,.2); }

/* MAIN CONTENT */
.main-content { 
    margin-left:280px; 
    padding:2rem; 
}

/* TOPBAR */
.topbar { 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    margin-bottom:2rem; 
    position:relative;
}
.search-box { border-radius:18px; padding:12px 16px; width:100%; }
.search-box:focus { box-shadow:0 0 0 3px rgba(59,130,246,.25); }

/* Profile avatar */
.profile-avatar {
    width:45px;
    height:45px;
    border-radius:50%;
    background:#4f46e5;
    color:white;
    font-size:20px;
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    transition: transform .2s;
}
.profile-avatar:hover { transform: scale(1.1); }

/* EDUCATION HERO */
.edu-hero { 
    background: linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)), 
    url("https://images.unsplash.com/photo-1524995997946-a1c2e315a42f"); 
    background-size: cover; 
    background-position: center; 
    border-radius:28px; 
    padding:90px 60px; 
    color:white; 
    box-shadow:0 25px 45px rgba(0,0,0,.3); 
}
.edu-hero h1 { font-size:2.6rem; font-weight:700; }
.edu-hero p { font-size:1.2rem; max-width:720px; opacity:.95; }



/* Responsive */
@media(max-width:768px){
    .main-content { margin-left:0; padding:1rem; }
    .sidebar { position:relative; width:100%; border-radius:0; min-height:auto; margin-bottom:1rem;}
}


</style>
</head>

<body>

<div class="sidebar shadow-lg">
<h5>Skill-Barter Network</h5>
<ul class="nav flex-column gap-2">
<li><a class="nav-link active">Dashboard</a></li>
<li><a class="nav-link" href="{{ route('myskill') }}">My Skills</a></li>
<li><a class="nav-link" href="{{route('request')}}">Requests</a></li>
<li><a class="nav-link"href="{{ route('messages') }}">Messages</a></li>
</ul>
</div>

<div class="main-content">

<!-- TOP BAR -->
<div class="topbar mb-4 position-relative">
    <div class="input-group w-50" style="position: relative;">
        <span class="input-group-text bg-white border-0">
            <i class="bi bi-search"></i>
        </span>
        <input id="searchInput" class="form-control border-0 search-box" placeholder="Search skills (Python, AI, UI)">

        <!-- Search results dropdown -->
        <div id="searchResults" 
             class="bg-white border shadow rounded" 
             style="
                 position: absolute;
                 top: 100%;
                 left: 0;
                 width: 100%;
                 z-index: 1000;
                 display: none;
                 max-height: 300px;
                 overflow-y: auto;
             ">
        </div>
    </div>
</div>

<a href="{{ route('profile') }}" style="text-decoration:none;">
    <div class="profile"
         style="
            position:fixed;
            top:20px;
            right:20px;
            width:48px;
            height:48px;
            border-radius:50%;
            background:#4f46e5;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            box-shadow:0 8px 20px rgba(0,0,0,.25);
 <p>Your User ID is: <strong>{{ $userId }}</strong></p>
         ">
        <i class="bi bi-person-fill"
           style="font-size:22px;color:white;"></i>
    </div>
</a>



<!-- EDUCATION HERO -->
<div class="edu-hero">
<h1>“Education is the most powerful weapon</h1>
<h1>which you can use to change the world.”</h1>
<p class="mt-4">
Skill-Barter connects learners and mentors to share knowledge,
grow skills, and build a smarter future together.
</p>
<span class="mt-3 d-block fst-italic">— Nelson Mandela</span>
</div>

</div>




<script>
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

// CSRF token from meta tag
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Fetch and display skills on input
searchInput.addEventListener('input', function() {
    const query = this.value.trim();
    if (!query) {
        searchResults.style.display = 'none';
        searchResults.innerHTML = '';
        return;
    }

    fetch(`/search-skills?q=${encodeURIComponent(query)}`, {
        headers: { 'X-CSRF-TOKEN': token }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not OK');
        return res.json();
    })
    .then(data => {
        if (!data.length) {
            searchResults.innerHTML = '<div class="p-2 text-muted">No results found</div>';
            searchResults.style.display = 'block';
            return;
        }

        searchResults.innerHTML = data.map(r => `
            <div class="card mb-2 shadow-sm p-2 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 fw-bold">${r.skill_name ?? 'Unknown Skill'}</h6>
                    <small>by ${r.user?.username ?? 'Unknown User'}</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="openProfile(${r.user?.id ?? 0})">Profile</button>
                    <button class="btn btn-sm btn-success" onclick="sendRequest(event, ${r.id ?? 0}, ${r.user?.id ?? 0})">Request</button>
                </div>
            </div>
        `).join('');

        searchResults.style.display = 'block';
    })
    .catch(err => {
        console.error('Error fetching skills:', err);
        searchResults.innerHTML = '<div class="p-2 text-danger">Error fetching results</div>';
        searchResults.style.display = 'block';
    });
});

// Hide dropdown when clicking outside
document.addEventListener('click', e => {
    if (!searchResults.contains(e.target) && e.target !== searchInput) {
        searchResults.style.display = 'none';
    }
});

// Send request to user
function sendRequest(event, skillId, userId) {
    fetch(`/api/send-request`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ skill_id: skillId, user_id: userId })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message ?? 'Request sent');
        if (event && event.target) {
            event.target.disabled = true;
            event.target.innerText = "Requested";
        }
    })
    .catch(err => {
        console.error('Error sending request:', err);
        alert('Error sending request');
    });
}

// Open user profile
function openProfile(userId) {
    if (!userId) return alert('Invalid user ID');
    window.location.href = `/profile/${userId}`;
}
</script>









</body>
</html>
