<?php
/** Consultant Chat Page */
$pageTitle = t('chat_title');
$db = getDB();
$cid = currentUserId();

// Get users who have chatted
$chatUsers = $db->query("SELECT DISTINCT u.id, u.name, 
    (SELECT message FROM chat_messages WHERE (sender_id = u.id AND receiver_id = $cid) OR (sender_id = $cid AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
    (SELECT created_at FROM chat_messages WHERE (sender_id = u.id AND receiver_id = $cid) OR (sender_id = $cid AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_time,
    (SELECT COUNT(*) FROM chat_messages WHERE sender_id = u.id AND receiver_id = $cid AND is_read = 0) as unread
    FROM users u 
    INNER JOIN chat_messages cm ON (cm.sender_id = u.id OR cm.receiver_id = u.id) 
    WHERE u.id != $cid AND u.role = 'user'
    GROUP BY u.id 
    ORDER BY last_time DESC")->fetchAll();

$activeUserId = intval($_GET['user'] ?? ($chatUsers[0]['id'] ?? 0));

if ($activeUserId) {
    $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?")->execute([$cid, $activeUserId]);
    $messages = $db->prepare("SELECT cm.*, u.name as sender_name FROM chat_messages cm JOIN users u ON cm.sender_id = u.id WHERE (cm.sender_id = ? AND cm.receiver_id = ?) OR (cm.sender_id = ? AND cm.receiver_id = ?) ORDER BY cm.created_at ASC LIMIT 500");
    $messages->execute([$cid, $activeUserId, $activeUserId, $cid]);
    $messages = $messages->fetchAll();
    $activeUser = $db->prepare("SELECT name FROM users WHERE id = ?");
    $activeUser->execute([$activeUserId]);
    $activeUserName = $activeUser->fetchColumn();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <h1>
        <?= t('chat_title') ?>
    </h1>
</div>

<div class="chat-container">
    <!-- Users List -->
    <div class="chat-users">
        <?php if (empty($chatUsers)): ?>
            <div style="padding:var(--space-8);text-align:center;color:var(--text-muted);">💬
                <?= $lang === 'hi' ? 'कोई चैट नहीं' : 'No chats yet' ?>
            </div>
        <?php else: ?>
            <?php foreach ($chatUsers as $cu): ?>
                <a href="?user=<?= $cu['id'] ?>" class="chat-user-item <?= $activeUserId == $cu['id'] ? 'active' : '' ?>"
                    style="text-decoration:none;color:var(--text-primary);">
                    <div class="avatar avatar-sm avatar-placeholder">
                        <?= mb_substr($cu['name'], 0, 1) ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="flex-between"><strong class="text-sm">
                                <?= clean($cu['name']) ?>
                            </strong>
                            <?php if ($cu['unread']): ?><span class="badge badge-primary" style="font-size:10px;">
                                    <?= $cu['unread'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-muted" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= clean(mb_substr($cu['last_message'] ?? '', 0, 30)) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Chat Main -->
    <div class="chat-main">
        <?php if ($activeUserId): ?>
            <div class="chat-header">
                <div class="avatar avatar-sm avatar-placeholder">
                    <?= mb_substr($activeUserName ?? 'U', 0, 1) ?>
                </div>
                <strong>
                    <?= clean($activeUserName ?? 'User') ?>
                </strong>
            </div>
            <div class="chat-messages" id="chatMessages">
                <?php foreach ($messages as $msg): ?>
                    <div class="chat-message <?= $msg['sender_id'] == $cid ? 'sent' : 'received' ?>">
                        <?= clean($msg['message']) ?>
                        <div class="chat-message-time">
                            <?= date('h:i A', strtotime($msg['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="<?= t('chat_placeholder') ?>" autocomplete="off">
                <button onclick="sendMsg()">
                    <?= t('send') ?>
                </button>
            </div>
        <?php else: ?>
            <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-muted);">
                <div class="text-center">
                    <div style="font-size:3rem;">💬</div>
                    <p class="mt-4">
                        <?= $lang === 'hi' ? 'चैट शुरू करने के लिए बाईं ओर से उपयोगकर्ता चुनें' : 'Select a user from the left to start chatting' ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($activeUserId): ?>
    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        chatMessages.scrollTop = chatMessages.scrollHeight;
        let lastId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;

        async function sendMsg() {
            const msg = chatInput.value.trim();
            if (!msg) return;
            chatInput.value = '';
            const div = document.createElement('div');
            div.className = 'chat-message sent';
            div.innerHTML = `${msg}<div class="chat-message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>`;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            try {
                const res = await fetch('<?= BASE_URL ?>api/chat', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'send', receiver_id:<?= $activeUserId ?>, message: msg }) });
                const data = await res.json();
                if (data.id) lastId = data.id;
            } catch (e) { }
        }
        chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMsg(); });

        setInterval(async () => {
            try {
                const res = await fetch(`<?= BASE_URL ?>api/chat?action=poll&after=${lastId}&partner=<?= $activeUserId ?>`);
                const data = await res.json();
                if (data.messages) data.messages.forEach(m => {
                    if (m.sender_id != <?= $cid ?>) {
                        const div = document.createElement('div');
                        div.className = 'chat-message received';
                        div.innerHTML = `${m.message}<div class="chat-message-time">${m.time}</div>`;
                        chatMessages.appendChild(div);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                    lastId = Math.max(lastId, m.id);
                });
            } catch (e) { }
        }, 3000);
    </script>
<?php endif; ?>

<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>