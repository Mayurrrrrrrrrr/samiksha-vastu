<?php
/** Consultant Dashboard */
$pageTitle = t('consultant_dashboard');
$db = getDB();

$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalSubs = $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
$pendingSubs = $db->query("SELECT COUNT(*) FROM submissions WHERE status = 'pending'")->fetchColumn();
$totalBlogs = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$totalVideos = $db->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$totalQuestions = $db->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$totalChats = $db->query("SELECT COUNT(*) FROM chat_messages")->fetchColumn();
$todaySubs = $db->query("SELECT COUNT(*) FROM submissions WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$recentSubs = $db->query("SELECT s.*, u.name as user_name FROM submissions s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC LIMIT 5")->fetchAll();
$recentContacts = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentUsers = $db->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5")->fetchAll();

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <h1>
        <?= $lang === 'hi' ? 'नमस्ते' : 'Hello' ?>,
        <?= CONSULTANT_NAME ?> ⭐
    </h1>
    <p>
        <?= $lang === 'hi' ? 'आपके प्लेटफॉर्म का सारांश' : 'Your platform overview' ?>
    </p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(230,126,34,0.1);color:var(--primary);">📋</div>
        <div class="stat-info">
            <h3>
                <?= $pendingSubs ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'लंबित आवश्यकताएं' : 'Pending Submissions' ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(52,152,219,0.1);color:#3498db;">👥</div>
        <div class="stat-info">
            <h3>
                <?= $totalUsers ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'कुल उपयोगकर्ता' : 'Total Users' ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(39,174,96,0.1);color:var(--accent-green);">📊</div>
        <div class="stat-info">
            <h3>
                <?= $totalSubs ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'कुल आवश्यकताएं' : 'Total Submissions' ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(142,68,173,0.1);color:#8e44ad;">💬</div>
        <div class="stat-info">
            <h3>
                <?= $unreadChats ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'अपठित चैट' : 'Unread Chats' ?>
            </p>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(241,196,15,0.1);color:#f1c40f;">📰</div>
        <div class="stat-info">
            <h3>
                <?= $totalBlogs ?>
            </h3>
            <p>
                <?= t('nav_blogs') ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(231,76,60,0.1);color:#e74c3c;">🎬</div>
        <div class="stat-info">
            <h3>
                <?= $totalVideos ?>
            </h3>
            <p>
                <?= t('nav_videos') ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(26,188,156,0.1);color:#1abc9c;">❓</div>
        <div class="stat-info">
            <h3>
                <?= $unansweredQ ?>/
                <?= $totalQuestions ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'अनुत्तरित' : 'Unanswered' ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(149,165,166,0.1);color:#95a5a6;">📧</div>
        <div class="stat-info">
            <h3>
                <?= $newContacts ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'नए संपर्क' : 'New Contacts' ?>
            </p>
        </div>
    </div>
</div>

<!-- Recent Submissions -->
<div class="grid grid-2" style="gap:var(--space-6);">
    <div class="card" style="overflow:hidden;">
        <div
            style="padding:var(--space-4) var(--space-6);border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;">
            <h3>
                <?= $lang === 'hi' ? 'हाल की आवश्यकताएं' : 'Recent Submissions' ?>
            </h3>
            <a href="<?= BASE_URL ?>consultant/submissions" class="btn btn-sm btn-outline">
                <?= t('view_all') ?>
            </a>
        </div>
        <?php if (empty($recentSubs)): ?>
            <div style="padding:var(--space-8);text-align:center;color:var(--text-muted);">
                <?= t('no_results') ?>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <?= $lang === 'hi' ? 'उपयोगकर्ता' : 'User' ?>
                        </th>
                        <th>
                            <?= t('property_type') ?>
                        </th>
                        <th>
                            <?= t('submission_status') ?>
                        </th>
                        <th>
                            <?= $lang === 'hi' ? 'तारीख' : 'Date' ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSubs as $sub): ?>
                        <tr style="cursor:pointer;"
                            onclick="location.href='<?= BASE_URL ?>consultant/submission?id=<?= $sub['id'] ?>'">
                            <td><strong>
                                    <?= clean($sub['user_name']) ?>
                                </strong></td>
                            <td>
                                <?= t($sub['property_type'] ?? 'house') ?>
                            </td>
                            <td><span
                                    class="badge badge-<?= $sub['status'] === 'completed' ? 'success' : ($sub['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                                    <?= t('status_' . $sub['status']) ?>
                                </span></td>
                            <td class="text-muted">
                                <?= timeAgo($sub['created_at']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Recent Contacts -->
    <div class="card" style="overflow:hidden;">
        <div
            style="padding:var(--space-4) var(--space-6);border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;">
            <h3>
                <?= $lang === 'hi' ? 'हाल के संपर्क' : 'Recent Contacts' ?>
            </h3>
            <a href="<?= BASE_URL ?>consultant/contacts" class="btn btn-sm btn-outline">
                <?= t('view_all') ?>
            </a>
        </div>
        <?php if (empty($recentContacts)): ?>
            <div style="padding:var(--space-8);text-align:center;color:var(--text-muted);">
                <?= t('no_results') ?>
            </div>
        <?php else: ?>
            <div style="max-height:300px;overflow-y:auto;">
                <?php foreach ($recentContacts as $c): ?>
                    <div style="padding:var(--space-4) var(--space-6);border-bottom:1px solid var(--border-color);">
                        <div class="flex-between mb-1">
                            <strong class="text-sm">
                                <?= clean($c['name']) ?>
                            </strong>
                            <span class="text-xs text-muted">
                                <?= timeAgo($c['created_at']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-muted">
                            <?= clean(mb_substr($c['message'], 0, 80)) ?>...
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Users -->
<div class="card mt-6" style="overflow:hidden;">
    <div style="padding:var(--space-4) var(--space-6);border-bottom:1px solid var(--border-color);">
        <h3>
            <?= $lang === 'hi' ? 'नए उपयोगकर्ता' : 'Recent Users' ?>
        </h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>
                    <?= $lang === 'hi' ? 'नाम' : 'Name' ?>
                </th>
                <th>Email</th>
                <th>
                    <?= t('phone') ?>
                </th>
                <th>
                    <?= $lang === 'hi' ? 'जुड़े' : 'Joined' ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentUsers as $u): ?>
                <tr>
                    <td><strong>
                            <?= clean($u['name']) ?>
                        </strong></td>
                    <td>
                        <?= clean($u['email']) ?>
                    </td>
                    <td>
                        <?= clean($u['phone'] ?? '-') ?>
                    </td>
                    <td class="text-muted">
                        <?= timeAgo($u['created_at']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>