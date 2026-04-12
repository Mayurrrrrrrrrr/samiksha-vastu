<?php
/** My Questions */
$pageTitle = t('my_questions');
$db = getDB();
$questions = $db->prepare("SELECT q.*, (SELECT COUNT(*) FROM question_replies WHERE question_id = q.id) as reply_count FROM questions q WHERE q.user_id = ? ORDER BY q.created_at DESC");
$questions->execute([currentUserId()]);
$questions = $questions->fetchAll();
require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= t('my_questions') ?>
        </h1>
    </div>
    <a href="<?= BASE_URL ?>user/ask" class="btn btn-primary">
        <?= t('ask_question') ?> +
    </a>
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
            <div class="card" style="padding:var(--space-6);">
                <div class="flex-between mb-3">
                    <span class="badge <?= $q['is_answered'] ? 'badge-success' : 'badge-warning' ?>">
                        <?= $q['is_answered'] ? t('answered') : t('pending') ?>
                    </span>
                    <span class="text-sm text-muted">
                        <?= timeAgo($q['created_at']) ?>
                    </span>
                </div>
                <h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-2);">
                    <?= clean($q['title']) ?>
                </h3>
                <p class="text-muted text-sm mb-4">
                    <?= clean(mb_substr($q['body'], 0, 200)) ?>
                </p>
                <?php if ($q['reply_count'] > 0):
                    $replies = $db->prepare("SELECT r.*, u.name, u.role FROM question_replies r JOIN users u ON r.user_id = u.id WHERE r.question_id = ? ORDER BY r.created_at ASC");
                    $replies->execute([$q['id']]);
                    $replies = $replies->fetchAll();
                    ?>
                    <div style="border-top:1px solid var(--border-color);padding-top:var(--space-4);margin-top:var(--space-2);">
                        <?php foreach ($replies as $r): ?>
                            <div
                                style="padding:var(--space-3);margin-bottom:var(--space-2);background:<?= $r['role'] === 'consultant' ? 'rgba(230,126,34,0.05)' : 'var(--bg-section)' ?>;border-radius:var(--border-radius);border-left:3px solid <?= $r['role'] === 'consultant' ? 'var(--primary)' : 'var(--border-color)' ?>;">
                                <div class="flex-between mb-1">
                                    <span class="font-bold text-sm">
                                        <?= clean($r['name']) ?>
                                        <?= $r['role'] === 'consultant' ? '⭐' : '' ?>
                                    </span>
                                    <span class="text-xs text-muted">
                                        <?= timeAgo($r['created_at']) ?>
                                    </span>
                                </div>
                                <p class="text-sm">
                                    <?= nl2br(clean($r['reply'])) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>