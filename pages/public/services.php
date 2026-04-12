<?php
/** Services Page */
$pageTitle = t('nav_services');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('section_services') ?>
    </h1>
    <p>
        <?= t('section_services_sub') ?>
    </p>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> / <span>
            <?= t('nav_services') ?>
        </span></div>
</div>

<section class="section">
    <div class="container">
        <?php
        $services = [
            [
                'icon' => '🏠',
                'title' => t('service_vastu_home'),
                'desc' => t('service_vastu_home_desc'),
                'details' => $lang === 'hi' ? 'घर के हर कमरे, दिशा और तत्व का विस्तृत विश्लेषण। उत्तर-पूर्व, दक्षिण-पश्चिम, रसोई, बेडरूम, बाथरूम, पूजा घर — सभी का वास्तु अनुपालन जांच। सुधारात्मक उपाय बिना तोड़-फोड़ के।' : 'Detailed analysis of every room, direction, and element in your home. North-East, South-West, kitchen, bedroom, bathroom, prayer room — complete Vastu compliance check. Corrective remedies without demolition.'
            ],
            [
                'icon' => '🏢',
                'title' => t('service_vastu_office'),
                'desc' => t('service_vastu_office_desc'),
                'details' => $lang === 'hi' ? 'बॉस केबिन, रिसेप्शन, कर्मचारी बैठक, कॉन्फ्रेंस रूम, कैश काउंटर — सभी का वास्तु अनुकूलन। व्यापार वृद्धि और कर्मचारी उत्पादकता में सुधार।' : 'Boss cabin, reception, employee seating, conference room, cash counter — Vastu optimization for all. Improve business growth and employee productivity.'
            ],
            [
                'icon' => '🔢',
                'title' => t('service_numerology'),
                'desc' => t('service_numerology_desc'),
                'details' => $lang === 'hi' ? 'जीवन पथ अंक, भाग्य अंक, आत्मा अंक, व्यक्तित्व अंक — सम्पूर्ण अंक ज्योतिष विश्लेषण। मोबाइल नंबर, कार नंबर, घर नंबर का विश्लेषण।' : 'Life Path, Destiny, Soul Urge, Personality numbers — complete numerology analysis. Mobile number, car number, house number analysis.'
            ],
            [
                'icon' => '🗺️',
                'title' => t('service_vastu_plot'),
                'desc' => t('service_vastu_plot_desc'),
                'details' => $lang === 'hi' ? 'प्लॉट का आकार, ढलान, सड़क की स्थिति, पड़ोसी भवन — सभी का वास्तु विश्लेषण। नई जमीन खरीदने से पहले विशेषज्ञ मार्गदर्शन।' : 'Plot shape, slope, road position, neighboring buildings — complete Vastu analysis. Expert guidance before buying new land.'
            ],
            [
                'icon' => '✨',
                'title' => t('service_remedies'),
                'desc' => t('service_remedies_desc'),
                'details' => $lang === 'hi' ? 'नमक, पिरामिड, क्रिस्टल, रंग, दर्पण, पौधे — सरल और प्रभावी वास्तु उपाय। बिना तोड़-फोड़ के दोष निवारण।' : 'Salt, pyramids, crystals, colors, mirrors, plants — simple and effective Vastu remedies. Dosha correction without demolition.'
            ],
            [
                'icon' => '📝',
                'title' => t('service_name_correction'),
                'desc' => t('service_name_correction_desc'),
                'details' => $lang === 'hi' ? 'अंक ज्योतिष के आधार पर नाम में अक्षर जोड़ना या बदलना। व्यवसाय नाम, ब्रांड नाम, बच्चों के नाम — सभी का विश्लेषण।' : 'Adding or changing letters in name based on numerology. Business names, brand names, baby names — complete analysis.'
            ],
        ];
        foreach ($services as $i => $svc): ?>
            <div class="reveal"
                style="display:grid;grid-template-columns:<?= $i % 2 === 0 ? '1fr 1.5fr' : '1.5fr 1fr' ?>;gap:var(--space-12);align-items:center;margin-bottom:var(--space-16);<?= $i % 2 !== 0 ? 'direction:rtl;' : '' ?>">
                <div style="<?= $i % 2 !== 0 ? 'direction:ltr;' : '' ?>">
                    <div
                        style="width:100%;aspect-ratio:1.2;background:linear-gradient(135deg,var(--bg-section),var(--bg-card));border-radius:var(--border-radius-xl);display:flex;align-items:center;justify-content:center;font-size:6rem;border:1px solid var(--border-color);">
                        <?= $svc['icon'] ?>
                    </div>
                </div>
                <div style="<?= $i % 2 !== 0 ? 'direction:ltr;' : '' ?>">
                    <h3 style="font-size:var(--font-size-2xl);margin-bottom:var(--space-4);">
                        <?= $svc['title'] ?>
                    </h3>
                    <p style="font-size:var(--font-size-lg);color:var(--text-secondary);margin-bottom:var(--space-4);">
                        <?= $svc['desc'] ?>
                    </p>
                    <p style="color:var(--text-muted);line-height:1.8;margin-bottom:var(--space-6);">
                        <?= $svc['details'] ?>
                    </p>
                    <a href="<?= BASE_URL ?>contact" class="btn btn-primary">
                        <?= t('hero_cta') ?> →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Pricing hint -->
