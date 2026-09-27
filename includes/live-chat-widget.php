<?php
// Live Chat Widget - Include this in footer or where you want the chat button
$pageUrl = current_url();
?>
<!-- Live Chat Widget -->
<div id="liveChatWidget">
    <!-- Chat Button -->
    <button id="liveChatBtn" class="live-chat-btn" aria-label="Open Live Chat">
        <i class="fas fa-comments"></i>
        <span class="live-chat-badge" id="liveChatBadge" style="display: none;">0</span>
    </button>

    <!-- Chat Window -->
    <div id="liveChatWindow" class="live-chat-window" style="display: none;">
        <div class="live-chat-header">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-primary text-white me-2">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <strong>Live Support</strong>
                    <small class="d-block text-white-50" id="liveChatStatus">Click to start chat</small>
                </div>
            </div>
            <button class="btn-close btn-close-white" id="liveChatClose" aria-label="Close chat"></button>
        </div>

        <div class="live-chat-body" id="liveChatBody">
            <!-- Pre-chat Form -->
            <div id="liveChatPreForm" class="p-3">
                <h5 class="mb-3">Start a Conversation</h5>
                <form id="liveChatForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="sender_type" value="visitor">
                    <div class="mb-3">
                        <label class="form-label">Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Your Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="your@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="3" required placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-1"></i>Start Chat
                    </button>
                </form>
            </div>

            <!-- Chat Messages -->
            <div id="liveChatMessages" style="display: none;">
                <div class="chat-messages" id="chatMessagesList" style="height: 300px; overflow-y: auto; padding: 10px;">
                    <div class="text-center text-muted py-3" id="chatEmptyState">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>Connecting...</p>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div id="liveChatInputArea" style="display: none;" class="p-3 border-top">
                <form id="liveChatSendForm" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="sender_type" value="visitor">
                    <input type="text" name="message" class="form-control" placeholder="Type a message..." required autocomplete="off">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Live Chat Widget Styles */
#liveChatWidget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: inherit;
}

.live-chat-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.live-chat-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(37, 99, 235, 0.5);
}

.live-chat-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
}

.live-chat-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--danger-color);
    color: white;
    font-size: 11px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid white;
}

.live-chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 360px;
    max-height: 500px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border: 1px solid var(--border-color);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.live-chat-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.live-chat-header .btn-close {
    opacity: 0.8;
    transition: opacity 0.2s;
}

.live-chat-header .btn-close:hover {
    opacity: 1;
}

.live-chat-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-message {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 18px;
    animation: fadeIn 0.3s ease;
}

.chat-message.visitor {
    align-self: flex-start;
    background: var(--light-color);
    border-bottom-left-radius: 4px;
}

.chat-message.admin {
    align-self: flex-end;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-bottom-right-radius: 4px;
}

.chat-message .sender {
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 4px;
    opacity: 0.8;
}

.chat-message .text {
    word-wrap: break-word;
    line-height: 1.5;
}

.chat-message .time {
    font-size: 10px;
    opacity: 0.6;
    margin-top: 4px;
    text-align: right;
}

.live-chat-input-area {
    padding: 16px;
    border-top: 1px solid var(--border-color);
    background: var(--light-color);
}

.live-chat-input-area .form-control {
    border-radius: 24px;
    padding: 10px 16px;
    border: 1px solid var(--border-color);
}

.live-chat-input-area .btn {
    border-radius: 24px;
    padding: 10px 20px;
}

#liveChatForm .form-control,
#liveChatSendForm .form-control {
    border-radius: 10px;
    padding: 12px 16px;
}

@media (max-width: 480px) {
    .live-chat-window {
        width: calc(100vw - 32px);
        right: -16px;
        bottom: 80px;
        max-height: 70vh;
    }
    
    #liveChatWidget {
        bottom: 16px;
        right: 16px;
    }
}
</style>

