<?php
/** Consultant - Contact Messages */
$pageTitle = $lang === 'hi' ? 'संपर्क संदेश' : 'Contact Messages';
$db = getDB();
$db->query("UPDATE contact_messages SET is_read = 1");
$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <h1>
        <?= $pageTitle ?>
    </h1>
</div>
<?php if (empty($messages)): ?>
    <div class="card text-center" style="padding:var(--space-12);">
        <div style="font-size:3rem;">📧</div>
        <p class="text-muted mt-4">
            <?= t('no_results') ?>
        </p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:var(--space-4);">
        <?php foreach ($messages as $m): ?>
            <div class="card" style="padding:var(--space-6);">
                <div class="flex-between mb-3">
                    <div><strong>
                            <?= clean($m['name']) ?>
                        </strong> <span class="text-muted text-sm">—
                            <?= clean($m['email']) ?>
                        </span>
                        <?php if ($m['phone']): ?><span class="text-muted text-sm">|
                                <?= clean($m['phone']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="text-sm text-muted">
                        <?= timeAgo($m['created_at']) ?>
                    </span>
                </div>
                <?php if ($m['subject']): ?>
                    <h4 class="mb-2">
                        <?= clean($m['subject']) ?>
                    </h4>
                <?php endif; ?>
                <p style="line-height:1.7;color:var(--text-secondary);">
                    <?= nl2br(clean($m['message'])) ?>
                </p>
                <div class="mt-4 flex gap-3">
                    <a href="mailto:<?= clean($m['email']) ?>" class="btn btn-sm btn-outline">📧
                        <?= $lang === 'hi' ? 'ईमेल' : 'Reply' ?>
                    </a>
                    <?php if ($m['phone']): ?><a href="https://wa.me/91<?= preg_replace('/\D/', '', $m['phone']) ?>"
                            target="_blank" class="btn btn-sm btn-outline" style="color:#25d366;">💬 WhatsApp</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>