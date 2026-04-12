<?php
/** 404 Page */
$pageTitle = 'Page Not Found';
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header" style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div style="text-align:center;position:relative;">
        <div style="font-size:8rem;margin-bottom:var(--space-4);">🔍</div>
        <h1 style="color:var(--white);">404</h1>
        <p style="color:rgba(255,255,255,0.7);font-size:var(--font-size-xl);margin-bottom:var(--space-8);">
            <?= $lang === 'hi' ? 'पेज नहीं मिला' : 'Page Not Found' ?>
        </p>
        <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg">
            <?= t('nav_home') ?> →
        </a>
    </div>
</div>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>