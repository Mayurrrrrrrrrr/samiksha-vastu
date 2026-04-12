<?php
/** User Chat Page */
$pageTitle = t('chat_title');
$db = getDB();
$uid = currentUserId();

// Get consultant ID
$consultant = $db->query("SELECT id, name FROM users WHERE role = 'consultant' LIMIT 1")->fetch();
$consultantId = $consultant['id'] ?? 1;

// Mark messages as read
$db->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?")->execute([$uid, $consultantId]);

// Get chat history
$messages = $db->prepare("SELECT cm.*, u.name as sender_name FROM chat_messages cm JOIN users u ON cm.sender_id = u.id WHERE (cm.sender_id = ? AND cm.receiver_id = ?) OR (cm.sender_id = ? AND cm.receiver_id = ?) ORDER BY cm.created_at ASC LIMIT 200");
$messages->execute([$uid, $consultantId, $consultantId, $uid]);
$messages = $messages->fetchAll();

require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <h1>
        <?= t('chat_title') ?>
    </h1>
</div>

<div class="card" style="overflow:hidden;height:calc(100vh - 240px);display:flex;flex-direction:column;">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="avatar avatar-sm avatar-placeholder">
            <?= mb_substr($consultant['name'] ?? 'S', 0, 1) ?>
        </div>
        <div>
            <strong>
                <?= clean($consultant['name'] ?? CONSULTANT_NAME) ?>
            </strong>
            <div id="chatStatus" style="font-size:var(--font-size-xs);color:var(--accent-green);">●
                <?= t('chat_online') ?>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="chat-messages" id="chatMessages">
        <?php if (empty($messages)): ?>
            <div style="text-align:center;padding:var(--space-12) 0;color:var(--text-muted);">
                <div style="font-size:3rem;margin-bottom:var(--space-4);">💬</div>
                <p>
                    <?= $lang === 'hi' ? 'बातचीत शुरू करें! अपना सवाल पूछें।' : 'Start a conversation! Ask your question.' ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?= $msg['sender_id'] == $uid ? 'sent' : 'received' ?>">
                    <?= clean($msg['message']) ?>
                    <div class="chat-message-time">
                        <?= date('h:i A', strtotime($msg['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Input -->
    <div class="chat-input">
        <input type="text" id="chatInput" placeholder="<?= t('chat_placeholder') ?>" autocomplete="off">
        <button onclick="sendMessage()" id="sendBtn">
            <?= t('send') ?>
        </button>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const myId = <?= $uid ?>;
    const receiverId = <?= $consultantId ?>;
    let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Send message
    async function sendMessage() {
        const msg = chatInput.value.trim();
        if (!msg) return;
        chatInput.value = '';

        // Add to UI immediately
        const div = document.createElement('div');
        div.className = 'chat-message sent';
        div.innerHTML = `${escapeHtml(msg)}<div class="chat-message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const res = await fetch('<?= BASE_URL ?>api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'send', receiver_id: receiverId, message: msg })
            });
            const data = await res.json();
            if (data.id) lastMessageId = data.id;
        } catch (e) { console.error(e); }
    }

    // Enter to send
    chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

    // Poll for new messages
    async function pollMessages() {
        try {
            const res = await fetch(`<?= BASE_URL ?>api/chat?action=poll&after=${lastMessageId}&partner=${receiverId}`);
            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.sender_id != myId) {
                        const div = document.createElement('div');
                        div.className = 'chat-message received';
                        div.innerHTML = `${escapeHtml(msg.message)}<div class="chat-message-time">${msg.time}</div>`;
                        chatMessages.appendChild(div);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
            }
        } catch (e) { }
    }

    setInterval(pollMessages, 3000);

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>