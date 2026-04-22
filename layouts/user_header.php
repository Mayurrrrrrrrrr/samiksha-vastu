<?php
/** User Dashboard Layout Header */
requireLogin();
if (isConsultant()) {
    header('Location: ' . BASE_URL . 'consultant/dashboard');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$currentRoute = trim($_GET['route'] ?? '', '/');
$db = getDB();

// Build language toggle URL that preserves current page
$_langParams = $_GET;
unset($_langParams['route']);
$_langParams['lang'] = $lang === 'en' ? 'hi' : 'en';
$langToggleUrl = BASE_URL . $currentRoute . '?' . http_build_query($_langParams);

$unreadChats = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
$unreadChats->execute([currentUserId()]);
$unreadCount = $unreadChats->fetchColumn();

$pendingSubs = $db->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ? AND status != 'completed'");
$pendingSubs->execute([currentUserId()]);
$pendingSubCount = $pendingSubs->fetchColumn();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?? t('user_dashboard') ?> |
        <?= SITE_NAME ?>
    </title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏠</text></svg>">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/public.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/dashboard.css">
</head>

<body>
    <nav class="navbar scrolled" id="navbar">
        <div class="container" style="max-width:100%;padding:0 var(--space-6);">
            <a href="<?= BASE_URL ?>" class="nav-brand" style="color:var(--text-primary);">
                <div class="nav-logo">वा</div>
                <div class="nav-brand-text">
                    <?= SITE_NAME ?>
                </div>
            </a>
            <div class="nav-actions">
                <a href="<?= $langToggleUrl ?>" class="nav-lang-toggle"
                    style="border-color:var(--border-color);color:var(--text-secondary);">
                    <?= t('switch_lang') ?>
                </a>
                <button class="nav-theme-toggle" id="themeToggle"
                    style="border-color:var(--border-color);color:var(--text-secondary);">🌙</button>
                <a href="<?= BASE_URL ?>logout" class="btn btn-outline btn-sm">
                    <?= t('nav_logout') ?>
                </a>
                <button class="nav-toggle" id="sidebarToggle" style="display:none;"
                    onclick="document.querySelector('.sidebar').classList.toggle('open')"><span
                        style="background:var(--text-primary);"></span><span
                        style="background:var(--text-primary);"></span><span
                        style="background:var(--text-primary);"></span></button>
            </div>
        </div>
    </nav>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                    <img src="<?= htmlspecialchars($_SESSION['user_avatar']) ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light);">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?= mb_substr(currentUserName(), 0, 1) ?>
                    </div>
                <?php endif; ?>
                <div class="sidebar-brand-info">
                    <h4>
                        <?= clean(currentUserName()) ?>
                    </h4>
                    <span>
                        <?= $lang === 'hi' ? 'उपयोगकर्ता' : 'User' ?>
                    </span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= BASE_URL ?>user/dashboard"
                    class="sidebar-link <?= $currentRoute === 'user/dashboard' ? 'active' : '' ?>"><span
                        class="link-icon">📊</span>
                    <?= t('user_dashboard') ?>
                </a>
                <a href="<?= BASE_URL ?>user/submit"
                    class="sidebar-link <?= $currentRoute === 'user/submit' ? 'active' : '' ?>"><span
                        class="link-icon">📋</span>
                    <?= t('submission_title') ?>
                </a>
                <a href="<?= BASE_URL ?>user/submissions"
                    class="sidebar-link <?= $currentRoute === 'user/submissions' ? 'active' : '' ?>"><span
                        class="link-icon">📑</span>
                    <?= t('my_submissions') ?>
                    <?php if ($pendingSubCount): ?><span class="badge">
                            <?= $pendingSubCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <div class="sidebar-section">
                    <?= $lang === 'hi' ? 'संवाद' : 'Communication' ?>
                </div>
                <a href="<?= BASE_URL ?>user/ask"
                    class="sidebar-link <?= $currentRoute === 'user/ask' ? 'active' : '' ?>"><span
                        class="link-icon">❓</span>
                    <?= t('ask_question') ?>
                </a>
                <a href="<?= BASE_URL ?>user/questions"
                    class="sidebar-link <?= $currentRoute === 'user/questions' ? 'active' : '' ?>"><span
                        class="link-icon">📝</span>
                    <?= t('my_questions') ?>
                </a>
                <a href="<?= BASE_URL ?>user/chat"
                    class="sidebar-link <?= $currentRoute === 'user/chat' ? 'active' : '' ?>"><span
                        class="link-icon">💬</span>
                    <?= t('nav_chat') ?>
                    <?php if ($unreadCount): ?><span class="badge">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <div class="sidebar-section">
                    <?= $lang === 'hi' ? 'अन्य' : 'Other' ?>
                </div>
                <a href="<?= BASE_URL ?>user/profile"
                    class="sidebar-link <?= $currentRoute === 'user/profile' ? 'active' : '' ?>"><span
                        class="link-icon">👤</span>
                    <?= t('nav_profile') ?>
                </a>
                <a href="<?= BASE_URL ?>" class="sidebar-link"><span class="link-icon">🌐</span>
                    <?= $lang === 'hi' ? 'वेबसाइट पर जाएं' : 'Visit Website' ?>
                </a>
            </nav>
        </aside>
        <div class="dash-content">
            <?php $flash = getFlash();
            if ($flash): ?>
                <div class="flash-message flash-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>