<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skill Barter - Skill Match Filter</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
.filter-section { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.request-card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: 0.2s; }
.request-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
.user-circle { width: 55px; height: 55px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; border: 2px solid #dbeafe; }
.medal-box { width: 45px; height: 45px; background: radial-gradient(circle, #fffbeb, #fef3c7); border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1px solid #fde68a; }
.medal-box i { font-size: 1.4rem; color: #d97706; }
.badge-skill { background: #e0f2fe; color: #0369a1; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; margin: 2px; display: inline-block; font-weight: 500; }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="filter-section mb-4">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold m-0 text-dark"><i class="fas fa-bolt me-2 text-warning"></i> Find Your Skill</h4>
                    </div>
                    <div class="col-md-7">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="skillFilter" class="form-control border-start-0 ps-0" 
                                   placeholder="What skill do you want to learn? (e.g. Python)" onkeyup="filterRequests()">
                        </div>
                    </div>
                </div>
            </div>

            <div id="requestList"></div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
<div class="modal-body p-4">
<div class="d-flex align-items-center justify-content-between mb-4">
<div class="d-flex align-items-center">
<div class="user-circle me-3"><i class="fas fa-user"></i></div>
<div>
<h5 class="fw-bold mb-0 text-dark" id="modalUserId"></h5>
<div class="text-warning small">
<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
<span class="ms-1 text-muted fw-bold">4.8</span>
</div>
</div>
</div>
<div class="medal-box"><i class="fas fa-medal"></i></div>
</div>

<div class="mb-3">
<p class="text-muted small fw-bold text-uppercase mb-2">Current Exchange Request</p>
<div class="p-2 border rounded bg-light fw-bold text-primary" id="modalRequestedSkill"></div>
</div>

<div class="mb-4">
<p class="text-muted small fw-bold text-uppercase mb-2">All Skills They Can Teach</p>
<div id="modalKnownSkills"></div>
</div>

<div class="d-grid gap-2">
<button type="button" class="btn btn-dark rounded-pill py-2" data-bs-dismiss="modal">Close Profile</button>
</div>
</div>
</div>
</div>
</div>

<button onclick="history.back()" style="padding:10px 20px; border-radius:5px; cursor:pointer;">
  Go Back
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let requests = [];

// Fetch data from API
async function fetchUsers() {
    try {
        const res = await fetch('{{ route("api.users") }}');
        if(!res.ok) throw new Error('HTTP ' + res.status);
        requests = await res.json();
        renderRequests(requests);
    } catch(err) {
        console.error('Error fetching users:', err);
        document.getElementById("requestList").innerHTML = "<div class='text-center py-5 text-danger'>Unable to load users.</div>";
    }
}

// Render requests
function renderRequests(data) {
    const container = document.getElementById("requestList");
    container.innerHTML = data.length === 0 ? "<div class='text-center py-5 text-muted'>No users found with that skill.</div>" : "";

    data.forEach(r => {
        container.innerHTML += `
        <div class="card request-card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center p-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary mb-1">${r.id}</span>
                    <h6 class="mb-0 fw-bold text-secondary">${r.reqSkill}</h6>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary fw-bold" onclick="openProfile(${r.user_id})">Profile</button>
                    <button class="btn btn-sm btn-success px-3" onclick="handleRequest('accept', ${r.skill_id}, ${r.user_id})">Accept</button>
                    <button class="btn btn-sm btn-danger px-3" onclick="handleRequest('reject', ${r.skill_id}, ${r.user_id})">Reject</button>
                </div>
            </div>
        </div>`;
    });
}

// Filter requests by known skill
function filterRequests() {
    const val = document.getElementById("skillFilter").value.toLowerCase();
    const filtered = requests.filter(r => r.known.some(s => s.toLowerCase().includes(val)) || r.id.toLowerCase().includes(val));
    renderRequests(filtered);
}

// Open user profile
async function openProfile(userId) {
    try {
        const res = await fetch(`/api/user/${userId}`);
        const data = await res.json();

        document.getElementById("modalUserId").innerText = data.id;
        document.getElementById("modalRequestedSkill").innerText = data.skills[0] ?? 'N/A';
        document.getElementById("modalKnownSkills").innerHTML = data.skills.map(s => `<span class="badge-skill">${s}</span>`).join('');

        new bootstrap.Modal(document.getElementById('profileModal')).show();
    } catch(err) {
        alert('Failed to load profile.');
    }
}

// Accept/Reject skill
async function handleRequest(action, skillId, toUserId) {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/api/skill-request', {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ skill_id: skillId, to_user: toUserId, action })
        });
        alert(`Skill request ${action}ed successfully`);
    } catch(err) {
        alert('Error processing request');
    }
}

// Initialize
fetchUsers();
</script>
</body>
</html>
