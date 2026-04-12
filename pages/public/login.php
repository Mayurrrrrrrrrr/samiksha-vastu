<?php
/** Login Page */
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . (isConsultant() ? 'consultant/dashboard' : 'user/dashboard'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request');
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    $result = loginUser($_POST['email'] ?? '', $_POST['password'] ?? '');
    if ($result['success']) {
        header('Location: ' . BASE_URL . ($result['role'] === 'consultant' ? 'consultant/dashboard' : 'user/dashboard'));
        exit;
    }
    setFlash('error', $result['message']);
}

$pageTitle = t('login_title');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="auth-page">
    <div class="auth-card animate-slide-up">
        <a href="<?= BASE_URL ?>" class="nav-brand"
            style="justify-content:center;margin-bottom:var(--space-8);color:var(--text-primary);">
            <div class="nav-logo">वा</div>
            <div class="nav-brand-text">
                <?= SITE_NAME ?>
            </div>
        </a>
        <div class="auth-header">
            <h1>
                <?= t('login_title') ?>
            </h1>
            <p>
                <?= t('login_subtitle') ?>
            </p>
        </div>
        <form method="POST">
            <?= csrfField() ?>
            <div class="form-group">
                <label class="form-label">
                    <?= t('email') ?>
                </label>
                <input type="email" name="email" class="form-control" required placeholder="you@example.com"
                    value="<?= clean($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">
                    <?= t('password') ?>
                </label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-full btn-lg">
                <?= t('nav_login') ?> →
            </button>
        </form>
        <div class="auth-footer">
            <p>
                <?= t('no_account') ?> <a href="<?= BASE_URL ?>register">
                    <?= t('nav_register') ?>
                </a>
            </p>
        </div>
    </div>
    <?php require __DIR__ . '/../../layouts/public_footer.php'; ?>