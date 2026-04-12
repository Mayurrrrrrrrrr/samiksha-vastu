<?php
/** Public Q&A Page */
$pageTitle = t('questions_title');
$db = getDB();
$questions = $db->query("SELECT q.*, u.name as user_name, (SELECT COUNT(*) FROM question_replies WHERE question_id = q.id) as reply_count FROM questions q JOIN users u ON q.user_id = u.id WHERE q.is_public = 1 ORDER BY q.created_at DESC LIMIT 50")->fetchAll();
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('questions_title') ?>
    </h1>
    <p>
        <?= $lang === 'hi' ? 'वास्तु और अंक ज्योतिष से संबंधित प्रश्न और उत्तर' : 'Questions and answers related to Vastu & Numerology' ?>
    </p>
</div>
<section class="section">
    <div class="container container-narrow">
        <?php if (isLoggedIn()): ?>
            <div class="mb-8"><a href="<?= BASE_URL ?>user/ask" class="btn btn-primary">
                    <?= t('ask_question') ?> →
                </a></div>
        <?php else: ?>
            <div class="mb-8"><a href="<?= BASE_URL ?>login" class="btn btn-primary">
                    <?= t('nav_login') ?>
                    <?= $lang === 'hi' ? 'करके प्रश्न पूछें' : 'to Ask a Question' ?>
                </a></div>
        <?php endif; ?>

        <?php if (empty($questions)): ?>
            <div class="text-center" style="padding:var(--space-12) 0;">
                <p style="font-size:3rem;">❓</p>
                <p class="text-muted mt-4">
                    <?= t('no_results') ?>
                </p>
            </div>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:var(--space-4);">
                <?php foreach ($questions as $q): ?>
                    <div class="card" style="padding:var(--space-6);">
                        <div class="flex-between mb-4">
                            <span class="badge <?= $q['is_answered'] ? 'badge-success' : 'badge-warning' ?>">
                                <?= $q['is_answered'] ? t('answered') : t('pending') ?>
                            </span>
                            <span class="text-sm text-muted">
                                <?= timeAgo($q['created_at']) ?>
                            </span>
                        </div>
                        <h3 style="font-size:var(--font-size-lg);margin-bottom:var(--space-3);">
                            <?= clean($q['title']) ?>
                        </h3>
                        <p class="text-muted text-sm" style="margin-bottom:var(--space-3);">
                            <?= clean(mb_substr($q['body'], 0, 200)) ?>...
                        </p>
                        <div class="flex-between">
                            <span class="text-sm text-muted">
                                <?= t('posted_by') ?>
                                <?= clean($q['user_name']) ?>
                            </span>
                            <span class="text-sm text-muted">💬
                                <?= $q['reply_count'] ?>
                                <?= $lang === 'hi' ? 'जवाब' : 'replies' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>