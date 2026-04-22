<?php
/** Login Page */
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . (isConsultant() ? 'consultant/dashboard' : 'user/dashboard'));
    exit;
}

$pageTitle = $lang === 'hi' ? 'लॉगिन' : 'Client Login';
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="auth-page">
    <div class="auth-card animate-slide-up" style="max-width: 450px;">
        <a href="<?= BASE_URL ?>" class="nav-brand" style="justify-content:center;margin-bottom:var(--space-8);color:var(--text-primary);">
            <div class="nav-logo">वा</div>
            <div class="nav-brand-text"><?= SITE_NAME ?></div>
        </a>
        
        <div class="auth-header">
            <h1><?= $lang === 'hi' ? 'क्लाइंट लॉगिन' : 'Client Login' ?></h1>
            <p><?= $lang === 'hi' ? 'अपनी रिपोर्ट और अपॉइंटमेंट तक पहुंचने के लिए लॉगिन करें।' : 'Access your reports and appointments securely.' ?></p>
        </div>

        <!-- Client Google Login -->
        <a href="<?= BASE_URL ?>api/auth/google_init.php" class="btn btn-outline btn-full btn-lg" style="margin-bottom: var(--space-6); display: flex; align-items: center; justify-content: center; gap: 10px; font-weight: 500;">
            <svg viewBox="0 0 48 48" width="24" height="24">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.7 17.74 9.5 24 9.5z"></path>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
            </svg>
            <?= $lang === 'hi' ? 'Google के साथ लॉगिन करें' : 'Login with Google' ?>
        </a>

        <div style="text-align: center; margin: var(--space-6) 0; border-bottom: 1px solid var(--border-color); line-height: 0.1em;">
            <span style="background: var(--bg-color); padding: 0 10px; color: var(--text-muted); font-size: 0.9em;"><?= $lang === 'hi' ? 'केवल एडमिन के लिए' : 'Admin Area' ?></span>
        </div>

        <!-- Admin Login -->
        <details>
            <summary style="cursor: pointer; color: var(--text-muted); text-align: center; margin-bottom: var(--space-4);">
                <?= $lang === 'hi' ? 'एडमिन लॉगिन दिखाएं' : 'Show Admin Login' ?>
            </summary>
            <form method="POST" action="<?= BASE_URL ?>api/auth/admin_login.php" style="margin-top: var(--space-4); padding: var(--space-4); background: var(--surface); border-radius: 8px;">
                <div class="form-group">
                    <label class="form-label"><?= t('email') ?></label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@samikshavastu.com">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= t('password') ?></label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary btn-full">
                    <?= $lang === 'hi' ? 'एडमिन लॉगिन' : 'Admin Login' ?> →
                </button>
            </form>
        </details>
        
    </div>
</div>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>