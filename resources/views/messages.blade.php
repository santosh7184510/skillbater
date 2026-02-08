<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Dashboard - 24MCA2011</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>
    <style>
        :root {
            --wa-bg: #efeae2;
            --wa-header: #f0f2f5;
            --wa-sent: #d9fdd3;
            --wa-received: #ffffff;
            --wa-secondary: #667781;
            --wa-green: #008069;
            --wa-blue: #34b7f1;
            --danger: #ea4335;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #d1d7db; height: 100vh; display: flex; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 300px; background: white; border-right: 1px solid #ddd; display: flex; flex-direction: column; }
        .sidebar-header { padding: 15px; background: var(--wa-header); border-bottom: 1px solid #ddd; }
        .sidebar-search { padding: 10px; border-bottom: 1px solid #eee; }
        .sidebar-search input { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ddd; outline: none; font-size: 14px; }
        .chat-list-item { padding: 15px; background: #f0f2f5; cursor: pointer; border-bottom: 1px solid #eee; }

        /* Workspace */
        .workspace { flex: 1; display: flex; flex-direction: column; background: var(--wa-bg); position: relative; }
        #closed-screen { display: none; position: absolute; inset: 0; background: #f0f2f5; z-index: 200; align-items: center; justify-content: center; flex-direction: column; text-align: center; }

        /* Search Bar Overlay */
        #search-overlay { display: none; position: absolute; top: 0; left: 0; width: 100%; height: 65px; background: var(--wa-header); align-items: center; padding: 0 20px; z-index: 110; gap: 15px; border-bottom: 1px solid #d1d7db; }
        #search-overlay input { flex: 1; padding: 8px 15px; border-radius: 8px; border: 1px solid #d1d7db; outline: none; }
        #not-found-alert { display: none; position: absolute; top: 75px; left: 50%; transform: translateX(-50%); background: #323232; color: white; padding: 10px 20px; border-radius: 8px; z-index: 1000; font-size: 14px; }

        /* Highlighting & Animation */
        .highlight { background: #fff59d !important; border: 2px solid orange !important; animation: blink 0.5s step-end 4; transition: 0.3s; }
        @keyframes blink { 50% { opacity: 0.4; } }

        /* Header */
        .header { background: var(--wa-header); padding: 10px 16px; display: flex; align-items: center; border-bottom: 1px solid #d1d7db; height: 65px; z-index: 10; }
        #chat-box { flex: 1; overflow-y: auto; padding: 20px 5%; display: flex; flex-direction: column; gap: 10px; scroll-behavior: smooth; }
        .date-label { align-self: center; background: #fff; padding: 4px 12px; border-radius: 6px; font-size: 12px; color: var(--wa-secondary); margin: 10px 0; }

        /* Message Bubbles */
        .msg { max-width: 65%; padding: 8px; border-radius: 8px; font-size: 14.5px; position: relative; box-shadow: 0 1px 1px rgba(0,0,0,0.1); cursor: pointer; }
        .sent { align-self: flex-end; background: var(--wa-sent); }
        .media-content { max-width: 250px; border-radius: 6px; margin-top: 4px; display: block; }
        .selected { outline: 2px solid var(--wa-blue); }

        /* Media Overlays */
        #media-overlay { display: none; position: fixed; inset: 0; background: #000; z-index: 5000; flex-direction: column; align-items: center; justify-content: center; }
        video#webcam { width: 90%; max-width: 600px; border-radius: 12px; background: #1a1a1a; }
        .mirror { transform: scaleX(-1); }
        
        #doc-preview-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 6000; flex-direction: column; padding: 20px; }
        #doc-iframe { width: 100%; height: 90%; background: white; border: none; border-radius: 8px; }

        /* Call Controls */
        .call-controls { position: absolute; bottom: 40px; display: flex; gap: 15px; background: rgba(255,255,255,0.1); padding: 15px 25px; border-radius: 50px; backdrop-filter: blur(10px); }
        .ctrl-btn { width: 50px; height: 50px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: white; }

        /* Footer */
        .footer { background: var(--wa-header); padding: 10px 16px; display: flex; align-items: center; gap: 15px; border-top: 1px solid #d1d7db; position: relative; }
        .footer input { flex: 1; padding: 10px 15px; border: none; border-radius: 8px; outline: none; }
        #emoji-picker-container { display: none; position: absolute; bottom: 70px; left: 15px; z-index: 1000; }
        
        .dropdown { display: none; position: absolute; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 500; width: 180px; }
        .dropdown div { padding: 12px 20px; cursor: pointer; font-size: 14px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><h3>Messages</h3></div>
    <div class="sidebar-search"><input type="text" placeholder="Search User ID..."></div>
    <div class="chat-list-item"><strong>KKK-432</strong></div>
</div>

<div class="workspace">
    <div id="closed-screen">
        <i data-lucide="message-square-off" style="width: 64px; height: 64px; color: #ccc;"></i>
        <h2 style="color: #667781; margin-top:10px;">Chat Closed</h2>
        <button onclick="location.reload()" style="margin-top:20px; padding:10px 20px; cursor:pointer; background:var(--wa-green); color:white; border:none; border-radius:5px;">New Chat</button>
    </div>

    <div id="not-found-alert">Result not found</div>

    <div id="search-overlay">
        <i data-lucide="arrow-left" onclick="toggleSearch()" style="cursor:pointer"></i>
        <input type="text" id="searchInput" placeholder="Search message or date..." onkeypress="if(event.key==='Enter') doSearch()">
        <i data-lucide="x" onclick="clearSearch()" style="cursor:pointer"></i>
    </div>

    <div class="header">
        <div style="flex:1; font-weight:600;">KKK-432</div>
        <div style="display:flex; gap:20px; color:var(--wa-secondary);">
            <i data-lucide="search" onclick="toggleSearch()" style="cursor:pointer"></i>
            <i data-lucide="video" onclick="startMedia('call')" style="cursor:pointer"></i>
            <i data-lucide="more-vertical" onclick="toggleMenu('mainDrop')" style="cursor:pointer"></i>
        </div>
        <div class="dropdown" id="mainDrop" style="right:15px; top:60px;">
            <div onclick="starSelected()">Star Selected</div>
            <div onclick="deleteSelected()" style="color:red;">Delete Selected</div>
            <div id="blockBtn" onclick="toggleBlock()">Block User</div>
            <div onclick="closeChat()">Close Chat</div>
        </div>
    </div>

    <div id="chat-box">
        <div class="date-label">FEBRUARY 8, 2026</div>
    </div>

    <div id="emoji-picker-container"></div>

    <div class="footer" id="chatFooter">
        <i data-lucide="smile" onclick="toggleEmoji()" style="cursor:pointer"></i>
        <i data-lucide="plus" onclick="toggleMenu('attachMenu')" style="cursor:pointer"></i>
        <div class="dropdown" id="attachMenu" style="bottom:70px; left:60px;">
            <div onclick="document.getElementById('upPhoto').click()">Photos</div>
            <div onclick="document.getElementById('upVideo').click()">Videos</div>
            <div onclick="document.getElementById('upDoc').click()">Documents</div>
        </div>
        <input type="text" id="msgInput" placeholder="Type a message" onkeypress="if(event.key==='Enter') send()">
        <i data-lucide="send-horizontal" onclick="send()" style="cursor:pointer; color:var(--wa-green)"></i>
    </div>
</div>

<div id="media-overlay">
    <video id="webcam" autoplay playsinline class="mirror"></video>
    <div class="call-controls">
        <button id="btn-mic" class="ctrl-btn" style="background:#4a4a4a;" onclick="toggleAudio()"><i data-lucide="mic"></i></button>
        <button id="btn-cam" class="ctrl-btn" style="background:#4a4a4a;" onclick="toggleVideo()"><i data-lucide="video"></i></button>
        <button id="btn-screen" class="ctrl-btn" style="background:#4a4a4a;" onclick="toggleScreenShare()"><i data-lucide="monitor"></i></button>
        <button class="ctrl-btn" style="background:var(--danger);" onclick="stopMedia()"><i data-lucide="phone-off"></i></button>
    </div>
</div>

<div id="doc-preview-overlay">
    <div style="display:flex; justify-content: flex-end; padding-bottom:10px;"><i data-lucide="x" onclick="closeDoc()" style="color:white; cursor:pointer;"></i></div>
    <iframe id="doc-iframe"></iframe>
</div>

<input type="file" id="upPhoto" hidden accept="image/*" onchange="fileSent('img')">
<input type="file" id="upVideo" hidden accept="video/*" onchange="fileSent('video')">
<input type="file" id="upDoc" hidden accept=".pdf,.txt" onchange="fileSent('doc')">

<script>
    lucide.createIcons();
    let stream = null, isBlocked = false;
    let micEnabled = true, camEnabled = true, isSharingScreen = false;

    // Search Logic
    function toggleSearch() {
        const s = document.getElementById('search-overlay');
        s.style.display = (s.style.display === 'flex') ? 'none' : 'flex';
        if(s.style.display === 'flex') document.getElementById('searchInput').focus();
    }

    function doSearch() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.msg, .date-label');
        let found = false;
        clearSearch();
        items.forEach(item => {
            if (q && item.textContent.toLowerCase().includes(q)) {
                item.classList.add('highlight');
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                found = true;
            }
        });
        if (!found && q) {
            document.getElementById('not-found-alert').style.display = 'block';
            setTimeout(() => document.getElementById('not-found-alert').style.display = 'none', 2000);
        }
    }

    function clearSearch() { document.querySelectorAll('.highlight').forEach(el => el.classList.remove('highlight')); }

    // Messaging & Blue Ticks
    function send(textVal = null, mediaUrl = null, type = 'text') {
        if(isBlocked) return;
        const box = document.getElementById('chat-box');
        const m = document.createElement('div');
        m.className = "msg sent";
        m.onclick = function() { this.classList.toggle('selected'); };
        
        if(type === 'text') m.innerHTML = `<div>${textVal || document.getElementById('msgInput').value}</div>`;
        else if(type === 'img') m.innerHTML = `<img src="${mediaUrl}" class="media-content">`;
        else if(type === 'video') m.innerHTML = `<video src="${mediaUrl}" controls class="media-content"></video>`;
        else if(type === 'doc') m.innerHTML = `<div onclick="viewDoc('${mediaUrl}')" style="color:var(--wa-green); text-decoration:underline; cursor:pointer;">📄 ${textVal}</div>`;

        m.innerHTML += `<div style="display:flex; align-items:center; justify-content:flex-end; gap:4px; margin-top:4px;">
            <span style="font-size:10px; color:gray;">${new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</span>
            <i data-lucide="check-check" style="width:14px; color:var(--wa-blue);"></i></div>`;
        
        box.appendChild(m);
        document.getElementById('msgInput').value = "";
        lucide.createIcons();
        box.scrollTop = box.scrollHeight;
    }

    function fileSent(type) {
        const inputId = type === 'img' ? 'upPhoto' : type === 'video' ? 'upVideo' : 'upDoc';
        const file = document.getElementById(inputId).files[0];
        if(file) send(file.name, URL.createObjectURL(file), type);
    }

    // Media Functions
    async function startMedia() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({video:true, audio:true});
            document.getElementById('webcam').srcObject = stream;
            document.getElementById('media-overlay').style.display = 'flex';
        } catch(e) { alert("Access Denied"); }
    }
    function stopMedia() { if(stream) stream.getTracks().forEach(t => t.stop()); document.getElementById('media-overlay').style.display = 'none'; }
    function toggleAudio() { micEnabled = !micEnabled; stream.getAudioTracks()[0].enabled = micEnabled; updateUI(); }
    function toggleVideo() { camEnabled = !camEnabled; stream.getVideoTracks()[0].enabled = camEnabled; updateUI(); }
    function updateUI() { 
        document.getElementById('btn-mic').innerHTML = `<i data-lucide="${micEnabled ? 'mic' : 'mic-off'}"></i>`;
        document.getElementById('btn-cam').innerHTML = `<i data-lucide="${camEnabled ? 'video' : 'video-off'}"></i>`;
        lucide.createIcons();
    }
    
    async function toggleScreenShare() {
        if (!isSharingScreen) {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            document.getElementById('webcam').srcObject = screenStream;
            document.getElementById('webcam').classList.remove('mirror');
            isSharingScreen = true;
        } else { stopMedia(); startMedia(); }
    }

    // UI Controls
    function viewDoc(url) { document.getElementById('doc-iframe').src = url; document.getElementById('doc-preview-overlay').style.display = 'flex'; }
    function closeDoc() { document.getElementById('doc-preview-overlay').style.display = 'none'; }
    function toggleBlock() { 
        isBlocked = !isBlocked; 
        document.getElementById('blockBtn').innerText = isBlocked ? "Unblock User" : "Block User";
        document.getElementById('chatFooter').style.opacity = isBlocked ? "0.3" : "1";
    }
    function closeChat() { document.getElementById('closed-screen').style.display = 'flex'; }
    function toggleEmoji() { const e = document.getElementById('emoji-picker-container'); e.style.display = e.style.display === 'block' ? 'none' : 'block'; }
    function toggleMenu(id) { document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none'); document.getElementById(id).style.display = 'block'; }
    function deleteSelected() { document.querySelectorAll('.msg.selected').forEach(e => e.remove()); }
    function starSelected() { document.querySelectorAll('.msg.selected').forEach(e => e.style.background = "#fff9c4"); }

    const picker = new EmojiMart.Picker({ onEmojiSelect: (e) => { document.getElementById('msgInput').value += e.native; } });
    document.getElementById('emoji-picker-container').appendChild(picker);
    window.onclick = (e) => { if(!e.target.closest('.lucide')) document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none'); };
</script>
</body>
</html>