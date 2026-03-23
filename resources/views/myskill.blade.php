<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skill Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root { --primary-blue: #3b82f6; --bg-gray: #f0f2f5; }
body { background-color: var(--bg-gray); padding: 20px; }

.skill-block { 
    background: white; 
    padding: 20px; 
    border-radius: 12px; 
    margin-bottom: 20px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-left: 5px solid var(--primary-blue);
}

.upload-section { 
    display: none; 
    margin-top: 15px;
    padding: 15px;
    background: #eef2ff;
    border-radius: 8px;
}

.btn-add { color: var(--primary-blue); cursor: pointer; font-weight: bold; text-decoration: none; }
.btn-submit { background-color: var(--primary-blue); color: white; border: none; padding: 10px 30px; border-radius: 8px; }
</style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="mb-4 fw-bold"><i class="fas fa-tools me-2"></i> My Skills</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="skillForm" method="POST" action="{{ route('skills.store') }}" enctype="multipart/form-data">
                @csrf
                <div id="skillsWrapper">
                    <div class="skill-block">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Skill Name</label>
                            <input type="text" name="skills[0][name]" class="form-control" placeholder="Enter Skill" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Do you have a certification for this skill?</label>
                            <select name="skills[0][has_certificate]" class="form-select cert-dropdown" onchange="checkSelection(this)">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="upload-section">
                            <label class="form-label fw-bold text-primary">Upload Certificate (Image or PDF)</label>
                            <input type="file" name="skills[0][certificate]" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="javascript:void(0)" class="btn-add" onclick="addNewSkill()">
                        <i class="fas fa-plus-circle"></i> Add More Skill
                    </a>
                    <button type="submit" class="btn btn-submit shadow">Submit Skills</button>
                </div>
            </form>

            <button onclick="history.back()" class="btn btn-secondary mt-3">Go Back</button>
        </div>
    </div>
</div>

<script>
let skillIndex = 1;

// Add new skill dynamically
function addNewSkill() {
    const wrapper = document.getElementById('skillsWrapper');
    const newBlock = document.createElement('div');
    newBlock.className = 'skill-block';
    newBlock.innerHTML = `
        <div class="mb-3">
            <label class="form-label fw-bold">Skill Name</label>
            <input type="text" name="skills[${skillIndex}][name]" class="form-control" placeholder="Enter Skill" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Do you have a certification for this skill?</label>
            <select name="skills[${skillIndex}][has_certificate]" class="form-select cert-dropdown" onchange="checkSelection(this)">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
        <div class="upload-section">
            <label class="form-label fw-bold text-primary">Upload Certificate (Image or PDF)</label>
            <input type="file" name="skills[${skillIndex}][certificate]" class="form-control" accept="image/*,.pdf">
        </div>
    `;
    wrapper.appendChild(newBlock);
    skillIndex++;
}

// Show/hide certificate upload
function checkSelection(select) {
    const uploadDiv = select.parentElement.nextElementSibling;
    if(select.value === "1") {
        uploadDiv.style.display = "block";
    } else {
        uploadDiv.style.display = "none";
        const input = uploadDiv.querySelector('input[type="file"]');
        if(input) input.value = "";
    }
}

// Apply initial check on first skill block
document.querySelectorAll('.cert-dropdown').forEach(select => checkSelection(select));
</script>

</body>
</html>
