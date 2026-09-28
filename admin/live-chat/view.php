<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect('/Tamim/admin/live-chat/');
}

$chat = db_one('SELECT lc.*, a.full_name as admin_name FROM live_chats lc LEFT JOIN admins a ON lc.assigned_admin_id = a.id WHERE lc.id = ?', [$id]);
if (!$chat) {
    redirect('/Tamim/admin/live-chat/', 'Chat not found', 'danger');
}

// Mark visitor messages as read
db_execute('UPDATE live_chat_messages SET is_read = TRUE WHERE chat_id = ? AND sender_type = "visitor"', [$chat['id']]);

// Assign to current admin if unassigned
if ($chat['status'] === 'waiting' && !$chat['assigned_admin_id']) {
    db_execute('UPDATE live_chats SET assigned_admin_id = ?, status = "active", updated_at = CURRENT_TIMESTAMP WHERE id = ?', [$_SESSION['admin_id'], $chat['id']]);
    $chat['assigned_admin_id'] = $_SESSION['admin_id'];
    $chat['status'] = 'active';
    $chat['admin_name'] = $_SESSION['admin_name'] ?? 'Admin';
}

$messages = db_all(
    'SELECT m.*, a.full_name as admin_name FROM live_chat_messages m LEFT JOIN admins a ON m.sender_id = a.id WHERE m.chat_id = ? ORDER BY m.created_at ASC',
    [$chat['id']]
);

$pageTitle = 'Live Chat: ' . e($chat['visitor_name']);
$currentPage = 'live-chat';

require __DIR__ . '/../../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Chat with <?= e($chat['visitor_name']) ?></h1>
        <small class="text-muted">Email: <?= e($chat['visitor_email'] ?: 'Not provided') ?> | IP: <?= e($chat['visitor_ip']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <?php if ($chat['status'] !== 'closed'): ?>
            <a href="/Tamim/admin/live-chat/" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <?php else: ?>
            <a href="/Tamim/admin/live-chat/" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2">
                        <?= strtoupper(substr($chat['visitor_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <strong><?= e($chat['visitor_name']) ?></strong>
                        <span class="badge bg-<?= $chat['status'] === 'waiting' ? 'warning' : ($chat['status'] === 'active' ? 'success' : 'secondary') ?> ms-2"><?= ucfirst($chat['status']) ?></span>
                    </div>
                </div>
                <?php if ($chat['status'] !== 'closed'): ?>
                    <form action="/Tamim/admin/live-chat/close.php" method="POST" onsubmit="return confirm('Close this chat?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $chat['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Close Chat</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="card-body chat-messages" style="height: 500px; overflow-y: auto; padding: 20px;">
                <?php if (empty($messages)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                <?php else: ?>
                    <div class="message-list">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-item mb-3 <?= $msg['sender_type'] === 'admin' ? 'text-end' : '' ?>">
                                <div class="d-inline-block max-width-75 p-3 rounded-3 <?= $msg['sender_type'] === 'admin' ? 'bg-primary text-white' : 'bg-light' ?>">
                                    <small class="d-block mb-1 opacity-75">
                                        <?= $msg['sender_type'] === 'admin' ? 'You' : e($chat['visitor_name']) ?>
                                        <span class="ms-2"><?= format_date($msg['created_at'], 'H:i') ?></span>
                                    </small>
                                    <p class="mb-0"><?= nl2br(e($msg['message'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($chat['status'] !== 'closed'): ?>
                <div class="card-footer">
                    <form id="adminChatForm" class="d-flex gap-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
                        <input type="hidden" name="sender_type" value="admin">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Send</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card-footer text-center text-muted">
                    Chat closed on <?= format_date($chat['closed_at'], 'M d, Y H:i') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Visitor Info</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th style="width: 120px;">Name</th><td><?= e($chat['visitor_name']) ?></td></tr>
                    <tr><th>Email</th><td><?= e($chat['visitor_email'] ?: 'Not provided') ?></td></tr>
                    <tr><th>IP Address</th><td><?= e($chat['visitor_ip']) ?></td></tr>
                    <tr><th>Status</th><td><span class="badge bg-<?= $chat['status'] === 'waiting' ? 'warning' : ($chat['status'] === 'active' ? 'success' : 'secondary') ?>"><?= ucfirst($chat['status']) ?></span></td></tr>
                    <tr><th>Assigned To</th><td><?= $chat['admin_name'] ? e($chat['admin_name']) : 'Unassigned' ?></td></tr>
                    <tr><th>Started</th><td><?= format_date($chat['created_at'], 'M d, Y H:i') ?></td></tr>
                    <tr><th>Last Active</th><td><?= format_date($chat['updated_at'], 'M d, Y H:i') ?></td></tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($chat['status'] === 'waiting'): ?>
                        <form action="/Tamim/admin/live-chat/assign.php" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $chat['id'] ?>">
                            <input type="hidden" name="admin_id" value="<?= $_SESSION['admin_id'] ?>">
                            <button type="submit" class="btn btn-success">Take Chat</button>
                        </form>
                    <?php elseif ($chat['status'] === 'active' && $chat['assigned_admin_id'] == $_SESSION['admin_id']): ?>
                        <form action="/Tamim/admin/live-chat/close.php" method="POST" onsubmit="return confirm('Close this chat?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $chat['id'] ?>">
                            <button type="submit" class="btn btn-danger">Close Chat</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>

<script>
document.getElementById('adminChatForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const input = form.querySelector('input[name="message"]');
    const message = input.value.trim();
    if (!message) return;
    
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const formData = new FormData(form);
        formData.append('sender_type', 'admin');
        
        const response = await fetch('/Tamim/api/live-chat/send.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            // Append message to chat
            const messagesDiv = document.querySelector('.message-list');
            const msgDiv = document.createElement('div');
            msgDiv.className = 'message-item mb-3 text-end';
            msgDiv.innerHTML = `
                <div class="d-inline-block max-width-75 p-3 rounded-3 bg-primary text-white">
                    <small class="d-block mb-1 opacity-75">You <span class="ms-2">${new Date().toLocaleTimeString()}</span></small>
                    <p class="mb-0">${message.replace(/\n/g, '<br>')}</p>
                </div>
            `;
            messagesDiv.appendChild(msgDiv);
            messagesDiv.parentElement.scrollTop = messagesDiv.parentElement.scrollHeight;
        } else {
            alert('Failed to send: ' + data.message);
        }
    } catch (err) {
        alert('Error sending message');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Auto-scroll to bottom
const messagesContainer = document.querySelector('.chat-messages');
if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
</script>

<style>
.max-width-75 { max-width: 75%; }
.message-item { animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>