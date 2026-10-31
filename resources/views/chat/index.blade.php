@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Ruang Diskusi Guru</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
    <style>
        /* Chat page: scroll biasa seperti halaman dashboard */
        .chat-wrapper {
            padding: 15px;
            padding-bottom: 85px;
            margin-top: 56px;
            min-height: calc(100vh - 56px);
            background: #f4f5f7;
            overscroll-behavior-y: contain;
        }

        .message-item {
            margin-bottom: 15px;
            display: flex;
            flex-direction: row;
            align-items: flex-end;
        }

        .chat-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid #ddd;
            margin-bottom: 2px;
        }

        .message-item.me .chat-avatar {
            margin-left: 8px;
        }

        .message-item.other .chat-avatar {
            margin-right: 8px;
        }

        .message-content {
            padding: 10px 15px;
            border-radius: 12px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08); /* slight shadow for wa feel */
        }

        .message-item.me {
            justify-content: flex-end;
        }

        .message-item.me .message-content {
            background-color: #dcf8c6; /* wa green */
            border-bottom-right-radius: 0;
        }

        .message-item.other {
            justify-content: flex-start;
        }

        .message-item.other .message-content {
            background-color: #ffffff;
            border-bottom-left-radius: 0;
        }

        .msg-sender {
            font-size: 12px;
            font-weight: bold;
            color: #ef4444; /* red or random color for name WA style */
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .admin-badge {
            background: #1e3a8a;
            color: white;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 900;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .msg-time {
            font-size: 10px;
            color: #888;
            margin-top: 4px;
            text-align: right;
            display: block;
        }

        .msg-action-btn {
            position: absolute;
            top: -6px;
            background: #ff4c4c;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #f4f5f7;
            opacity: 0;
            transition: opacity 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .msg-delete { left: -10px; }
        .msg-reply { left: -10px; background: #2196F3; } 
        .message-item.me .msg-reply { left: auto; right: -10px; }

        .message-content:hover .msg-action-btn {
            opacity: 1;
        }

        /* Reply Quote Box inside messages */
        .quote-box {
            background: rgba(0,0,0,0.05);
            border-left: 4px solid #2196F3;
            border-radius: 4px;
            padding: 5px 10px;
            margin-bottom: 8px;
            font-size: 11px;
            color: #555;
            cursor: pointer;
            word-break: break-all;
        }
        .quote-sender { font-weight: bold; color: #2196F3; margin-bottom: 2px; }

        /* Reply context above input */
        #reply-context {
            display: none;
            background: #f0f7ff;
            border-left: 4px solid #2196F3;
            padding: 8px 15px;
            position: fixed;
            bottom: 60px; /* above the input */
            left: 0;
            right: 0;
            z-index: 999;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
            border-top: 1px solid #ddd;
        }
        #reply-context .close-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            cursor: pointer;
            font-size: 20px;
        }

        /* Sembunyikan bottom menu khusus di halaman chat */
        .appBottomMenu {
            display: none !important;
        }

        .chat-input-area {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 10px 15px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }

        .chat-input-area input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .chat-input-area input:focus {
            border-color: #2196F3;
        }

        .chat-input-area button {
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(30, 58, 138, 0.3);
            transition: transform 0.1s;
        }
        
        .chat-input-area button:active {
            transform: scale(0.95);
        }
        
        /* Image message styling */
        .chat-img-msg {
            width: 100%;
            max-width: 250px;
            border-radius: 8px;
            margin-top: 5px;
            cursor: pointer;
            display: block;
        }

        #image-preview-container {
            display: none;
            padding: 10px 15px;
            background: #fff;
            border-top: 1px solid #eee;
            position: fixed;
            bottom: 60px;
            left: 0;
            right: 0;
            z-index: 998;
            align-items: center;
            gap: 15px;
        }
        
        #image-preview-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Fullscreen Modal */
        #fullscreen-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 10002;
            justify-content: center;
            align-items: center;
        }
        #fullscreen-modal img {
            max-width: 95%;
            max-height: 90%;
            object-fit: contain;
        }
        #fullscreen-modal .close-fs {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 35px;
            cursor: pointer;
        }

        /* Sweet alert z-index fix */
        .swal2-container {
            z-index: 10001 !important;
        }
    </style>
@endsection

@section('content')
<div class="chat-wrapper" id="chat-container">
    <div class="text-center mt-3" id="loading-msg">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted" style="font-size: 12px;">Memuat obrolan...</p>
    </div>
    <div id="messages-list"></div>
</div>

<div id="fullscreen-modal" onclick="this.style.display='none'">
    <span class="close-fs">&times;</span>
    <img id="fs-img" src="" alt="Full Screen">
</div>