<section class="section section-alt">
    <div class="container text-center">
        <div class="section-header reveal">
            <h2>
                <?= $lang === 'hi' ? 'परामर्श शुल्क' : 'Consultation Fees' ?>
            </h2>
            <p>
                <?= $lang === 'hi' ? 'पारदर्शी और किफायती मूल्य' : 'Transparent and affordable pricing' ?>
            </p>
        </div>
        <div class="grid grid-3">
            <div class="card reveal" style="padding:var(--space-8);text-align:center;">
                <div style="font-size:2.5rem;margin-bottom:var(--space-4);">🏠</div>
                <h3>
                    <?= $lang === 'hi' ? 'आवासीय वास्तु' : 'Residential Vastu' ?>
                </h3>
                <div
                    style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);margin:var(--space-4) 0;">
                    ₹2,100</div>
                <p class="text-muted text-sm">
                    <?= $lang === 'hi' ? 'ऑनलाइन परामर्श से शुरू' : 'Starting from online consultation' ?>
                </p>
                <a href="<?= BASE_URL ?>contact" class="btn btn-outline btn-full mt-4">
                    <?= t('hero_cta') ?>
                </a>
            </div>
            <div class="card reveal"
                style="padding:var(--space-8);text-align:center;border-color:var(--primary);transform:scale(1.05);">
                <div class="badge badge-primary" style="margin-bottom:var(--space-4);">
                    <?= $lang === 'hi' ? 'लोकप्रिय' : 'Popular' ?>
                </div>
                <div style="font-size:2.5rem;margin-bottom:var(--space-4);">🔢</div>
                <h3>
                    <?= $lang === 'hi' ? 'अंक ज्योतिष रीडिंग' : 'Numerology Reading' ?>
                </h3>
                <div
                    style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);margin:var(--space-4) 0;">
                    ₹1,100</div>
                <p class="text-muted text-sm">
                    <?= $lang === 'hi' ? 'सम्पूर्ण अंक विश्लेषण' : 'Complete number analysis' ?>
                </p>
                <a href="<?= BASE_URL ?>contact" class="btn btn-primary btn-full mt-4">
                    <?= t('hero_cta') ?>
                </a>
            </div>
            <div class="card reveal" style="padding:var(--space-8);text-align:center;">
                <div style="font-size:2.5rem;margin-bottom:var(--space-4);">🏢</div>
                <h3>
                    <?= $lang === 'hi' ? 'कमर्शियल वास्तु' : 'Commercial Vastu' ?>
                </h3>
                <div
                    style="font-size:var(--font-size-3xl);font-weight:800;color:var(--primary);margin:var(--space-4) 0;">
                    ₹5,100</div>
                <p class="text-muted text-sm">
                    <?= $lang === 'hi' ? 'ऑफिस/दुकान विश्लेषण' : 'Office/Shop analysis' ?>
                </p>
                <a href="<?= BASE_URL ?>contact" class="btn btn-outline btn-full mt-4">
                    <?= t('hero_cta') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>
            <?= t('section_cta') ?>
        </h2>
        <p>
            <?= t('section_cta_sub') ?>
        </p>
        <div style="display:flex;gap:var(--space-4);justify-content:center;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>contact" class="btn btn-secondary btn-lg">
                <?= t('contact_title') ?>
            </a>
            <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" class="btn btn-outline-light btn-lg">💬 WhatsApp</a>
        </div>
    </div>
</section>

<style>
    @media(max-width:768px) {
        .section>div>div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            direction: ltr !important;
        }
    }
</style>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>