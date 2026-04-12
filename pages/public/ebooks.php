<?php
/** E-Books Page */
$pageTitle = t('ebooks_title');
$db = getDB();
$ebooks = $db->query("SELECT * FROM ebooks WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('ebooks_title') ?>
    </h1>
    <p>
        <?= t('ebooks_subtitle') ?>
    </p>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> / <span>
            <?= t('nav_ebooks') ?>
        </span></div>
</div>
<section class="section">
    <div class="container">
        <?php if (empty($ebooks)): ?>
            <div class="text-center" style="padding:var(--space-20) 0;">
                <p style="font-size:3rem;">📚</p>
                <p class="text-muted mt-4">
                    <?= t('no_results') ?>
                </p>
            </div>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:var(--space-6);">
                <?php foreach ($ebooks as $eb): ?>
                    <div class="ebook-card reveal">
                        <div class="ebook-cover">
                            <?php if ($eb['cover_image']): ?>
                                <img src="<?= UPLOADS_URL . $eb['cover_image'] ?>" alt=""
                                    style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                📖
                            <?php endif; ?>
                        </div>
                        <div class="ebook-info">
                            <h3>
                                <?= clean($lang === 'hi' && $eb['title_hi'] ? $eb['title_hi'] : $eb['title']) ?>
                            </h3>
                            <p>
                                <?= clean($lang === 'hi' && $eb['description_hi'] ? $eb['description_hi'] : $eb['description']) ?>
                            </p>
                            <div class="ebook-meta">
                                <?php if ($eb['pages']): ?><span>📄
                                        <?= $eb['pages'] ?>
                                        <?= $lang === 'hi' ? 'पेज' : 'pages' ?>
                                    </span>
                                <?php endif; ?>
                                <span>📥
                                    <?= $eb['downloads'] ?>
                                    <?= t('downloads') ?>
                                </span>
                                <?php if ($eb['is_free']): ?><span class="badge badge-success">
                                        <?= t('free') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-2);">
                                <?php if (isLoggedIn()): ?>
                                    <a href="<?= UPLOADS_URL . $eb['file_path'] ?>" class="btn btn-primary btn-sm" download>
                                        <?= t('download') ?> →
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-sm">
                                        <?= t('nav_login') ?>
                                        <?= $lang === 'hi' ? 'करके डाउनलोड करें' : 'to Download' ?>
                                    </a>
                                <?php endif; ?>
                                <a href="javascript:void(0)" class="btn btn-outline btn-sm share-btn" data-platform="whatsapp"
                                    data-title="<?= clean($eb['title']) ?>">
                                    <?= t('share') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>