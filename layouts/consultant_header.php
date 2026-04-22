<?php
/** Consultant Dashboard Layout Header */
requireLogin();
if (!isConsultant()) {
    header('Location: ' . BASE_URL . 'user/dashboard');
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

$_cuid = currentUserId();
$_stmt = $db->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
$_stmt->execute([$_cuid]);
$unreadChats = $_stmt->fetchColumn();
$pendingSubs = $db->query("SELECT COUNT(*) FROM submissions WHERE status = 'pending'")->fetchColumn();
$unansweredQ = $db->query("SELECT COUNT(*) FROM questions WHERE is_answered = 0")->fetchColumn();
$newContacts = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?? t('consultant_dashboard') ?> |
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
                    <?= SITE_NAME ?> <small style="font-size:10px;color:var(--primary);">CONSULTANT</small>
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
                <div class="avatar-placeholder">
                    <?= mb_substr(CONSULTANT_NAME, 0, 1) ?>
                </div>
                <div class="sidebar-brand-info">
                    <h4>
                        <?= CONSULTANT_NAME ?>
                    </h4><span style="color:var(--primary);">Consultant ⭐</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= BASE_URL ?>consultant/dashboard"
                    class="sidebar-link <?= $currentRoute === 'consultant/dashboard' ? 'active' : '' ?>"><span
                        class="link-icon">📊</span>
                    <?= t('consultant_dashboard') ?>
                </a>

                <div class="sidebar-section">
                    <?= $lang === 'hi' ? 'CRM' : 'CRM' ?>
                </div>
                <a href="<?= BASE_URL ?>consultant/submissions"
                    class="sidebar-link <?= $currentRoute === 'consultant/submissions' ? 'active' : '' ?>"><span
                        class="link-icon">📋</span>
                    <?= $lang === 'hi' ? 'आवश्यकताएं' : 'Submissions' ?>
                    <?php if ($pendingSubs): ?><span class="badge">
                            <?= $pendingSubs ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/whatsapp"
                    class="sidebar-link <?= $currentRoute === 'consultant/whatsapp' ? 'active' : '' ?>"><span
                        class="link-icon">📱</span>
                    <?= $lang === 'hi' ? 'व्हाट्सएप' : 'WhatsApp Panel' ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/questions"
                    class="sidebar-link <?= $currentRoute === 'consultant/questions' ? 'active' : '' ?>"><span
                        class="link-icon">❓</span>
                    <?= $lang === 'hi' ? 'प्रश्न' : 'Questions' ?>
                    <?php if ($unansweredQ): ?><span class="badge">
                            <?= $unansweredQ ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/chat"
                    class="sidebar-link <?= $currentRoute === 'consultant/chat' ? 'active' : '' ?>"><span
                        class="link-icon">💬</span>
                    <?= t('nav_chat') ?>
                    <?php if ($unreadChats): ?><span class="badge">
                            <?= $unreadChats ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/contacts"
                    class="sidebar-link <?= $currentRoute === 'consultant/contacts' ? 'active' : '' ?>"><span
                        class="link-icon">📧</span>
                    <?= $lang === 'hi' ? 'संपर्क संदेश' : 'Contact Messages' ?>
                    <?php if ($newContacts): ?><span class="badge">
                            <?= $newContacts ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="sidebar-section">
                    <?= $lang === 'hi' ? 'कंटेंट' : 'Content' ?>
                </div>
                <a href="<?= BASE_URL ?>consultant/blogs"
                    class="sidebar-link <?= $currentRoute === 'consultant/blogs' ? 'active' : '' ?>"><span
                        class="link-icon">📰</span>
                    <?= t('nav_blogs') ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/videos"
                    class="sidebar-link <?= $currentRoute === 'consultant/videos' ? 'active' : '' ?>"><span
                        class="link-icon">🎬</span>
                    <?= t('nav_videos') ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/ebooks"
                    class="sidebar-link <?= $currentRoute === 'consultant/ebooks' ? 'active' : '' ?>"><span
                        class="link-icon">📚</span>
                    <?= t('nav_ebooks') ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/quizzes"
                    class="sidebar-link <?= $currentRoute === 'consultant/quizzes' ? 'active' : '' ?>"><span
                        class="link-icon">🧠</span>
                    <?= $lang === 'hi' ? 'प्रश्नोत्तरी' : 'Quizzes' ?>
                </a>
                <a href="<?= BASE_URL ?>consultant/testimonials"
                    class="sidebar-link <?= $currentRoute === 'consultant/testimonials' ? 'active' : '' ?>"><span
                        class="link-icon">⭐</span>
                    <?= $lang === 'hi' ? 'प्रशंसापत्र' : 'Testimonials' ?>
                </a>

                <div class="sidebar-section">
                    <?= $lang === 'hi' ? 'अन्य' : 'Other' ?>
                </div>
                <a href="<?= BASE_URL ?>consultant/users"
                    class="sidebar-link <?= $currentRoute === 'consultant/users' ? 'active' : '' ?>"><span
                        class="link-icon">👥</span>
                    <?= $lang === 'hi' ? 'उपयोगकर्ता' : 'Users' ?>
                </a>
                <a href="<?= BASE_URL ?>" class="sidebar-link"><span class="link-icon">🌐</span>
                    <?= $lang === 'hi' ? 'वेबसाइट' : 'Website' ?>
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