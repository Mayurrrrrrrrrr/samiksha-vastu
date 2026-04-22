<?php
$lang = $_SESSION['lang'] ?? 'en';
$currentRoute = trim($_GET['route'] ?? '', '/');
$switchLang = $lang === 'en' ? 'hi' : 'en';

// Build language toggle URL that preserves current page
$_langParams = $_GET;
unset($_langParams['route']);
$_langParams['lang'] = $switchLang;
$langToggleUrl = BASE_URL . $currentRoute . '?' . http_build_query($_langParams);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?? SITE_NAME ?> |
        <?= SITE_NAME ?>
    </title>
    <meta name="description"
        content="<?= $pageDescription ?? ($lang === 'hi' ? META_DESCRIPTION_HI : META_DESCRIPTION) ?>">
    <meta name="keywords" content="<?= $pageKeywords ?? META_KEYWORDS ?>">
    <meta name="author" content="<?= CONSULTANT_NAME ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Social -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $pageTitle ?? SITE_NAME ?>">
    <meta property="og:description"
        content="<?= $pageDescription ?? ($lang === 'hi' ? META_DESCRIPTION_HI : META_DESCRIPTION) ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:locale" content="<?= $lang === 'hi' ? 'hi_IN' : 'en_IN' ?>">
    <meta property="og:image" content="<?= ASSETS_URL ?>images/og-image.jpg">
    <meta property="og:url" content="<?= BASE_URL . $currentRoute ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?? SITE_NAME ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?? META_DESCRIPTION ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= BASE_URL . $currentRoute ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏠</text></svg>">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/main.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/public.css">
    <?php if (isset($extraCSS)): ?>
        <link rel="stylesheet" href="<?= ASSETS_URL ?>css/<?= $extraCSS ?>">
    <?php endif; ?>

    <!-- Hreflang for bilingual content -->
    <link rel="alternate" hreflang="en" href="<?= BASE_URL . $currentRoute ?>?lang=en" />
    <link rel="alternate" hreflang="hi" href="<?= BASE_URL . $currentRoute ?>?lang=hi" />
    <link rel="alternate" hreflang="x-default" href="<?= BASE_URL . $currentRoute ?>" />

    <?php if (isset($extraHeadTags))
        echo $extraHeadTags; ?>

    <!-- Structured Data / JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "<?= SITE_NAME ?>",
        "description": "<?= META_DESCRIPTION ?>",
        "founder": {
            "@type": "Person",
            "name": "<?= CONSULTANT_NAME ?>"
        },
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "IN"
        },
        "url": "<?= rtrim(BASE_URL, '/') ?>",
        "telephone": "<?= SITE_PHONE ?>",
        "email": "<?= SITE_EMAIL ?>",
        "priceRange": "₹1500 - ₹5100",
        "areaServed": "Global (Online) & India"
    }
    </script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a href="<?= BASE_URL ?>" class="nav-brand">
                <div class="nav-logo">वा</div>
                <div class="nav-brand-text">
                    <?= SITE_NAME ?>
                    <small>
                        <?= $lang === 'hi' ? 'वास्तु समीक्षा' : 'Vastu Samiksha' ?>
                    </small>
                </div>
            </a>

            <div class="nav-menu" id="navMenu">
                <a href="<?= BASE_URL ?>"
                    class="nav-link <?= $currentRoute === 'home' || $currentRoute === '' ? 'active' : '' ?>">
                    <?= t('nav_home') ?>
                </a>
                <a href="<?= BASE_URL ?>about" class="nav-link <?= $currentRoute === 'about' ? 'active' : '' ?>">
                    <?= t('nav_about') ?>
                </a>
                <a href="<?= BASE_URL ?>services" class="nav-link <?= $currentRoute === 'services' ? 'active' : '' ?>">
                    <?= t('nav_services') ?>
                </a>
                <a href="<?= BASE_URL ?>blogs" class="nav-link <?= $currentRoute === 'blogs' ? 'active' : '' ?>">
                    <?= t('nav_blogs') ?>
                </a>
                <a href="<?= BASE_URL ?>videos" class="nav-link <?= $currentRoute === 'videos' ? 'active' : '' ?>">
                    <?= t('nav_videos') ?>
                </a>
                <a href="<?= BASE_URL ?>ebooks" class="nav-link <?= $currentRoute === 'ebooks' ? 'active' : '' ?>">
                    <?= t('nav_ebooks') ?>
                </a>
                <a href="<?= BASE_URL ?>games" class="nav-link <?= $currentRoute === 'games' ? 'active' : '' ?>">
                    <?= t('nav_games') ?>
                </a>
                <a href="<?= BASE_URL ?>contact" class="nav-link <?= $currentRoute === 'contact' ? 'active' : '' ?>">
                    <?= t('nav_contact') ?>
                </a>
            </div>

            <div class="nav-actions">
                <a href="<?= $langToggleUrl ?>" class="nav-lang-toggle" title="Switch Language" style="display:flex; align-items:center; gap: 8px; font-weight:600; padding: 6px 12px; border-radius: 20px; background: var(--bg-section); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none;">
                    <span style="<?= $lang === 'en' ? 'color: var(--primary); font-weight:800;' : 'opacity:0.6;' ?>">EN</span>
                    <div style="width: 24px; height: 14px; background: var(--border-color); border-radius: 10px; position: relative;">
                        <div style="width: 10px; height: 10px; background: var(--primary); border-radius: 50%; position: absolute; top: 2px; <?= $lang === 'hi' ? 'right: 2px;' : 'left: 2px;' ?>"></div>
                    </div>
                    <span style="<?= $lang === 'hi' ? 'color: var(--primary); font-weight:800;' : 'opacity:0.6;' ?>">HI</span>
                </a>
                <button class="nav-theme-toggle" id="themeToggle" title="Toggle Dark Mode">🌙</button>
                <?php if (isLoggedIn()): ?>
                    <a href="<?= isConsultant() ? BASE_URL . 'consultant/dashboard' : BASE_URL . 'user/dashboard' ?>"
                        class="btn btn-outline btn-sm">
                        <?= t('nav_dashboard') ?>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login" class="btn btn-outline btn-sm">
                        <?= t('nav_login') ?>
                    </a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>book-appointment" class="btn btn-primary btn-sm">
                    <?= $lang === 'hi' ? 'अपॉइंटमेंट बुक करें' : 'Book Appointment' ?>
                </a>
                <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>
    <div class="nav-menu-overlay" id="navOverlay"></div>

    <!-- Flash Messages -->
    <?php $flash = getFlash();
    if ($flash): ?>
        <div class="flash-message flash-<?= $flash['type'] ?>"
            style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:999;max-width:500px;width:90%;">
            <?= $flash['message'] ?>
        </div>
    <?php endif; ?>

    <main>