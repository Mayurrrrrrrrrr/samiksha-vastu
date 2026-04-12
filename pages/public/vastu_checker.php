<?php
/** Vastu Direction Checker */
$pageTitle = t('vastu_checker');
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('vastu_checker') ?>
    </h1>
    <p>
        <?= t('vastu_checker_desc') ?>
    </p>
</div>
<section class="section">
    <div class="container container-narrow">
        <div class="card" style="padding:var(--space-8);text-align:center;">
            <!-- Compass -->
            <div style="width:300px;height:300px;margin:0 auto var(--space-8);position:relative;">
                <div
                    style="width:100%;height:100%;border-radius:50%;border:3px solid var(--border-color);position:relative;background:radial-gradient(circle,var(--bg-section),var(--bg-card));">
                    <?php
                    $dirs = [
                        ['N', 0, 'north'],
                        ['NE', 45, 'northeast'],
                        ['E', 90, 'east'],
                        ['SE', 135, 'southeast'],
                        ['S', 180, 'south'],
                        ['SW', 225, 'southwest'],
                        ['W', 270, 'west'],
                        ['NW', 315, 'northwest']
                    ];
                    foreach ($dirs as $d):
                        $rad = deg2rad($d[1] - 90);
                        $x = 50 + 40 * cos($rad);
                        $y = 50 + 40 * sin($rad);
                        ?>
                        <button onclick="checkDirection('<?= $d[2] ?>')"
                            style="position:absolute;left:<?= $x ?>%;top:<?= $y ?>%;transform:translate(-50%,-50%);width:48px;height:48px;border-radius:50%;border:2px solid var(--primary);background:var(--bg-card);cursor:pointer;font-weight:700;font-size:0.8rem;color:var(--primary);transition:all 0.3s;"
                            class="dir-btn" id="dir-<?= $d[2] ?>">
                            <?= $d[0] ?>
                        </button>
                    <?php endforeach; ?>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:2rem;">🧭
                    </div>
                </div>
            </div>
            <p class="text-muted">
                <?= $lang === 'hi' ? 'दिशा पर क्लिक करके वास्तु जानकारी देखें' : 'Click a direction to see Vastu information' ?>
            </p>
        </div>

        <div id="dirResult" style="display:none;margin-top:var(--space-8);"></div>
    </div>
