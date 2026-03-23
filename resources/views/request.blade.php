<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skill Barter - Skill Match Filter</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

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
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <!-- Filter -->
            <div class="filter-section mb-4">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold m-0 text-dark">
                            <i class="fas fa-bolt me-2 text-warning"></i> Find Your Skill
                        </h4>
                    </div>
                    <div class="col-md-7">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="skillFilter"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Search skill..."
                                   onkeyup="filterRequests()">
                        </div>
                    </div>
                </div>
            </div>

            <div id="requestList"></div>

        </div>
    </div>
</div>

<!-- ================= PROFILE MODAL ================= -->
<div class="modal fade" id="profileModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
<div class="modal-body p-4">

<div class="d-flex align-items-center justify-content-between mb-4">
<div class="d-flex align-items-center">
<div class="user-circle me-3"><i class="fas fa-user"></i></div>
<div>
<h5 class="fw-bold mb-0 text-dark" id="modalUserId"></h5>
</div>
</div>
<div class="medal-box"><i class="fas fa-medal"></i></div>
</div>

<div class="mb-3">
<p class="text-muted small fw-bold text-uppercase mb-2">Main Skill</p>
<div class="p-2 border rounded bg-light fw-bold text-primary" id="modalRequestedSkill"></div>
</div>

<div class="mb-4">
<p class="text-muted small fw-bold text-uppercase mb-2">All Skills</p>
<div id="modalKnownSkills"></div>
</div>

<div class="d-grid gap-2">
<button type="button" class="btn btn-dark rounded-pill"
data-bs-dismiss="modal">
Close Profile
</button>
</div>

</div>
</div>
</div>
</div>

<button onclick="history.back()" class="btn btn-secondary m-3">
Go Back
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let requests = [];

// ================= FETCH USERS =================
async function fetchUsers() {
    try {
        const res = await fetch('/users', {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        requests = await res.json();
        renderRequests(requests);

    } catch (err) {
        console.error(err);
        document.getElementById("requestList").innerHTML =
            "<div class='text-danger text-center py-5'>Unable to load users.</div>";
    }
}

// ================= RENDER =================
function renderRequests(data) {
    const container = document.getElementById("requestList");
    container.innerHTML = "";

    if (!data || data.length === 0) {
        container.innerHTML =
            "<div class='text-muted text-center py-5'>No users found.</div>";
        return;
    }

    data.forEach(r => {
        const username = r.user?.username ?? 'User';
        const userId = r.user?.id ?? null;

        container.innerHTML += `
        <div class="card request-card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-primary-subtle text-primary mb-1">
                        ${username}
                    </span>
                    <h6 class="fw-bold text-secondary">
                        Teaches: ${r.name}
                    </h6>
                </div>

                <div class="d-flex gap-2">
                    ${userId ? `
                        <button class="btn btn-sm btn-outline-primary"
                                onclick="openProfile(${userId})">
                                Profile
                        </button>
                        <button class="btn btn-sm btn-success"
                                onclick="acceptRequest(${userId})">
                                Accept
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>`;
    });
}

// ================= FILTER =================
function filterRequests() {
    const val = document.getElementById("skillFilter").value.toLowerCase();

    const filtered = requests.filter(r =>
        r.name.toLowerCase().includes(val) ||
        (r.user?.username ?? '').toLowerCase().includes(val)
    );

    renderRequests(filtered);
}

// ================= OPEN PROFILE =================
async function openProfile(userId) {
    try {
        const res = await fetch(`/user/${userId}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) {
            const text = await res.text(); // debug
            console.error("Server Response:", text);
            throw new Error("HTTP " + res.status);
        }

        const data = await res.json();

        document.getElementById("modalUserId").innerText =
            `${data.name} (ID: ${data.id})`;

        document.getElementById("modalRequestedSkill").innerText =
            data.skills?.length ? data.skills[0].name : 'N/A';

        document.getElementById("modalKnownSkills").innerHTML =
            data.skills?.length
                ? data.skills.map(s =>
                    `<span class="badge-skill">${s.name}</span>`
                  ).join('')
                : "<span class='text-muted'>No skills available</span>";

        const modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('profileModal')
        );
        modal.show();

    } catch (err) {
        console.error(err);
        alert("Failed to load profile.");
    }
}

// ================= ACCEPT REQUEST =================
async function acceptRequest(userId) {
    try {
        const res = await fetch('/accept-request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Error');

        alert("Request Accepted ✅");

    } catch (err) {
        console.error(err);
        alert("Failed to accept request.");
    }
}

// ================= INIT =================
document.addEventListener("DOMContentLoaded", fetchUsers);
</script>

</body>
</html>
