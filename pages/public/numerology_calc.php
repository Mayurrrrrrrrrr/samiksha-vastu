<?php
/** Numerology Calculator */
$pageTitle = t('numerology_calc');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('numerology_calc') ?>
    </h1>
    <p>
        <?= t('numerology_calc_desc') ?>
    </p>
</div>
<section class="section">
    <div class="container container-narrow">
        <div class="card" style="padding:var(--space-8);">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">
                        <?= t('enter_name') ?>
                    </label>
                    <input type="text" id="calcName" class="form-control"
                        placeholder="<?= $lang === 'hi' ? 'पूरा नाम' : 'Full Name' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <?= t('enter_dob') ?>
                    </label>
                    <input type="date" id="calcDob" class="form-control">
                </div>
            </div>
            <button class="btn btn-primary btn-full btn-lg" onclick="calculateAll()">
                <?= t('calculate') ?> ✨
            </button>
        </div>

        <div id="calcResults" style="display:none;margin-top:var(--space-8);">
            <div class="grid grid-3" id="numberCards"></div>
            <div id="detailedAnalysis" style="margin-top:var(--space-8);"></div>
        </div>
    </div>
</section>

<script>
    const meanings = {
        1: { en: 'The Leader', hi: 'नेता', desc_en: 'Independent, ambitious, pioneering. Natural born leader with strong determination. Best suited for entrepreneurship and management.', desc_hi: 'स्वतंत्र, महत्वाकांक्षी, अग्रणी। मजबूत दृढ़ संकल्प वाले प्राकृतिक नेता।', color: '#e74c3c', planet_en: 'Sun', planet_hi: 'सूर्य' },
        2: { en: 'The Diplomat', hi: 'राजनयिक', desc_en: 'Sensitive, cooperative, peace-loving. Excel in partnerships and mediation.', desc_hi: 'संवेदनशील, सहयोगी, शांतिप्रिय। साझेदारी और मध्यस्थता में उत्कृष्ट।', color: '#f39c12', planet_en: 'Moon', planet_hi: 'चंद्रमा' },
        3: { en: 'The Communicator', hi: 'संवादक', desc_en: 'Creative, expressive, social. Gift for communication and artistic expression.', desc_hi: 'रचनात्मक, अभिव्यक्त, सामाजिक। संचार और कलात्मक अभिव्यक्ति की प्रतिभा।', color: '#f1c40f', planet_en: 'Jupiter', planet_hi: 'बृहस्पति' },
        4: { en: 'The Builder', hi: 'निर्माता', desc_en: 'Practical, organized, hardworking. Build strong foundations and value stability.', desc_hi: 'व्यावहारिक, संगठित, मेहनती। मजबूत नींव बनाते हैं।', color: '#27ae60', planet_en: 'Rahu', planet_hi: 'राहु' },
        5: { en: 'The Adventurer', hi: 'साहसी', desc_en: 'Freedom-loving, versatile, dynamic. Thrive on change and new experiences.', desc_hi: 'स्वतंत्रता-प्रेमी, बहुमुखी, गतिशील। परिवर्तन पर फलते-फूलते हैं।', color: '#3498db', planet_en: 'Mercury', planet_hi: 'बुध' },
        6: { en: 'The Nurturer', hi: 'पोषक', desc_en: 'Loving, responsible, caring. Drawn to home, family, and community.', desc_hi: 'प्रेमपूर्ण, जिम्मेदार, देखभाल करने वाले।', color: '#9b59b6', planet_en: 'Venus', planet_hi: 'शुक्र' },
        7: { en: 'The Seeker', hi: 'खोजी', desc_en: 'Analytical, spiritual, introspective. Seek deeper truths and inner wisdom.', desc_hi: 'विश्लेषणात्मक, आध्यात्मिक, आत्मनिरीक्षक।', color: '#1abc9c', planet_en: 'Ketu', planet_hi: 'केतु' },
        8: { en: 'The Powerhouse', hi: 'शक्तिशाली', desc_en: 'Ambitious, authoritative, goal-oriented. Strong business acumen.', desc_hi: 'महत्वाकांक्षी, अधिकारी, लक्ष्य-उन्मुख।', color: '#34495e', planet_en: 'Saturn', planet_hi: 'शनि' },
        9: { en: 'The Humanitarian', hi: 'मानवतावादी', desc_en: 'Compassionate, generous, idealistic. Driven to serve humanity.', desc_hi: 'दयालु, उदार, आदर्शवादी। मानवता की सेवा।', color: '#e67e22', planet_en: 'Mars', planet_hi: 'मंगल' }
    };
    const lang = '<?= $lang ?>';

    function reduceToSingle(n) {
        while (n > 9 && n !== 11 && n !== 22 && n !== 33) {
            let s = 0; for (let d of String(n)) s += parseInt(d); n = s;
        }
        return n;
    }

    function calculateAll() {
        const name = document.getElementById('calcName').value.trim();
        const dob = document.getElementById('calcDob').value;
        if (!dob) { showToast(lang === 'hi' ? 'जन्म तिथि आवश्यक' : 'Date of birth required', 'warning'); return; }

        // Life Path
        const digits = dob.replace(/\D/g, '');
        let lpSum = 0; for (let d of digits) lpSum += parseInt(d);
        const lifePath = reduceToSingle(lpSum);

        // Destiny (from name)
        let destinyNum = 0;
        if (name) {
            const pythagorean = { a: 1, b: 2, c: 3, d: 4, e: 5, f: 6, g: 7, h: 8, i: 9, j: 1, k: 2, l: 3, m: 4, n: 5, o: 6, p: 7, q: 8, r: 9, s: 1, t: 2, u: 3, v: 4, w: 5, x: 6, y: 7, z: 8 };
            for (let ch of name.toLowerCase()) if (pythagorean[ch]) destinyNum += pythagorean[ch];
            destinyNum = reduceToSingle(destinyNum);
        }

        // Soul Urge (vowels only)
        let soulNum = 0;
        if (name) {
            const vowels = 'aeiou';
            const pythagorean = { a: 1, e: 5, i: 9, o: 6, u: 3 };
            for (let ch of name.toLowerCase()) if (pythagorean[ch]) soulNum += pythagorean[ch];
            soulNum = reduceToSingle(soulNum);
        }

        const lp = meanings[lifePath > 9 ? lifePath % 10 || 9 : lifePath] || meanings[1];
        const dn = meanings[destinyNum > 9 ? destinyNum % 10 || 9 : destinyNum] || meanings[1];
        const sn = meanings[soulNum > 9 ? soulNum % 10 || 9 : soulNum] || meanings[1];

        const cards = [
            { label: lang === 'hi' ? 'जीवन पथ अंक' : 'Life Path Number', num: lifePath, m: lp },
            { label: lang === 'hi' ? 'भाग्य अंक' : 'Destiny Number', num: destinyNum || '-', m: dn },
            { label: lang === 'hi' ? 'आत्मा अंक' : 'Soul Urge Number', num: soulNum || '-', m: sn }
        ];

        let cardsHtml = '';
        cards.forEach(c => {
            cardsHtml += `<div class="calc-result" style="background:linear-gradient(135deg,${c.m.color}22,var(--bg-card));border:1px solid ${c.m.color}44;">
            <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:var(--space-2);">${c.label}</div>
            <div style="font-size:4rem;font-weight:800;color:${c.m.color};font-family:var(--font-heading);">${c.num}</div>
            <div style="font-size:1.1rem;font-weight:600;color:var(--text-primary);margin-top:var(--space-2);">${lang === 'hi' ? c.m.hi : c.m.en}</div>
            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:var(--space-2);">${lang === 'hi' ? 'ग्रह: ' + c.m.planet_hi : 'Planet: ' + c.m.planet_en}</div>
        </div>`;
        });

        document.getElementById('numberCards').innerHTML = cardsHtml;
        document.getElementById('detailedAnalysis').innerHTML = `
        <div class="card" style="padding:var(--space-8);">
            <h3 style="margin-bottom:var(--space-4);">${lang === 'hi' ? 'विस्तृत विश्लेषण' : 'Detailed Analysis'}</h3>
            <p style="line-height:1.8;color:var(--text-secondary);">${lang === 'hi' ? lp.desc_hi : lp.desc_en}</p>
            <div style="margin-top:var(--space-6);display:flex;gap:var(--space-4);flex-wrap:wrap;">
                <a href="javascript:void(0)" class="btn btn-primary share-btn" data-platform="whatsapp" data-title="${lang === 'hi' ? 'मेरा जीवन पथ अंक ' + lifePath + ' है!' : 'My Life Path Number is ' + lifePath + '!'}">${lang === 'hi' ? 'WhatsApp पर शेयर करें' : 'Share on WhatsApp'} 💬</a>
                <a href="<?= BASE_URL ?>contact" class="btn btn-outline">${lang === 'hi' ? 'विशेषज्ञ परामर्श लें' : 'Get Expert Consultation'} →</a>
            </div>
        </div>
    `;
        document.getElementById('calcResults').style.display = 'block';
        document.getElementById('calcResults').scrollIntoView({ behavior: 'smooth' });
    }
</script>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>