</section>
<script>
    const vastuData = {
        north: {
            element_en: 'Water', element_hi: 'जल', lord_en: 'Kubera (God of Wealth)', lord_hi: 'कुबेर (धन के देवता)', color: '#3498db',
            good_en: ['Career growth', 'Financial prosperity', 'New opportunities', 'Water features', 'Blue/green decor'],
            good_hi: ['करियर विकास', 'वित्तीय समृद्धि', 'नए अवसर', 'जल सुविधाएं', 'नीला/हरा सजावट'],
            avoid_en: ['Toilets', 'Red colors', 'Fire elements', 'Heavy storage'],
            avoid_hi: ['शौचालय', 'लाल रंग', 'अग्नि तत्व', 'भारी भंडारण'],
            tip_en: 'Keep this area clean and clutter-free. Place a money plant or aquarium here for prosperity.',
            tip_hi: 'इस क्षेत्र को साफ और अव्यवस्था-मुक्त रखें। समृद्धि के लिए यहां मनी प्लांट या एक्वेरियम रखें।'
        },
        northeast: {
            element_en: 'Water + Earth', element_hi: 'जल + पृथ्वी', lord_en: 'Shiva (Ishaan)', lord_hi: 'शिव (ईशान)', color: '#1abc9c',
            good_en: ['Prayer/Pooja room', 'Meditation', 'Water storage', 'Open space', 'Light colors'],
            good_hi: ['पूजा कक्ष', 'ध्यान', 'जल भंडारण', 'खुली जगह', 'हल्के रंग'],
            avoid_en: ['Toilets', 'Kitchen', 'Heavy objects', 'Dark colors', 'Staircases'],
            avoid_hi: ['शौचालय', 'रसोई', 'भारी वस्तुएं', 'गहरे रंग', 'सीढ़ियां'],
            tip_en: 'The most sacred direction. Keep it elevated and bright. Ideal for the entrance and prayer room.',
            tip_hi: 'सबसे पवित्र दिशा। इसे ऊंचा और उज्ज्वल रखें। प्रवेश और पूजा कक्ष के लिए आदर्श।'
        },
        east: {
            element_en: 'Air', element_hi: 'वायु', lord_en: 'Indra (King of Gods)', lord_hi: 'इंद्र (देवताओं के राजा)', color: '#f39c12',
            good_en: ['Main entrance', 'Living room', 'Windows', 'Social interactions', 'Morning sunlight'],
            good_hi: ['मुख्य प्रवेश', 'लिविंग रूम', 'खिड़कियां', 'सामाजिक बातचीत', 'सुबह की धूप'],
            avoid_en: ['Toilets', 'Staircases blocking light', 'Heavy walls'],
            avoid_hi: ['शौचालय', 'प्रकाश रोकने वाली सीढ़ियां', 'भारी दीवारें'],
            tip_en: 'Allow maximum sunlight from the East. Keep large windows and ensure good ventilation.',
            tip_hi: 'पूर्व से अधिकतम सूर्य का प्रकाश आने दें। बड़ी खिड़कियां रखें।'
        },
        southeast: {
            element_en: 'Fire', element_hi: 'अग्नि', lord_en: 'Agni (God of Fire)', lord_hi: 'अग्नि देव', color: '#e74c3c',
            good_en: ['Kitchen', 'Electrical equipment', 'Generator', 'Fire-related items', 'Red/orange decor'],
            good_hi: ['रसोई', 'बिजली उपकरण', 'जनरेटर', 'अग्नि संबंधित वस्तुएं', 'लाल/नारंगी सजावट'],
            avoid_en: ['Water storage', 'Bedroom', 'Toilet', 'Blue/black colors'],
            avoid_hi: ['जल भंडारण', 'बेडरूम', 'शौचालय', 'नीला/काला रंग'],
            tip_en: 'Place your kitchen here with the cook facing East. Keep fire-related items in this zone.',
            tip_hi: 'रसोई यहां रखें, रसोइया पूर्व की ओर मुख करके खाना बनाए।'
        },
        south: {
            element_en: 'Earth', element_hi: 'पृथ्वी', lord_en: 'Yama (God of Death/Dharma)', lord_hi: 'यम (धर्म के देवता)', color: '#e67e22',
            good_en: ['Storage', 'Heavy items', 'Master bedroom', 'Fame/reputation area'],
            good_hi: ['भंडारण', 'भारी वस्तुएं', 'मास्टर बेडरूम', 'प्रसिद्धि क्षेत्र'],
            avoid_en: ['Main entrance', 'Water elements', 'Open spaces'],
            avoid_hi: ['मुख्य प्रवेश', 'जल तत्व', 'खुली जगह'],
            tip_en: 'Keep this area heavier than the North. Tall structures and heavy furniture work well here.',
            tip_hi: 'इस क्षेत्र को उत्तर से भारी रखें। ऊंचे ढांचे और भारी फर्नीचर यहां अच्छे काम करते हैं।'
        },
        southwest: {
            element_en: 'Earth', element_hi: 'पृथ्वी', lord_en: 'Nairuti (Demon Lord)', lord_hi: 'नैऋति', color: '#8e44ad',
            good_en: ['Master bedroom', 'Owner cabin', 'Heavy storage', 'Tall structures'],
            good_hi: ['मास्टर बेडरूम', 'मालिक का कमरा', 'भारी भंडारण', 'ऊंचे ढांचे'],
            avoid_en: ['Toilets', 'Water boring', 'Basements', 'Empty spaces'],
            avoid_hi: ['शौचालय', 'बोरिंग', 'तहखाना', 'खाली जगह'],
            tip_en: 'The heaviest corner. Owner of the house should sleep here for stability and authority.',
            tip_hi: 'सबसे भारी कोना। घर के मालिक को स्थिरता के लिए यहां सोना चाहिए।'
        },
        west: {
            element_en: 'Air/Space', element_hi: 'वायु/आकाश', lord_en: 'Varuna (God of Water/Ocean)', lord_hi: 'वरुण (जल/सागर के देवता)', color: '#2980b9',
            good_en: ['Dining room', 'Study room', 'Children room', 'Metal elements'],
            good_hi: ['डाइनिंग रूम', 'अध्ययन कक्ष', 'बच्चों का कमरा', 'धातु तत्व'],
            avoid_en: ['Main entrance', 'Water tanks', 'Kitchen'],
            avoid_hi: ['मुख्य प्रवेश', 'पानी की टंकी', 'रसोई'],
            tip_en: 'Good for dining and social activities. Keep medium-weight furniture here.',
            tip_hi: 'भोजन और सामाजिक गतिविधियों के लिए अच्छा। मध्यम भारी फर्नीचर रखें।'
        },
        northwest: {
            element_en: 'Air', element_hi: 'वायु', lord_en: 'Vayu (God of Wind)', lord_hi: 'वायु देव', color: '#95a5a6',
            good_en: ['Guest room', 'Garage', 'Bathroom', 'Movement/travel', 'Light items'],
            good_hi: ['अतिथि कक्ष', 'गैरेज', 'बाथरूम', 'यात्रा', 'हल्की वस्तुएं'],
            avoid_en: ['Master bedroom', 'Heavy storage', 'Fire elements'],
            avoid_hi: ['मास्टर बेडरूम', 'भारी भंडारण', 'अग्नि तत्व'],
            tip_en: 'Zone of movement and change. Good for guests and temporary storage.',
            tip_hi: 'गति और परिवर्तन का क्षेत्र। मेहमानों और अस्थायी भंडारण के लिए अच्छा।'
        }
    };
    const lang = '<?= $lang ?>';
    function checkDirection(dir) {
        document.querySelectorAll('.dir-btn').forEach(b => { b.style.background = 'var(--bg-card)'; b.style.color = 'var(--primary)'; });
        const btn = document.getElementById('dir-' + dir);
        btn.style.background = 'var(--primary)'; btn.style.color = '#fff';
        const d = vastuData[dir];
        const goodList = (lang === 'hi' ? d.good_hi : d.good_en).map(i => `<li style="list-style:none;padding:4px 0;">✅ ${i}</li>`).join('');
        const avoidList = (lang === 'hi' ? d.avoid_hi : d.avoid_en).map(i => `<li style="list-style:none;padding:4px 0;">❌ ${i}</li>`).join('');
        document.getElementById('dirResult').innerHTML = `
    <div class="card" style="padding:var(--space-8);border-left:4px solid ${d.color};">
        <div class="flex gap-6 mb-6" style="flex-wrap:wrap;">
            <div class="badge" style="background:${d.color}22;color:${d.color};">${lang === 'hi' ? 'तत्व: ' + d.element_hi : 'Element: ' + d.element_en}</div>
            <div class="badge" style="background:${d.color}22;color:${d.color};">${lang === 'hi' ? 'स्वामी: ' + d.lord_hi : 'Lord: ' + d.lord_en}</div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);">
            <div><h4 style="color:var(--accent-green);margin-bottom:var(--space-3);">${lang === 'hi' ? '✅ शुभ' : '✅ Recommended'}</h4><ul>${goodList}</ul></div>
            <div><h4 style="color:var(--accent);margin-bottom:var(--space-3);">${lang === 'hi' ? '❌ अशुभ' : '❌ Avoid'}</h4><ul>${avoidList}</ul></div>
        </div>
        <div style="margin-top:var(--space-6);padding:var(--space-4);background:var(--bg-section);border-radius:var(--border-radius);"><strong>💡 ${lang === 'hi' ? 'सुझाव' : 'Tip'}:</strong> ${lang === 'hi' ? d.tip_hi : d.tip_en}</div>
    </div>`;
        document.getElementById('dirResult').style.display = 'block';
        document.getElementById('dirResult').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>