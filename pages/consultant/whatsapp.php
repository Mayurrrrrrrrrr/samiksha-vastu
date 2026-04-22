<?php
/** WhatsApp Messaging Panel - Admin */
$pageTitle = 'WhatsApp Panel';
require __DIR__ . '/../../layouts/consultant_header.php';

$db = getDB();
// Fetch active clients (users with phone numbers)
$stmt = $db->query("
    SELECT u.id as client_id, u.name, u.phone as mobile, 
    (SELECT status FROM submissions WHERE user_id = u.id ORDER BY id DESC LIMIT 1) as latest_sub
    FROM users u 
    WHERE u.role = 'user' AND u.phone IS NOT NULL AND u.phone != ''
    ORDER BY u.created_at DESC
");
$clients = $stmt->fetchAll();

// Fetch templates
$templates = $db->query("SELECT id, name, category, message_body FROM whatsapp_templates WHERE is_active = 1")->fetchAll();
$templatesJson = json_encode($templates);
?>

<div class="dash-header">
    <h1>📱 WhatsApp Broadcast & Outreach</h1>
    <p>Send personalized template messages to clients directly.</p>
</div>

<div style="display: flex; gap: var(--space-6); align-items: stretch; min-height: 70vh;">
    
    <!-- LEFT PANEL (Clients) -->
    <div class="card" style="flex: 0 0 40%; display: flex; flex-direction: column;">
        <div style="padding: var(--space-4); border-bottom: 1px solid var(--border-color);">
            <input type="text" id="clientSearch" class="form-control" placeholder="Search by name or mobile..." onkeyup="filterClients()">
        </div>
        <div id="clientList" style="flex: 1; overflow-y: auto; padding: var(--space-2);">
            <?php foreach ($clients as $client): 
                $status = $client['latest_sub'] ?? 'new';
                $badgeBg = match($status) {
                    'pending' => '#FFC107',
                    'completed' => '#28A745',
                    default => '#007BFF'
                };
            ?>
            <div class="client-row" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid var(--border-color); cursor: pointer;"
                 onclick="selectClient(<?= $client['client_id'] ?>, '<?= htmlspecialchars($client['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($client['mobile'], ENT_QUOTES) ?>')">
                <div>
                    <strong class="client-name"><?= htmlspecialchars($client['name']) ?></strong><br>
                    <span class="text-muted text-sm client-mobile">📞 <?= htmlspecialchars($client['mobile']) ?></span>
                </div>
                <div>
                    <span style="background: <?= $badgeBg ?>; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; text-transform: uppercase;">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RIGHT PANEL (Compose) -->
    <div class="card" style="flex: 1; padding: var(--space-6); display: flex; flex-direction: column;">
        <div id="compose-placeholder" style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
            <h3>Select a client from the list to compose a message.</h3>
        </div>
        
        <div id="compose-panel" style="display: none; flex: 1; flex-direction: column;">
            <div style="margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 4px;">Messaging: <span id="selClientName" style="color: var(--primary);"></span></h3>
                <p class="text-sm text-muted">Mobile: <span id="selClientMobile"></span></p>
                <input type="hidden" id="selClientId">
            </div>

            <div class="form-group">
                <label class="form-label">Select Template</label>
                <select id="selTemplate" class="form-control" onchange="applyTemplate()">
                    <option value="">-- Choose a message template --</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> [<?= ucfirst($t['category']) ?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1; display: flex; flex-direction: column;">
                <label class="form-label">Message Body (Editable)</label>
                <textarea id="msgBody" class="form-control" style="flex: 1; min-height: 150px; resize: none;"></textarea>
                <small class="text-muted" style="margin-top: 8px;">Ensure placeholders like {date} or {amount} are manually replaced before sending.</small>
            </div>

            <button class="btn btn-primary btn-lg" style="background: #25D366; border-color: #25D366; align-self: flex-end;" onclick="sendWhatsApp()">
                Open in WhatsApp Web 💬
            </button>
        </div>
    </div>
</div>

<script>
const templates = <?= $templatesJson ?>;

function filterClients() {
    const q = document.getElementById('clientSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.client-row');
    rows.forEach(row => {
        const name = row.querySelector('.client-name').innerText.toLowerCase();
        const mob = row.querySelector('.client-mobile').innerText.toLowerCase();
        if (name.includes(q) || mob.includes(q)) {
            row.style.display = 'flex';
        } else {
            row.style.display = 'none';
        }
    });
}

function selectClient(id, name, mobile) {
    document.getElementById('compose-placeholder').style.display = 'none';
    const panel = document.getElementById('compose-panel');
    panel.style.display = 'flex';

    document.getElementById('selClientId').value = id;
    document.getElementById('selClientName').innerText = name;
    document.getElementById('selClientMobile').innerText = mobile;
    
    // Auto-apply current template logic
    applyTemplate();
    
    // Highlight active row
    document.querySelectorAll('.client-row').forEach(r => r.style.background = 'transparent');
    event.currentTarget.style.background = 'var(--bg-color)';
}

function applyTemplate() {
    const tId = document.getElementById('selTemplate').value;
    const name = document.getElementById('selClientName').innerText.split(' ')[0]; // First name only
    
    if (!tId) return;
    
    const t = templates.find(x => x.id == tId);
    if (t) {
        let msg = t.message_body;
        // Replace known variables automatically
        msg = msg.replace(/{name}/g, name);
        document.getElementById('msgBody').value = msg;
    }
}

async function sendWhatsApp() {
    const cId = document.getElementById('selClientId').value;
    const cName = document.getElementById('selClientName').innerText;
    let cMob = document.getElementById('selClientMobile').innerText;
    const tId = document.getElementById('selTemplate').value;
    const msg = document.getElementById('msgBody').value.trim();

    if (!msg) {
        alert("Message cannot be empty.");
        return;
    }

    // Clean mobile
    cMob = cMob.replace(/[^0-9]/g, '');
    if (cMob.length === 10) cMob = '91' + cMob;

    // Log the message asynchronous via AJAX
    try {
        const formData = new FormData();
        formData.append('client_id', cId);
        formData.append('client_name', cName);
        formData.append('client_mobile', cMob);
        formData.append('template_id', tId || 0);
        formData.append('message_sent', msg);

        await fetch('<?= BASE_URL ?>api/admin/log_whatsapp.php', {
            method: 'POST',
            body: formData
        });
    } catch(e) {
        console.error("Failed to log message", e);
    }

    // Launch WhatsApp Web Native
    const encoded = encodeURIComponent(msg);
    const waUrl = `https://web.whatsapp.com/send?phone=${cMob}&text=${encoded}`;
    window.open(waUrl, '_blank');
}
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
