<?php
/** User Dashboard */
$pageTitle = t('user_dashboard');
$db = getDB();
$uid = currentUserId();

$subCount = $db->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ?");
$subCount->execute([$uid]);
$subCount = $subCount->fetchColumn();
$qCount = $db->prepare("SELECT COUNT(*) FROM questions WHERE user_id = ?");
$qCount->execute([$uid]);
$qCount = $qCount->fetchColumn();
$completedCount = $db->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ? AND status = 'completed'");
$completedCount->execute([$uid]);
$completedCount = $completedCount->fetchColumn();

$recentSubs = $db->prepare("SELECT * FROM submissions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentSubs->execute([$uid]);
$recentSubs = $recentSubs->fetchAll();

$userStmt = $db->prepare("SELECT latitude, longitude, location_token FROM users WHERE id = ?");
$userStmt->execute([$uid]);
$currentUserInfo = $userStmt->fetch();

require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <h1>
        <?= $lang === 'hi' ? 'नमस्ते' : 'Hello' ?>,
        <?= clean(currentUserName()) ?> 🙏
    </h1>
    <p>
        <?= $lang === 'hi' ? 'आपके डैशबोर्ड में स्वागत है' : 'Welcome to your dashboard' ?>
    </p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(230,126,34,0.1);color:var(--primary);">📋</div>
        <div class="stat-info">
            <h3>
                <?= $subCount ?>
            </h3>
            <p>
                <?= t('my_submissions') ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(39,174,96,0.1);color:var(--accent-green);">✅</div>
        <div class="stat-info">
            <h3>
                <?= $completedCount ?>
            </h3>
            <p>
                <?= t('status_completed') ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(52,152,219,0.1);color:#3498db;">❓</div>
        <div class="stat-info">
            <h3>
                <?= $qCount ?>
            </h3>
            <p>
                <?= t('my_questions') ?>
            </p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(142,68,173,0.1);color:#8e44ad;">💬</div>
        <div class="stat-info">
            <h3>
                <?= $unreadCount ?>
            </h3>
            <p>
                <?= $lang === 'hi' ? 'अपठित संदेश' : 'Unread Messages' ?>
            </p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-4" style="margin-bottom:var(--space-8);">
    <a href="<?= BASE_URL ?>user/submit" class="card"
        style="padding:var(--space-6);text-align:center;text-decoration:none;color:var(--text-primary);">
        <div style="font-size:2rem;margin-bottom:var(--space-2);">📋</div>
        <h4>
            <?= t('submission_title') ?>
        </h4>
        <p class="text-sm text-muted">
            <?= $lang === 'hi' ? 'नई आवश्यकता जमा करें' : 'Submit new requirement' ?>
        </p>
    </a>
    <a href="<?= BASE_URL ?>user/chat" class="card"
        style="padding:var(--space-6);text-align:center;text-decoration:none;color:var(--text-primary);">
        <div style="font-size:2rem;margin-bottom:var(--space-2);">💬</div>
        <h4>
            <?= t('nav_chat') ?>
        </h4>
        <p class="text-sm text-muted">
            <?= $lang === 'hi' ? 'सलाहकार से चैट करें' : 'Chat with consultant' ?>
        </p>
    </a>
    <a href="<?= BASE_URL ?>user/ask" class="card"
        style="padding:var(--space-6);text-align:center;text-decoration:none;color:var(--text-primary);">
        <div style="font-size:2rem;margin-bottom:var(--space-2);">❓</div>
        <h4>
            <?= t('ask_question') ?>
        </h4>
        <p class="text-sm text-muted">
            <?= $lang === 'hi' ? 'प्रश्न पूछें' : 'Ask a question' ?>
        </p>
    </a>
    <a href="<?= BASE_URL ?>capture_location<?= !empty($currentUserInfo['location_token']) ? '?token='.$currentUserInfo['location_token'] : '' ?>" target="_blank" class="card"
        style="padding:var(--space-6);text-align:center;text-decoration:none;color:var(--text-primary); border: 2px dashed var(--primary);">
        <div style="font-size:2rem;margin-bottom:var(--space-2);">📍</div>
        <h4>
            <?= $lang === 'hi' ? 'स्थान अपडेट करें' : 'Update Location' ?>
        </h4>
        <p class="text-sm text-muted">
            <?= $lang === 'hi' ? 'अपना वर्तमान स्थान साझा करें' : 'Share your current location' ?>
        </p>
    </a>
</div>

<?php if(!empty($currentUserInfo['latitude']) && !empty($currentUserInfo['longitude'])): ?>
<div class="card" style="margin-bottom:var(--space-8); padding: var(--space-4) var(--space-6); border-left: 4px solid var(--accent-green); background-color: rgba(39, 174, 96, 0.05);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h4 style="margin:0 0 5px 0;">📍 <?= $lang === 'hi' ? 'आपका सहेजा गया स्थान' : 'Your Saved Location' ?></h4>
            <p style="margin:0; font-size:0.9em; color:var(--text-muted);">
                <?= $currentUserInfo['latitude'] ?>, <?= $currentUserInfo['longitude'] ?>
            </p>
        </div>
        <a href="https://maps.google.com/?q=<?= $currentUserInfo['latitude'] ?>,<?= $currentUserInfo['longitude'] ?>" target="_blank" class="btn btn-sm" style="background:#e8f4f8;color:#3498db;padding:6px 12px;font-size:13px;text-decoration:none;">
            <?= $lang === 'hi' ? 'नक्शे पर देखें' : 'View on Map' ?>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Recent Submissions -->
<div class="card" style="overflow:hidden;">
    <div style="padding:var(--space-4) var(--space-6);border-bottom:1px solid var(--border-color);">
        <h3>
            <?= $lang === 'hi' ? 'हाल की आवश्यकताएं' : 'Recent Submissions' ?>
        </h3>
    </div>
    <?php if (empty($recentSubs)): ?>
        <div style="padding:var(--space-8);text-align:center;color:var(--text-muted);">
            <?= $lang === 'hi' ? 'कोई आवश्यकता नहीं। ' : 'No submissions yet. ' ?>
            <a href="<?= BASE_URL ?>user/submit">
                <?= $lang === 'hi' ? 'अभी जमा करें' : 'Submit now' ?>
            </a>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <?= t('property_type') ?>
                    </th>
                    <th>
                        <?= t('address') ?>
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
                    <tr>
                        <td>
                            <?= $sub['id'] ?>
                        </td>
                        <td>
                            <?= t($sub['property_type'] ?? 'house') ?>
                        </td>
                        <td>
                            <?= clean(mb_substr($sub['address'] ?? '-', 0, 40)) ?>
                        </td>
                        <td><span
                                class="badge badge-<?= $sub['status'] === 'completed' ? 'success' : ($sub['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                                <?= t('status_' . $sub['status']) ?>
                            </span></td>
                        <td class="text-muted">
                            <?= date('d/m/Y', strtotime($sub['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>