<script>
// Live Chat Widget Logic
(function() {
    let chatId = null;
    let lastMessageId = 0;
    let pollInterval = null;
    let isOpen = false;
    
    const btn = document.getElementById('liveChatBtn');
    const windowEl = document.getElementById('liveChatWindow');
    const closeBtn = document.getElementById('liveChatClose');
    const preForm = document.getElementById('liveChatPreForm');
    const messagesDiv = document.getElementById('liveChatMessages');
    const messagesList = document.getElementById('chatMessagesList');
    const inputArea = document.getElementById('liveChatInputArea');
    const chatForm = document.getElementById('liveChatForm');
    const sendForm = document.getElementById('liveChatSendForm');
    const statusEl = document.getElementById('liveChatStatus');
    const badge = document.getElementById('liveChatBadge');
    
    // Toggle chat window
    btn?.addEventListener('click', toggleChat);
    closeBtn?.addEventListener('click', closeChat);
    
    function toggleChat() {
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    }
    
    function openChat() {
        isOpen = true;
        windowEl.style.display = 'flex';
        btn.style.display = 'none';
        loadChat();
        startPolling();
    }
    
    function closeChat() {
        isOpen = false;
        windowEl.style.display = 'none';
        btn.style.display = 'flex';
        stopPolling();
    }
    
    // Load existing chat or show pre-form
    async function loadChat() {
        try {
            const response = await fetch('/Tamim/api/live-chat/messages.php');
            const data = await response.json();
            
            if (data.success && data.chat_id) {
                chatId = data.chat_id;
                if (data.messages && data.messages.length > 0) {
                    showChatInterface();
                    renderMessages(data.messages);
                    lastMessageId = data.messages[data.messages.length - 1]?.id || 0;
                    updateUnreadBadge(data.unread_count || 0);
                    statusEl.textContent = data.status === 'active' ? 'Online' : 'Waiting for agent...';
                } else {
                    showPreForm();
                    statusEl.textContent = 'Click to start chat';
                }
            } else {
                showPreForm();
                statusEl.textContent = 'Click to start chat';
            }
        } catch (err) {
            console.error('Load chat error:', err);
            showPreForm();
        }
    }
    
    function showPreForm() {
        preForm.style.display = 'block';
        messagesDiv.style.display = 'none';
        inputArea.style.display = 'none';
    }
    
    function showChatInterface() {
        preForm.style.display = 'none';
        messagesDiv.style.display = 'block';
        inputArea.style.display = 'block';
    }
    
    function renderMessages(messages) {
        if (!messages || messages.length === 0) {
            messagesList.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-comments fa-2x mb-2"></i><p>No messages yet. Start the conversation!</p></div>';
            return;
        }
        
        messagesList.innerHTML = messages.map(msg => `
            <div class="chat-message ${msg.sender_type}">
                <div class="sender">${msg.sender_type === 'admin' ? (msg.admin_name || 'Support') : 'You'}</div>
                <div class="text">${msg.message.replace(/\n/g, '<br>')}</div>
                <div class="time">${formatTime(msg.created_at)}</div>
            </div>
        `).join('');
        
        scrollToBottom();
    }
    
    function appendMessage(msg) {
        const emptyState = document.getElementById('chatEmptyState');
        if (emptyState) emptyState.remove();
        
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${msg.sender_type}`;
        msgDiv.innerHTML = `
            <div class="sender">${msg.sender_type === 'admin' ? (msg.admin_name || 'Support') : 'You'}</div>
            <div class="text">${msg.message.replace(/\n/g, '<br>')}</div>
            <div class="time">${formatTime(msg.created_at)}</div>
        `;
        messagesList.appendChild(msgDiv);
        scrollToBottom();
    }
    
    function scrollToBottom() {
        messagesList.scrollTop = messagesList.scrollHeight;
    }
    
    function formatTime(isoString) {
        const date = new Date(isoString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    function updateUnreadBadge(count) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    // Pre-chat form submission
    chatForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Connecting...';
        
        try {
            // First initialize chat
            const initResponse = await fetch('/Tamim/api/live-chat/init.php', {
                method: 'POST',
                body: new FormData(form)
            });
            const initData = await initResponse.json();
            
            if (!initData.success) throw new Error(initData.message);
            
            chatId = initData.chat_id;
            
            // Then send first message
            const message = form.querySelector('textarea[name="message"]').value;
            const formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('message', message);
            formData.append('sender_type', 'visitor');
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
            
            const sendResponse = await fetch('/Tamim/api/live-chat/send.php', {
                method: 'POST',
                body: formData
            });
            const sendData = await sendResponse.json();
            
            if (sendData.success) {
                showChatInterface();
                statusEl.textContent = 'Waiting for agent...';
                startPolling();
            } else {
                throw new Error(sendData.message);
            }
        } catch (err) {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Send message form
    sendForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const input = this.querySelector('input[name="message"]');
        const message = input.value.trim();
        if (!message || !chatId) return;
        
        const formData = new FormData(this);
        formData.append('chat_id', chatId);
        formData.append('sender_type', 'visitor');
        
        try {
            const response = await fetch('/Tamim/api/live-chat/send.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                input.value = '';
                // Message will appear via polling
            }
        } catch (err) {
            console.error('Send error:', err);
        }
    });
    
    // Polling for new messages
    function startPolling() {
        if (pollInterval) return;
        pollInterval = setInterval(pollMessages, 3000);
    }
    
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
    
    async function pollMessages() {
        if (!chatId) return;
        
        try {
            const response = await fetch(`/Tamim/api/live-chat/messages.php?since=${lastMessageId}`);
            const data = await response.json();
            
            if (data.success && data.messages && data.messages.length > 0) {
                const newMessages = data.messages.filter(m => m.id > lastMessageId);
                newMessages.forEach(msg => appendMessage(msg));
                lastMessageId = data.messages[data.messages.length - 1]?.id || lastMessageId;
                
                // Mark as read if window is focused
                if (document.hasFocus() && newMessages.some(m => m.sender_type === 'admin')) {
                    markAsRead(newMessages.filter(m => m.sender_type === 'admin').map(m => m.id));
                }
                
                updateUnreadBadge(data.unread_count || 0);
                statusEl.textContent = data.status === 'active' ? 'Agent online' : 'Waiting for agent...';
            }
        } catch (err) {
            console.error('Poll error:', err);
        }
    }
    
    async function markAsRead(messageIds) {
        if (messageIds.length === 0) return;
        
        try {
            const formData = new FormData();
            formData.append('message_ids', JSON.stringify(messageIds));
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
            
            await fetch('/Tamim/api/live-chat/read.php', {
                method: 'POST',
                body: formData
            });
        } catch (err) {
            console.error('Mark read error:', err);
        }
    }
    
    // Handle page visibility
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else if (isOpen && chatId) {
            startPolling();
        }
    });
    
    // Handle page unload
    window.addEventListener('beforeunload', () => {
        stopPolling();
    });
})();
</script>