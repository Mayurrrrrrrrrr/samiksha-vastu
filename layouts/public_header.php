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
    <nav class="navbar" id="navbar" style="border-bottom: 1px solid var(--border-color); background: rgba(253, 248, 240, 0.95); backdrop-filter: blur(8px); position: sticky; top: 0; z-index: 1000;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-4) var(--space-6);">
            <!-- Brand Logo -->
            <a href="<?= BASE_URL ?>" class="nav-brand" style="display: flex; flex-direction: column; align-items: center; text-decoration: none;">
                <div class="brand-devanagari" style="font-size: 28px; line-height: 1; color: var(--primary);">समिक्षा</div>
                <div style="font-family: var(--font-body); font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-primary); margin-top: 2px;">SAMIKSHA</div>
                <div style="font-family: var(--font-body); font-size: 11px; letter-spacing: 1px; color: var(--accent); margin-top: 1px;">Vastu & Numerology</div>
            </a>

            <!-- Desktop Nav Menu -->
            <div class="nav-menu" id="navMenu" style="display: flex; gap: var(--space-6); align-items: center;">
                <a href="<?= BASE_URL ?>" class="nav-link <?= $currentRoute === 'home' || $currentRoute === '' ? 'active' : '' ?>">Home</a>
                <a href="<?= BASE_URL ?>journey" class="nav-link <?= $currentRoute === 'journey' ? 'active' : '' ?>">My Journey</a>
                <a href="<?= BASE_URL ?>packages" class="nav-link <?= $currentRoute === 'packages' ? 'active' : '' ?>">Services</a>
                <a href="<?= BASE_URL ?>game" class="nav-link <?= $currentRoute === 'game' ? 'active' : '' ?>">Vastu Quiz</a>
            </div>

            <!-- Nav Actions -->
            <div class="nav-actions" style="display: flex; gap: var(--space-4); align-items: center;">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= isConsultant() ? BASE_URL . 'consultant/dashboard' : BASE_URL . 'user/dashboard' ?>" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--text-primary); font-weight: 500;">
                        <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user_avatar']) ?>" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light);">
                        <?php else: ?>
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                <?= mb_substr($_SESSION['user_name'] ?? 'U', 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <span class="hide-mobile">Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login" class="btn btn-outline btn-sm" style="border-radius: var(--border-radius-full);">Client Login</a>
                <?php endif; ?>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" style="background: none; border: none; cursor: pointer; color: var(--primary);">
                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
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