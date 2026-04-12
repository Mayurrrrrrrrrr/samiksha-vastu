<?php
/** Register Page */
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'user/dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request');
        header('Location: ' . BASE_URL . 'register');
        exit;
    }
    if ($_POST['password'] !== $_POST['confirm_password']) {
        setFlash('error', 'Passwords do not match');
        header('Location: ' . BASE_URL . 'register');
        exit;
    }
    if (strlen($_POST['password']) < 6) {
        setFlash('error', 'Password must be at least 6 characters');
        header('Location: ' . BASE_URL . 'register');
        exit;
    }

    $result = registerUser($_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'] ?? '', $_POST['dob'] ?? null, $_POST['gender'] ?? '');
    if ($result['success']) {
        header('Location: ' . BASE_URL . 'user/dashboard');
        exit;
    }
    setFlash('error', $result['message']);
}

$pageTitle = t('register_title');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="auth-page">
    <div class="auth-card animate-slide-up" style="max-width:520px;">
        <a href="<?= BASE_URL ?>" class="nav-brand"
            style="justify-content:center;margin-bottom:var(--space-8);color:var(--text-primary);">
            <div class="nav-logo">वा</div>
            <div class="nav-brand-text">
                <?= SITE_NAME ?>
            </div>
        </a>
        <div class="auth-header">
            <h1>
                <?= t('register_title') ?>
            </h1>
            <p>
                <?= t('register_subtitle') ?>
            </p>
        </div>
        <form method="POST">
            <?= csrfField() ?>
            <div class="form-group">
                <label class="form-label">
                    <?= t('full_name') ?> *
                </label>
                <input type="text" name="name" class="form-control" required value="<?= clean($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">
                    <?= t('email') ?> *
                </label>
                <input type="email" name="email" class="form-control" required
                    value="<?= clean($_POST['email'] ?? '') ?>">
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">
                        <?= t('phone') ?>
                    </label>
                    <input type="tel" name="phone" class="form-control" value="<?= clean($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <?= t('dob') ?>
                    </label>
                    <input type="date" name="dob" class="form-control" value="<?= clean($_POST['dob'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <?= t('gender') ?>
                </label>
                <select name="gender" class="form-control">
                    <option value="">--</option>
                    <option value="male">
                        <?= t('male') ?>
                    </option>
                    <option value="female">
                        <?= t('female') ?>
                    </option>
                    <option value="other">
                        <?= t('other') ?>
                    </option>
                </select>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">
                        <?= t('password') ?> *
                    </label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <?= t('confirm_password') ?> *
                    </label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full btn-lg">
                <?= t('nav_register') ?> →
            </button>
        </form>
        <div class="auth-footer">
            <p>
                <?= t('has_account') ?> <a href="<?= BASE_URL ?>login">
                    <?= t('nav_login') ?>
                </a>
            </p>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>