<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f2f4f7; padding:30px; }
.profile-card { max-width:600px; margin:auto; border:2px solid #0d6efd; border-radius:15px; background:#fff; padding:30px; }
.avatar { width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #0d6efd; }
video { display:none; }
</style>
</head>
<body>

@if(!$user)
<h3 class="text-center text-danger">User not found</h3>
@else

<div class="container">
    <div class="profile-card text-center">
        <img id="profileImg" class="avatar mb-3" 
             src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('default.png') }}">

        <div class="mb-3 text-start">
            <label>User ID</label>
            <input class="form-control" value="{{ $user->user_id }}" readonly>
        </div>

        <div class="mb-3 text-start">
            <label>Username</label>
            <input class="form-control" value="{{ $user->username }}">
        </div>

        <div class="mb-3 text-start">
            <label>Email</label>
            <input class="form-control" value="{{ $user->email }}">
        </div>

        <div class="mb-3 text-start">
            <label>Mobile</label>
            <input class="form-control" value="{{ $user->mobile }}">
        </div>

        <div class="mt-3">
            <button class="btn btn-primary me-2" onclick="openCamera()">Open Camera</button>
            <button class="btn btn-success" onclick="capturePhoto()">Capture & Save</button>
        </div>

        <video id="video" autoplay></video>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>
</div>
@endif
<button onclick="history.back()" style="padding:10px 20px; border-radius:5px; cursor:pointer;">
  Go Back
</button>

<script>
let stream;
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

function openCamera(){
  navigator.mediaDevices.getUserMedia({video:true}).then(s=>{
    stream=s;
    video.srcObject=s;
    video.style.display='block';
  }).catch(e=>alert('Camera not accessible'));
}

function capturePhoto(){
  canvas.width=video.videoWidth;
  canvas.height=video.videoHeight;
  canvas.getContext('2d').drawImage(video,0,0);
  const imgData=canvas.toDataURL('image/png');

  document.getElementById('profileImg').src=imgData;

  stream.getTracks().forEach(t=>t.stop());
  video.style.display='none';

  fetch("{{ route('profile.photo') }}",{
    method:"POST",
    headers:{
      "Content-Type":"application/json",
      "X-CSRF-TOKEN":"{{ csrf_token() }}"
    },
    body: JSON.stringify({photo:imgData})
  }).then(res=>res.json()).then(data=>{
    if(data.success) alert('Photo saved!');
  }).catch(err=>{
    console.error(err);
    alert('Error saving photo');
  });
}
</script>

</body>
</html>