<div id="reply-context">
    <div style="font-size: 11px; color: #2196F3; font-weight: bold;" id="reply-sender">Membalas</div>
    <div style="font-size: 12px; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="reply-text">Halo...</div>
    <ion-icon name="close" class="close-btn" onclick="cancelReply()"></ion-icon>
</div>

<div id="image-preview-container">
    <img id="image-preview-img" src="">
    <div style="flex:1; font-size:12px; color:#555;" id="image-preview-name">foto.jpg</div>
    <ion-icon name="close-circle" style="font-size:24px; color:#ff4c4c; cursor:pointer;" onclick="clearImage()"></ion-icon>
</div>

<form id="chat-form" class="chat-input-area" onsubmit="sendMessage(event)">
    <input type="file" id="photo-input" accept="image/*" style="display:none" onchange="previewImage(this)">
    <button type="button" style="background:none; box-shadow:none; color:#1e3a8a; width:30px;" onclick="document.getElementById('photo-input').click()">
        <ion-icon name="camera-outline" style="font-size:28px;"></ion-icon>
    </button>
    <input type="hidden" id="reply-to-id" value="">
    <input type="text" id="message-input" placeholder="Ketik pesan disini..." autocomplete="off">
    <button type="submit" id="btn-send">
        <ion-icon name="send"></ion-icon>
    </button>
</form>

