<?php
/**
 * Packages & Pricing Page
 */
$pageTitle = $lang === 'hi' ? 'पैकेज और मूल्य' : 'Packages & Pricing';
$pageDescription = $lang === 'hi' ? 'वास्तु और अंक ज्योतिष के लिए हमारे परामर्श पैकेज देखें।' : 'Explore our consultation packages for Vastu & Numerology.';

require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header">
    <h1><?= $pageTitle ?></h1>
    <p><?= $lang === 'hi' ? 'अपनी आवश्यकताओं के अनुसार सही पैकेज चुनें' : 'Choose the right package tailored to your needs' ?></p>
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a> / <span><?= $pageTitle ?></span>
    </div>
</div>

<section class="section">
    <div class="container container-narrow reveal">
        <div class="grid grid-3" style="gap: var(--space-6); align-items: stretch;">
            
            <!-- Starter Package -->
            <div class="card" style="padding: var(--space-6); display: flex; flex-direction: column; border-top: 4px solid var(--border-color);">
                <div style="text-align: center; margin-bottom: var(--space-6);">
                    <h3 style="margin-bottom: var(--space-2); color: var(--text-secondary);"><?= $lang === 'hi' ? 'स्टार्टर' : 'Starter' ?></h3>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: var(--space-2);">₹1,100</div>
                    <p class="text-sm text-muted"><?= $lang === 'hi' ? 'अंक ज्योतिष नाम विश्लेषण' : 'Numerology Name Check' ?></p>
                </div>
                <ul style="list-style: none; padding: 0; margin-bottom: var(--space-8); flex-grow: 1;">
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'मूल नाम संख्या गणना' : 'Basic Name Number Calculation' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'भाग्यशाली ग्रह और रंग' : 'Lucky Planets & Colors' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'संक्षिप्त पीडीएफ रिपोर्ट' : 'Short PDF Report' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px; color: var(--text-muted);">❌ <span><?= $lang === 'hi' ? 'नाम सुधार सुझाव' : 'Name Correction Suggestions' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px; color: var(--text-muted);">❌ <span><?= $lang === 'hi' ? 'कोई व्यक्तिगत कॉल नहीं' : 'No Personal Call' ?></span></li>
                </ul>
                <a href="whatsapp://send?phone=<?= str_replace(['+', ' ', '-'], '', SITE_PHONE) ?>&text=Hi, I am interested in the Starter Package (₹1,100)." class="btn btn-outline btn-full" style="color: #25D366; border-color: #25D366;">
                    <?= $lang === 'hi' ? 'व्हाट्सएप पर पूछें' : 'Enquire on WhatsApp' ?> 💬
                </a>
            </div>

            <!-- Standard Package -->
            <div class="card" style="padding: var(--space-6); display: flex; flex-direction: column; border-top: 4px solid var(--primary); transform: scale(1.05); box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative; z-index: 1;">
                <div style="position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; letter-spacing: 1px;">
                    <?= $lang === 'hi' ? 'सबसे लोकप्रिय' : 'MOST POPULAR' ?>
                </div>
                <div style="text-align: center; margin-bottom: var(--space-6);">
                    <h3 style="margin-bottom: var(--space-2); color: var(--primary);"><?= $lang === 'hi' ? 'स्टैंडर्ड' : 'Standard' ?></h3>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: var(--space-2);">₹5,100</div>
                    <p class="text-sm text-muted"><?= $lang === 'hi' ? 'ऑनलाइन वास्तु परामर्श' : 'Online Vastu Consultation' ?></p>
                </div>
                <ul style="list-style: none; padding: 0; margin-bottom: var(--space-8); flex-grow: 1;">
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'विस्तृत फ्लोर प्लान विश्लेषण' : 'Detailed Floor Plan Analysis' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'दिशाओं में दोष की पहचान' : 'Identification of Directional Doshas' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'बिना तोड़-फोड़ के उपाय (कलर, पिरामिड)' : 'Zero-Demolition Remedies (Color, Metal)' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? '45 मिनट की वीडियो कॉल' : '45-Min Video Call Session' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px; color: var(--text-muted);">❌ <span><?= $lang === 'hi' ? 'फॉलो-अप सपोर्ट' : 'Follow-up Support' ?></span></li>
                </ul>
                <a href="whatsapp://send?phone=<?= str_replace(['+', ' ', '-'], '', SITE_PHONE) ?>&text=Hi, I am interested in the Standard Package (₹5,100)." class="btn btn-primary btn-full">
                    <?= $lang === 'hi' ? 'व्हाट्सएप पर पूछें' : 'Enquire on WhatsApp' ?> 💬
                </a>
            </div>

            <!-- Premium Package -->
            <div class="card" style="padding: var(--space-6); display: flex; flex-direction: column; border-top: 4px solid var(--accent-orange);">
                <div style="text-align: center; margin-bottom: var(--space-6);">
                    <h3 style="margin-bottom: var(--space-2); color: var(--accent-orange);"><?= $lang === 'hi' ? 'प्रीमियम' : 'Premium' ?></h3>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: var(--space-2);">₹11,000</div>
                    <p class="text-sm text-muted"><?= $lang === 'hi' ? 'वास्तु + अंक ज्योतिष + सपोर्ट' : 'Vastu + Numerology + Support' ?></p>
                </div>
                <ul style="list-style: none; padding: 0; margin-bottom: var(--space-8); flex-grow: 1;">
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'पूर्ण वास्तु स्कैन और रिपोर्ट' : 'Full Vastu Scan & PDF Report' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'अंक ज्योतिष विश्लेषण और नाम सुधार' : 'Numerology Analysis & Name Correction' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? '60 मिनट की विस्तृत वीडियो कॉल' : '60-Min In-Depth Video Call' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><?= $lang === 'hi' ? 'मुहूर्त और शुभ तिथियां' : 'Auspicious Dates (Muhurat) Selection' ?></span></li>
                    <li style="margin-bottom: var(--space-3); display: flex; gap: 8px;">✅ <span><strong><?= $lang === 'hi' ? '1 महीने का फ्री फॉलो-अप' : '1 Month Free Follow-up Chat' ?></strong></span></li>
                </ul>
                <a href="whatsapp://send?phone=<?= str_replace(['+', ' ', '-'], '', SITE_PHONE) ?>&text=Hi, I am interested in the Premium Package (₹11,000)." class="btn btn-outline btn-full" style="color: var(--accent-orange); border-color: var(--accent-orange);">
                    <?= $lang === 'hi' ? 'व्हाट्सएप पर पूछें' : 'Enquire on WhatsApp' ?> 💬
                </a>
            </div>

        </div>
        
        <!-- Bottom Note -->
        <div class="text-center mt-12 text-muted">
            <p><small><?= $lang === 'hi' ? 'नोट: कीमतें केवल सांकेतिक हैं और संपत्ति के आकार (स्क्वायर फीट) के आधार पर भिन्न हो सकती हैं।' : 'Note: Prices are indicative and may vary based on property size (sq. ft) and special requirements.' ?></small></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
