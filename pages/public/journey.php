<?php
/** About Page */
$pageTitle = t('about_title');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('about_title') ?>
    </h1>
    <p>
        <?= t('about_subtitle') ?>
    </p>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> / <span>
            <?= t('nav_about') ?>
        </span></div>
</div>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:var(--space-16);align-items:center;">
            <div class="reveal">
                <div
                    style="width:100%;aspect-ratio:1;background:linear-gradient(135deg,var(--secondary),var(--secondary-light));border-radius:var(--border-radius-xl);display:flex;align-items:center;justify-content:center;font-size:8rem;position:relative;overflow:hidden;">
                    🙏
                    <div
                        style="position:absolute;inset:0;border:3px solid var(--primary);border-radius:var(--border-radius-xl);opacity:0.3;">
                    </div>
                </div>
            </div>
            <div class="reveal">
                <h2 style="margin-bottom:var(--space-6);">
                    <?= $lang === 'hi' ? CONSULTANT_NAME_HI : CONSULTANT_NAME ?>
                </h2>
                <p
                    style="font-size:var(--font-size-lg);line-height:1.8;color:var(--text-secondary);margin-bottom:var(--space-8);">
                    <?= t('about_bio') ?>
                </p>

                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-4);">
                    <div
                        style="text-align:center;padding:var(--space-4);background:var(--bg-section);border-radius:var(--border-radius);border:1px solid var(--border-color);">
                        <div
                            style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);font-family:var(--font-heading);">
                            10+</div>
                        <div style="font-size:var(--font-size-sm);color:var(--text-muted);">
                            <?= t('about_experience') ?>
                        </div>
                    </div>
                    <div
                        style="text-align:center;padding:var(--space-4);background:var(--bg-section);border-radius:var(--border-radius);border:1px solid var(--border-color);">
                        <div
                            style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);font-family:var(--font-heading);">
                            2000+</div>
                        <div style="font-size:var(--font-size-sm);color:var(--text-muted);">
                            <?= t('about_clients') ?>
                        </div>
                    </div>
                    <div
                        style="text-align:center;padding:var(--space-4);background:var(--bg-section);border-radius:var(--border-radius);border:1px solid var(--border-color);">
                        <div
                            style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);font-family:var(--font-heading);">
                            5000+</div>
                        <div style="font-size:var(--font-size-sm);color:var(--text-muted);">
                            <?= t('about_consultations') ?>
                        </div>
                    </div>
                    <div
                        style="text-align:center;padding:var(--space-4);background:var(--bg-section);border-radius:var(--border-radius);border:1px solid var(--border-color);">
                        <div
                            style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);font-family:var(--font-heading);">
                            50+</div>
                        <div style="font-size:var(--font-size-sm);color:var(--text-muted);">
                            <?= t('about_cities') ?>
                        </div>
                    </div>
                </div>

                <div style="margin-top:var(--space-8);display:flex;gap:var(--space-4);">
                    <a href="<?= BASE_URL ?>services" class="btn btn-primary">
                        <?= t('hero_cta') ?>
                    </a>
                    <a href="<?= BASE_URL ?>contact" class="btn btn-outline">
                        <?= t('contact_title') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications / Expertise -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= $lang === 'hi' ? 'विशेषज्ञता के क्षेत्र' : 'Areas of Expertise' ?>
            </h2>
        </div>
        <div class="grid grid-4">
            <?php
            $expertise = [
                ['icon' => '🏠', 'en' => 'Residential Vastu', 'hi' => 'आवासीय वास्तु'],
                ['icon' => '🏢', 'en' => 'Commercial Vastu', 'hi' => 'कमर्शियल वास्तु'],
                ['icon' => '🏗️', 'en' => 'Industrial Vastu', 'hi' => 'औद्योगिक वास्तु'],
                ['icon' => '🔢', 'en' => 'Numerology', 'hi' => 'अंक ज्योतिष'],
                ['icon' => '📝', 'en' => 'Name Correction', 'hi' => 'नाम सुधार'],
                ['icon' => '💎', 'en' => 'Gemstone Advisory', 'hi' => 'रत्न सलाह'],
                ['icon' => '🎨', 'en' => 'Color Therapy', 'hi' => 'रंग चिकित्सा'],
                ['icon' => '🧘', 'en' => 'Energy Healing', 'hi' => 'ऊर्जा उपचार'],
            ];
            foreach ($expertise as $exp): ?>
                <div class="service-card reveal" style="padding:var(--space-6);">
                    <div style="font-size:2.5rem;margin-bottom:var(--space-3);">
                        <?= $exp['icon'] ?>
                    </div>
                    <h4>
                        <?= $lang === 'hi' ? $exp['hi'] : $exp['en'] ?>
                    </h4>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>
            <?= t('section_cta') ?>
        </h2>
        <p>
            <?= t('section_cta_sub') ?>
        </p>
        <a href="<?= BASE_URL ?>contact" class="btn btn-secondary btn-lg">
            <?= t('contact_title') ?> →
        </a>
    </div>
</section>

<style>
    @media (max-width: 768px) {
        .section>.container>div:first-child {
            grid-template-columns: 1fr !important;
        }

        .section>.container>div:first-child>div:first-child {
            max-width: 300px;
            margin: 0 auto;
        }
    }
</style>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>