@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const myNik = "{{ Auth::guard('karyawan')->user()->nik }}";
    const chatContainer = document.getElementById('chat-container');
    const messagesList = document.getElementById('messages-list');
    const loadingMsg = document.getElementById('loading-msg');
    let lastMessageCount = 0;
    
    // Auto polling trigger
    let pollInterval;

    /**
     * Memanggil Fungsi untuk mengambil pesan dari server
     */
    function fetchMessages() {
        $.ajax({
            url: '/chat/fetch',
            type: 'GET',
            success: function(res) {
                if (loadingMsg) loadingMsg.style.display = 'none';

                let html = '';
                
                // Jika belum ada pesan sama sekali
                if (res.length === 0) {
                    html = `<div class="text-center mt-4 p-3" style="color:#aaa; font-size:12px; background:rgba(0,0,0,0.03); border-radius:10px;">
                                Belum ada obrolan. Jadilah yang pertama menyapa guru lain!
                            </div>`;
                }
                
                res.forEach(msg => {
                    const isMe = msg.nik === myNik;
                    const cssClass = isMe ? 'me' : 'other';
                    let deleteBtn = isMe ? `<div class="msg-action-btn msg-delete" onclick="deleteMessage(${msg.id})"><ion-icon name="trash"></ion-icon></div>` : '';
                    
                    // Mengambil nama pengirim dari relasi karyawan
                    let senderName = (msg.karyawan && msg.karyawan.nama_lengkap) ? msg.karyawan.nama_lengkap : 'Guru';
                    let safeMsg = escapeHTML(msg.message || '');

                    // Lencana Admin (Khusus NIK tertentu)
                    let adminBadge = (msg.nik === 'CBG250003') ? `<span class="admin-badge"><ion-icon name="checkmark-circle"></ion-icon> Admin</span>` : '';

                    // Tombol Balas (Reply)
                    let replyBtn = `<div class="msg-action-btn msg-reply" onclick="setReply(${msg.id}, '${senderName.replace(/'/g, "\\'")}', '${safeMsg.replace(/'/g, "\\'")}')"><ion-icon name="return-down-forward"></ion-icon></div>`;
                    
                    // Box kutipan jika membalas pesan orang lain
                    let quoteHtml = '';
                    if (msg.reply_to) {
                        let repliedName = msg.reply_to.karyawan ? msg.reply_to.karyawan.nama_lengkap : 'Seseorang';
                        let repliedText = escapeHTML(msg.reply_to.message || '');
                        quoteHtml = `
                        <div class="quote-box" onclick="Swal.fire({title:'Pesan Asli',text:'${repliedText}'})">
                            <div class="quote-sender">${repliedName}</div>
                            <div class="quote-text">${repliedText.substring(0,50)}${repliedText.length > 50 ? '...' : ''}</div>
                        </div>`;
                    }

                    const dt = new Date(msg.created_at);
                    const timeStr = dt.getHours().toString().padStart(2, '0') + ':' + dt.getMinutes().toString().padStart(2, '0');

                    // Foto Profil Pengirim
                    let userFoto = (msg.karyawan && msg.karyawan.foto) ? msg.karyawan.foto : null;
                    let avatarUrl = userFoto ? '/storage/uploads/karyawan/' + userFoto : '/assets/img/sample/avatar/avatar1.jpg';
                    let avatarHtml = `<img src="${avatarUrl}" class="chat-avatar" onerror="this.src='/assets/img/sample/avatar/avatar1.jpg'">`;

                    // Tampilan Foto jika pesan mengandung lampiran gambar
                    let photoHtml = '';
                    if (msg.file) {
                        let msgPhotoPath = '/storage/uploads/chat/' + msg.file;
                        photoHtml = `<img src="${msgPhotoPath}" class="chat-img-msg" onclick="showFullScreen('${msgPhotoPath}')">`;
                    }

                    html += `
                        <div class="message-item ${cssClass}">
                            ${!isMe ? avatarHtml : ''}
                            <div class="message-content">
                                ${deleteBtn}
                                ${replyBtn}
                                ${!isMe ? `<div class="msg-sender">${senderName} ${adminBadge}</div>` : `<div class="msg-sender" style="color:#16a34a">Anda ${adminBadge}</div>`}
                                ${quoteHtml}
                                ${photoHtml}
                                ${safeMsg ? `<div class="msg-text">${safeMsg}</div>` : ''}
                                <span class="msg-time">${timeStr}</span>
                            </div>
                            ${isMe ? avatarHtml : ''}
                        </div>
                    `;
                });

                // Is there new data?
                const isNewMessage = res.length > lastMessageCount;
                
                // Keep scroll position unless we are at the bottom or there's a new message
                const isScrolledToBottom = (window.innerHeight + window.scrollY) >= (document.body.scrollHeight - 50);

                messagesList.innerHTML = html;

                if (isNewMessage) {
                    if (isScrolledToBottom || lastMessageCount === 0) {
                        scrollToBottom();
                    }
                    lastMessageCount = res.length;
                }
            }
        });
    }

    function setReply(id, senderName, text) {
        document.getElementById('reply-to-id').value = id;
        document.getElementById('reply-sender').innerText = "Membalas " + senderName;
        document.getElementById('reply-text').innerText = text;
        document.getElementById('reply-context').style.display = 'block';
        document.getElementById('message-input').focus();
    }

    function cancelReply() {
        document.getElementById('reply-to-id').value = '';
        document.getElementById('reply-context').style.display = 'none';
        document.getElementById('message-input').focus();
    }

    function scrollToBottom() {
        setTimeout(() => {
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }, 100);
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview-img').src = e.target.result;
                document.getElementById('image-preview-name').innerText = input.files[0].name;
                document.getElementById('image-preview-container').style.display = 'flex';
                document.getElementById('chat-container').style.paddingBottom = '140px'; // Space for preview
                scrollToBottom();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImage() {
        document.getElementById('photo-input').value = '';
        document.getElementById('image-preview-container').style.display = 'none';
        document.getElementById('chat-container').style.paddingBottom = '80px';
    }

    function showFullScreen(url) {
        document.getElementById('fs-img').src = url;
        document.getElementById('fullscreen-modal').style.display = 'flex';
    }

    function sendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const photoInput = document.getElementById('photo-input');
        const text = input.value.trim();
        const hasPhoto = photoInput.files && photoInput.files.length > 0;

        if(!text && !hasPhoto) return;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', text);
        
        let replyToId = document.getElementById('reply-to-id').value;
        if(replyToId) formData.append('reply_to_id', replyToId);
        
        if(hasPhoto) {
            formData.append('photo', photoInput.files[0]);
        }

        // Show loading state
        const btnSend = document.getElementById('btn-send');
        const oldBtnHtml = btnSend.innerHTML;
        btnSend.disabled = true;
        btnSend.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';

        input.value = ''; // clear immediately
        input.focus();
        
        $.ajax({
            url: '/chat/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                btnSend.disabled = false;
                btnSend.innerHTML = oldBtnHtml;
                cancelReply();
                clearImage();
                fetchMessages();
                setTimeout(scrollToBottom, 200);
            },
            error: function(xhr) {
                btnSend.disabled = false;
                btnSend.innerHTML = oldBtnHtml;
                let errorMsg = 'Gagal mengirim pesan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Gagal!', errorMsg, 'error');
            }
        });
    }

    function deleteMessage(id) {
        Swal.fire({
            title: 'Hapus Pesan?',
            text: "Pesan ini akan dihapus dari Ruang Diskusi",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/chat/destroy/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        lastMessageCount = 0; // force re-render
                        fetchMessages();
                    }
                });
            }
        });
    }

    function escapeHTML(str) {
        if(!str) return '';
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    /**
     * Menandai bahwa user sudah melihat chat agar notifikasi di dashboard hilang
     */
    function markAsRead() {
        $.ajax({
            url: '/chat/mark-as-read',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                console.log('Chat ditandai sebagai sudah dibaca');
            }
        });
    }

    // Load awal: Ambil pesan dan tandai sudah dibaca
    fetchMessages();
    markAsRead();

    // Jalankan polling setiap 3 detik untuk sinkronisasi pesan real-time
    pollInterval = setInterval(fetchMessages, 3000);

</script>
@endpush
