<?php
/**
 * Admin Message View
 * Portfolio Website - Admin
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!auth_check()) {
    redirect('/admin/login.php');
}

$id = (int)($_GET['id'] ?? 0);
$message = db_one('SELECT * FROM messages WHERE id = ?', [$id]);

if (!$message) {
    redirect('/admin/messages/', 'Message not found.', 'danger');
}

// Mark as read
if (!$message['is_read']) {
    db_execute('UPDATE messages SET is_read = 1 WHERE id = ?', [$id]);
}

$pageTitle = 'View Message';
$currentPage = 'messages';

require __DIR__ . '/../../includes/admin_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
    <a href="/admin/messages/" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?= e($message['subject'] ?: 'No Subject') ?></h5>
                <span class="badge <?= !$message['is_read'] ? 'bg-primary' : 'bg-success' ?>">
                    <?= !$message['is_read'] ? 'Unread' : 'Read' ?>
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4 p-3 bg-light rounded">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Name:</strong><br>
                            <?= e($message['name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
                        </div>
                        <?php if ($message['phone']): ?>
                            <div class="col-md-6">
                                <strong>Phone:</strong><br>
                                <a href="tel:<?= e($message['phone']) ?>"><?= e($message['phone']) ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <strong>Received:</strong><br>
                            <?= format_date($message['created_at'], 'M d, Y H:i') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>IP Address:</strong><br>
                            <code><?= e($message['ip_address']) ?></code>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <strong>Message:</strong>
                    <div class="mt-2 p-3 bg-light rounded white-space-pre-wrap"><?= e($message['message']) ?></div>
                </div>
                
                <?php if ($message['replied_at']): ?>
                    <div class="border-top pt-4">
                        <strong>Reply sent on <?= format_date($message['replied_at']) ?>:</strong>
                        <div class="mt-2 p-3 bg-success bg-opacity-10 rounded"><?= e($message['reply_message']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if (!$message['is_read']): ?>
                        <a href="/admin/messages/?read=<?= $message['id'] ?>" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Mark as Read
                        </a>
                    <?php else: ?>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-check me-2"></i>Already Read
                        </button>
                    <?php endif; ?>
                    
                    <a href="mailto:<?= e($message['email']) ?>?subject=Re:%20<?= urlencode($message['subject'] ?: 'Your Message') ?>" class="btn btn-primary">
                        <i class="fas fa-reply me-2"></i>Reply via Email
                    </a>
                    
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#replyModal">
                        <i class="fas fa-paper-plane me-2"></i>Send Reply (Save in System)
                    </button>
                    
                    <hr>
                    
                    <a href="/admin/messages/?archive=<?= $message['id'] ?>" class="btn btn-outline-secondary" onclick="return confirm('Archive this message?')">
                        <i class="fas fa-archive me-2"></i>Archive
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($message['user_agent']): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Technical Details</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted"><strong>User Agent:</strong></small>
                    <pre class="small text-muted mt-1" style="max-height: 150px; overflow: auto;"><?= e($message['user_agent']) ?></pre>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/admin/messages/reply.php">
                <?= csrf_field() ?>
                <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Send Reply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <input type="email" class="form-control" value="<?= e($message['email']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" value="Re: <?= e($message['subject'] ?: 'Your Message') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="reply_message" rows="6" required placeholder="Type your reply here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/admin_footer.php'; ?>
