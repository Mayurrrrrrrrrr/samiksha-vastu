<?php
/** Consultant - Manage Questions */
$pageTitle = $lang === 'hi' ? 'प्रश्न प्रबंधित करें' : 'Manage Questions';
$db = getDB();

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $db->prepare("INSERT INTO question_replies (question_id, user_id, reply) VALUES (?,?,?)")->execute([$_POST['question_id'], currentUserId(), $_POST['reply']]);
    $db->prepare("UPDATE questions SET is_answered = 1 WHERE id = ?")->execute([$_POST['question_id']]);
    setFlash('success', $lang === 'hi' ? 'जवाब दिया गया!' : 'Reply sent!');
    header('Location:' . BASE_URL . 'consultant/questions');
    exit;
}

$questions = $db->query("SELECT q.*, u.name as user_name, (SELECT COUNT(*) FROM question_replies WHERE question_id = q.id) as reply_count FROM questions q JOIN users u ON q.user_id = u.id ORDER BY q.is_answered ASC, q.created_at DESC")->fetchAll();
require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <h1>
        <?= $pageTitle ?>
    </h1>
</div>

<?php if (empty($questions)): ?>
    <div class="card text-center" style="padding:var(--space-12);">
        <div style="font-size:3rem;">❓</div>
        <p class="text-muted mt-4">
            <?= t('no_results') ?>
        </p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:var(--space-4);">
        <?php foreach ($questions as $q): ?>
            <div class="card"
                style="padding:var(--space-6);<?= !$q['is_answered'] ? 'border-left:4px solid var(--primary);' : '' ?>">
                <div class="flex-between mb-3">
                    <div>
                        <span class="badge <?= $q['is_answered'] ? 'badge-success' : 'badge-warning' ?>">
                            <?= $q['is_answered'] ? t('answered') : t('pending') ?>
                        </span>
                        <span class="badge badge-info ml-2">
                            <?= $q['category'] ?>
                        </span>
                    </div>
                    <span class="text-sm text-muted">
                        <?= clean($q['user_name']) ?> •
                        <?= timeAgo($q['created_at']) ?>
                    </span>
                </div>
                <h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-2);">
                    <?= clean($q['title']) ?>
                </h3>
                <p class="text-muted text-sm mb-4">
                    <?= nl2br(clean($q['body'])) ?>
                </p>

                <?php
                $replies = $db->prepare("SELECT r.*, u.name, u.role FROM question_replies r JOIN users u ON r.user_id = u.id WHERE r.question_id = ? ORDER BY r.created_at ASC");
                $replies->execute([$q['id']]);
                $replies = $replies->fetchAll();
                if (!empty($replies)): ?>
                    <div style="border-top:1px solid var(--border-color);padding-top:var(--space-4);margin-bottom:var(--space-4);">
                        <?php foreach ($replies as $r): ?>
                            <div
                                style="padding:var(--space-3);margin-bottom:var(--space-2);background:<?= $r['role'] === 'consultant' ? 'rgba(230,126,34,0.05)' : 'var(--bg-section)' ?>;border-radius:var(--border-radius);border-left:3px solid <?= $r['role'] === 'consultant' ? 'var(--primary)' : 'var(--border-color)' ?>;">
                                <div class="flex-between mb-1"><span class="font-bold text-sm">
                                        <?= clean($r['name']) ?>
                                        <?= $r['role'] === 'consultant' ? '⭐' : '' ?>
                                    </span><span class="text-xs text-muted">
                                        <?= timeAgo($r['created_at']) ?>
                                    </span></div>
                                <p class="text-sm">
                                    <?= nl2br(clean($r['reply'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Reply Form -->
                <form method="POST" style="border-top:1px solid var(--border-color);padding-top:var(--space-4);">
                    <?= csrfField() ?>
                    <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                    <div style="display:flex;gap:var(--space-3);">
                        <textarea name="reply" class="form-control" rows="2" required
                            placeholder="<?= $lang === 'hi' ? 'जवाब लिखें...' : 'Write your reply...' ?>" style="flex:1;"></textarea>
                        <button type="submit" class="btn btn-primary" style="align-self:flex-end;">
                            <?= t('send') ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>