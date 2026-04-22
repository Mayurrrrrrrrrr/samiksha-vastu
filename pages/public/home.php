<?php
/**
 * Home Page - Vastu Samiksha
 */
$pageTitle = $lang === 'hi' ? 'होम - वास्तु समीक्षा' : 'Home - Vastu Samiksha';

// Fetch latest blogs
$db = getDB();
$langCol = $lang === 'hi' ? 'title_hi' : 'title';
$excerptCol = $lang === 'hi' ? 'excerpt_hi' : 'excerpt';

$blogs = $db->query("SELECT b.*, c.name as category_name, c.name_hi as category_name_hi FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id WHERE b.status = 'published' ORDER BY b.published_at DESC LIMIT 6")->fetchAll();
$videos = $db->query("SELECT * FROM videos WHERE status = 'published' ORDER BY created_at DESC LIMIT 4")->fetchAll();
$testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY id")->fetchAll();

require __DIR__ . '/../../layouts/public_header.php';
?>

<!-- Hero Section (Premium Indian Theme) -->
<section class="hero-gradient" style="padding: var(--space-16) 0 var(--space-12) 0; position: relative;">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: var(--space-12);">
            
            <!-- Hero Content -->
            <div style="flex: 1; min-width: 300px; z-index: 10;">
                <h1 style="font-size: var(--font-size-5xl); color: var(--primary); margin-bottom: var(--space-4); line-height: 1.1; font-family: var(--font-devanagari);">
                    <?= $lang === 'hi' ? 'वास्तु और अंक ज्योतिष से बदलें जीवन' : 'Transform Your Life with Vastu & Numerology' ?>
                </h1>
                <p style="font-size: var(--font-size-xl); color: var(--text-secondary); margin-bottom: var(--space-8); max-width: 500px;">
                    <?= $lang === 'hi' ? 'समिक्षा जी के 15 वर्षों के अनुभव के साथ अपने घर और जीवन में सकारात्मक ऊर्जा लाएं।' : 'Bring positive energy and harmony to your space and destiny with expert guidance from Samiksha ji.' ?>
                </p>
                <div style="display: flex; gap: var(--space-4); flex-wrap: wrap;">
                    <a href="<?= BASE_URL ?>book-appointment" class="btn btn-primary btn-lg">
                        <?= $lang === 'hi' ? 'परामर्श बुक करें' : 'Book Consultation' ?>
                    </a>
                    <a href="<?= BASE_URL ?>contact" class="btn btn-outline btn-lg" style="border-radius: var(--border-radius-full); padding: var(--space-4) var(--space-8); border: 2px solid var(--primary); color: var(--primary); background: transparent; font-weight: 500;">
                        <?= $lang === 'hi' ? 'हमसे बात करें' : 'Get in Touch' ?>
                    </a>
                </div>
            </div>

            <!-- Hero Visual -->
            <div style="flex: 1; min-width: 300px; display: flex; justify-content: center; position: relative; z-index: 10;">
                <div style="width: 400px; height: 500px; border-radius: 200px 200px 20px 20px; background: var(--bg-card); border: 12px solid var(--surface); box-shadow: 0 20px 40px rgba(194,91,26,0.15); position: relative; overflow: hidden; display: flex; align-items: flex-end; justify-content: center;">
                    <!-- Placeholder Photo Background Pattern -->
                    <div style="position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(135deg, var(--bg-section), rgba(212,160,23,0.1)); z-index: 1;"></div>
                    <!-- Photo Silhouette -->
                    <div style="width: 80%; height: 90%; background: var(--primary); opacity: 0.1; border-radius: 200px 200px 0 0; z-index: 2;"></div>
                    <div style="position: absolute; bottom: 20px; z-index: 3; background: var(--surface); padding: 8px 16px; border-radius: 20px; box-shadow: var(--shadow-sm); font-size: 0.9rem; font-weight: 600; color: var(--text-primary);">
                        ✨ Samiksha Ji
                    </div>
                </div>
                
                <!-- Floating Decorative Elements -->
                <div style="position: absolute; top: 50px; right: 0; font-size: 40px; animation: float 3s ease-in-out infinite; opacity: 0.5;">🪔</div>
                <div style="position: absolute; bottom: 100px; left: -20px; font-size: 40px; animation: float 4s ease-in-out infinite reverse; opacity: 0.5;">🕉️</div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Signals Banner -->
<section class="trust-banner" style="background: var(--surface); padding: var(--space-8) 0; border-bottom: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-top: -30px; position: relative; z-index: 10;">
    <div class="container">
        <div class="grid grid-3 text-center" style="gap: var(--space-4);">
            <div class="trust-card" style="padding: var(--space-6); background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 2.5rem; color: var(--primary); font-weight: 800; margin-bottom: var(--space-2);">15+</div>
                <div style="color: var(--text-secondary); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">
                    <?= $lang === 'hi' ? 'वर्षों का अनुभव' : 'Years of Experience' ?>
                </div>
            </div>
            <div class="trust-card" style="padding: var(--space-6); background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 2.5rem; color: var(--primary); font-weight: 800; margin-bottom: var(--space-2);">5000+</div>
                <div style="color: var(--text-secondary); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">
                    <?= $lang === 'hi' ? 'संतुष्ट ग्राहक' : 'Clients Helped' ?>
                </div>
            </div>
            <div class="trust-card" style="padding: var(--space-6); background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size: 2.5rem; color: var(--primary); font-weight: 800; margin-bottom: var(--space-2);">120+</div>
                <div style="color: var(--text-secondary); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem;">
                    <?= $lang === 'hi' ? 'शहरों में सेवाएं' : 'Cities Served' ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Blogs Section (Moved Up for Content-First Strategy) -->
<section class="section section-alt" id="blogs">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= t('section_blogs') ?>
            </h2>
            <p>
                <?= t('section_blogs_sub') ?>
            </p>
        </div>

        <div class="grid grid-3">
            <?php foreach ($blogs as $blog): ?>
                <div class="blog-card reveal">
                    <div class="blog-card-image">
                        <?php if ($blog['image']): ?>
                            <img src="<?= UPLOADS_URL . $blog['image'] ?>" alt="<?= clean($blog['title']) ?>">
                        <?php else: ?>
                            📰
                        <?php endif; ?>
                        <?php if ($blog['category_name']): ?>
                            <?php $dispLangCat = isset($_GET['lang']) ? $lang : DEFAULT_BLOG_LANG; ?>
                            <span class="blog-card-category">
                                <?= $dispLangCat === 'hi' ? ($blog['category_name_hi'] ?? $blog['category_name']) : $blog['category_name'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="blog-card-body">
                        <?php $dispLang = isset($_GET['lang']) ? $lang : DEFAULT_BLOG_LANG; ?>
                        <h3 class="blog-card-title">
                            <a href="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>">
                                <?= clean($dispLang === 'hi' && $blog['title_hi'] ? $blog['title_hi'] : $blog['title']) ?>
                            </a>
                        </h3>
                        <p class="blog-card-excerpt">
                            <?= clean($dispLang === 'hi' && $blog['excerpt_hi'] ? $blog['excerpt_hi'] : $blog['excerpt']) ?>
                        </p>
                        <div class="blog-card-footer">
                            <span class="blog-card-author">
                                <span class="avatar avatar-sm avatar-placeholder">
                                    <?= mb_substr(CONSULTANT_NAME, 0, 1) ?>
                                </span>
                                <?= CONSULTANT_NAME ?>
                            </span>
                            <a href="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" class="btn btn-sm btn-outline">
                                <?= t('read_more') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= BASE_URL ?>blogs" class="btn btn-primary">
                <?= t('view_all') ?> →
            </a>
        </div>
    </div>
</section>

<!-- Content-First Strategy: Newsletter CTA -->
<section class="section section-primary text-center">
    <div class="container container-narrow reveal">
        <h2 style="color:var(--white);"><?= $lang === 'hi' ? 'मुफ़्त वास्तु टिप्स प्राप्त करें' : 'Get Free Vastu Tips' ?></h2>
        <p style="color:var(--white); opacity:0.9; margin-bottom:var(--space-6);">
            <?= $lang === 'hi' ? 'हमारे न्यूज़लेटर की सदस्यता लें और सीधे अपने इनबॉक्स में ज्ञान प्राप्त करें।' : 'Subscribe to our newsletter and get knowledge directly in your inbox.' ?>
        </p>
        <form class="flex gap-4" style="justify-content:center; flex-wrap:wrap; max-width:500px; margin:0 auto;" 
              onsubmit="event.preventDefault(); showToast('<?= $lang === 'hi' ? 'सदस्यता लेने के लिए धन्यवाद!' : 'Thanks for subscribing!' ?>', 'success');">
            <input type="email" class="form-control" style="flex-grow:1; min-width:250px;" required
                   placeholder="<?= $lang === 'hi' ? 'अपना ईमेल दर्ज करें' : 'Enter your email' ?>">
            <button type="submit" class="btn btn-secondary"><?= t('submit') ?></button>
        </form>
    </div>
</section>

<!-- About Teaser (Indian Brand Style) -->
<section class="section">
    <div class="container container-narrow text-center reveal">
        <div style="font-size: 3rem; color: var(--primary); margin-bottom: var(--space-4);">ॐ</div>
        <h2 style="font-family: var(--font-devanagari); color: var(--primary); font-size: 2.5rem; margin-bottom: var(--space-4);">
            <?= $lang === 'hi' ? 'मेरी यात्रा' : 'My Journey' ?>
        </h2>
        <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: var(--space-6); line-height: 1.8;">
            <?= $lang === 'hi' ? 'नमस्ते! मैं समिक्षा हूँ। मेरा लक्ष्य प्राचीन वैदिक ज्ञान के माध्यम से आपके जीवन में संतुलन और सफलता लाना है।' : 'Namaste! I am Samiksha. My mission is to bring balance, prosperity, and success to your life through the ancient Vedic sciences of Vastu Shastra and Numerology.' ?>
        </p>
        <a href="<?= BASE_URL ?>journey" class="btn btn-outline" style="border-radius: var(--border-radius-full);">
            <?= $lang === 'hi' ? 'मेरी कहानी पढ़ें' : 'Read My Story' ?>
        </a>
    </div>
</section>

<!-- Services Section (Premium Cards) -->
<section class="section section-alt" id="services" style="background: var(--bg-section); padding: var(--space-16) 0;">
    <div class="container">
        <div class="section-header reveal">
            <h2 style="font-family: var(--font-devanagari); font-size: 2.5rem; color: var(--primary);">
                <?= $lang === 'hi' ? 'हमारी सेवाएँ' : 'Vedic Services' ?>
            </h2>
            <p style="color: var(--text-secondary);">
                <?= t('section_services_sub') ?>
            </p>
        </div>

        <div class="grid grid-3">
            <div class="card reveal" style="padding: var(--space-6); text-align: center; border-radius: 16px; border: 1px solid var(--border-color); background: var(--surface);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🏠</div>
                <h3 style="color: var(--text-primary); font-family: var(--font-heading); margin-bottom: var(--space-2);">
                    <?= t('service_vastu_home') ?>
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: var(--space-4);">
                    <?= t('service_vastu_home_desc') ?>
                </p>
                <a href="<?= BASE_URL ?>packages" style="color: var(--primary); font-weight: 600; text-decoration: none;">View Packages →</a>
            </div>
            
            <div class="card reveal" style="padding: var(--space-6); text-align: center; border-radius: 16px; border: 1px solid var(--border-color); background: var(--surface);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🏢</div>
                <h3 style="color: var(--text-primary); font-family: var(--font-heading); margin-bottom: var(--space-2);">
                    <?= t('service_vastu_office') ?>
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: var(--space-4);">
                    <?= t('service_vastu_office_desc') ?>
                </p>
                <a href="<?= BASE_URL ?>packages" style="color: var(--primary); font-weight: 600; text-decoration: none;">View Packages →</a>
            </div>
            
            <div class="card reveal" style="padding: var(--space-6); text-align: center; border-radius: 16px; border: 1px solid var(--border-color); background: var(--surface);">
                <div style="font-size: 3rem; margin-bottom: var(--space-4);">🔢</div>
                <h3 style="color: var(--text-primary); font-family: var(--font-heading); margin-bottom: var(--space-2);">
                    <?= t('service_numerology') ?>
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: var(--space-4);">
                    <?= t('service_numerology_desc') ?>
                </p>
                <a href="<?= BASE_URL ?>packages" style="color: var(--primary); font-weight: 600; text-decoration: none;">View Packages →</a>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="<?= BASE_URL ?>packages" class="btn btn-primary btn-lg" style="box-shadow: var(--shadow-md);">
                <?= $lang === 'hi' ? 'सभी सेवाएँ देखें' : 'View All Services' ?>
            </a>
        </div>
    </div>
</section>



<!-- Video Section -->
<section class="section" id="videos">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= t('section_videos') ?>
            </h2>
            <p>
                <?= t('section_videos_sub') ?>
            </p>
        </div>

        <div class="grid grid-4">
            <?php foreach ($videos as $video): ?>
                <div class="video-card reveal">
                    <div class="video-thumbnail">
                        <img src="https://img.youtube.com/vi/<?= $video['youtube_id'] ?>/mqdefault.jpg"
                            alt="<?= clean($video['title']) ?>">
                        <div class="video-play-btn">▶</div>
                    </div>
                    <div class="video-card-body">
                        <h4 class="video-card-title">
                            <?= clean($lang === 'hi' && $video['title_hi'] ? $video['title_hi'] : $video['title']) ?>
                        </h4>
                        <span class="text-sm text-muted">
                            <?= $video['views'] ?> views
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= BASE_URL ?>videos" class="btn btn-primary">
                <?= t('view_all') ?> →
            </a>
        </div>
    </div>
</section>

<!-- Numerology Calculator Widget -->
<section class="section section-dark" id="calculator">
    <div class="container">
        <div class="section-header reveal">
            <h2 style="-webkit-text-fill-color:var(--white);background:none;">
                <?= t('section_calculator') ?>
            </h2>
            <p style="color:rgba(255,255,255,0.7);">
                <?= t('section_calculator_sub') ?>
            </p>
        </div>

        <div style="max-width:600px;margin:0 auto;" class="reveal">
            <div class="glass-card">
                <div class="form-group">
                    <label class="form-label" style="color:var(--white);">
                        <?= t('enter_name') ?>
                    </label>
                    <input type="text" class="form-control" id="heroCalcName"
                        placeholder="<?= $lang === 'hi' ? 'अपना पूरा नाम लिखें' : 'Enter your full name' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" style="color:var(--white);">
                        <?= t('enter_dob') ?>
                    </label>
                    <input type="date" class="form-control" id="heroCalcDob">
                </div>
                <button class="btn btn-primary btn-full" onclick="quickCalculate()">
                    <?= t('calculate') ?> ✨
                </button>

                <div id="heroCalcResult" style="display:none;margin-top:var(--space-8);"></div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section" id="testimonials">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= t('section_testimonials') ?>
            </h2>
            <p>
                <?= t('section_testimonials_sub') ?>
            </p>
        </div>

        <div class="testimonials-slider">
            <div class="testimonials-track">
                <?php foreach ($testimonials as $t_item): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-inner">
                            <div class="stars">★★★★★</div>
                            <p class="testimonial-text">
                                "
                                <?= clean($lang === 'hi' && $t_item['content_hi'] ? $t_item['content_hi'] : $t_item['content']) ?>"
                            </p>
                            <div class="testimonial-author">
                                <div class="avatar avatar-placeholder">
                                    <?= mb_substr($t_item['name'], 0, 1) ?>
                                </div>
                                <div class="testimonial-author-info">
                                    <h4>
                                        <?= clean($t_item['name']) ?>
                                    </h4>
                                    <span>
                                        <?= clean($t_item['location']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Games CTA -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= t('section_games') ?>
            </h2>
            <p>
                <?= t('section_games_sub') ?>
            </p>
        </div>

        <div class="grid grid-3">
            <div class="service-card reveal">
                <div class="service-icon">🧠</div>
                <h3>
                    <?= t('quiz_title') ?>
                </h3>
                <p>
                    <?= $lang === 'hi' ? 'वास्तु और अंक ज्योतिष पर मजेदार प्रश्नोत्तरी' : 'Fun quizzes on Vastu & Numerology topics' ?>
                </p>
                <a href="<?= BASE_URL ?>games" class="btn btn-outline btn-sm mt-4">
                    <?= t('quiz_start') ?>
                </a>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🔢</div>
                <h3>
                    <?= t('numerology_calc') ?>
                </h3>
                <p>
                    <?= t('numerology_calc_desc') ?>
                </p>
                <a href="<?= BASE_URL ?>numerology-calculator" class="btn btn-outline btn-sm mt-4">
                    <?= t('calculate') ?>
                </a>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🧭</div>
                <h3>
                    <?= t('vastu_checker') ?>
                </h3>
                <p>
                    <?= t('vastu_checker_desc') ?>
                </p>
                <a href="<?= BASE_URL ?>vastu-checker" class="btn btn-outline btn-sm mt-4">
                    <?= $lang === 'hi' ? 'जांचें' : 'Check Now' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2>
            <?= t('section_cta') ?>
        </h2>
        <p>
            <?= t('section_cta_sub') ?>
        </p>
        <div style="display:flex;gap:var(--space-4);justify-content:center;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>services" class="btn btn-secondary btn-lg">
                <?= t('hero_cta') ?>
            </a>
            <a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" class="btn btn-outline-light btn-lg">💬 WhatsApp</a>
        </div>
    </div>
</section>

<script>
    function quickCalculate() {
        const name = document.getElementById('heroCalcName').value.trim();
        const dob = document.getElementById('heroCalcDob').value;
        const resultDiv = document.getElementById('heroCalcResult');

        if (!dob) { showToast('<?= $lang === "hi" ? "कृपया जन्म तिथि दर्ज करें" : "Please enter date of birth" ?>', 'warning'); return; }

        // Calculate Life Path Number
        const digits = dob.replace(/\D/g, '');
        let sum = 0;
        for (let d of digits) sum += parseInt(d);
        while (sum > 9 && sum !== 11 && sum !== 22 && sum !== 33) {
            let newSum = 0;
            for (let d of String(sum)) newSum += parseInt(d);
            sum = newSum;
        }

        const meanings = {
            1: { en: 'The Leader - Independent, ambitious, pioneering', hi: 'नेता - स्वतंत्र, महत्वाकांक्षी, अग्रणी' },
            2: { en: 'The Diplomat - Sensitive, cooperative, peace-loving', hi: 'राजनयिक - संवेदनशील, सहयोगी, शांतिप्रिय' },
            3: { en: 'The Communicator - Creative, expressive, social', hi: 'संवादक - रचनात्मक, अभिव्यक्त, सामाजिक' },
            4: { en: 'The Builder - Practical, organized, hardworking', hi: 'निर्माता - व्यावहारिक, संगठित, मेहनती' },
            5: { en: 'The Adventurer - Freedom-loving, versatile, dynamic', hi: 'साहसी - स्वतंत्रता-प्रेमी, बहुमुखी, गतिशील' },
            6: { en: 'The Nurturer - Loving, responsible, caring', hi: 'पोषक - प्रेमपूर्ण, जिम्मेदार, देखभाल करने वाला' },
            7: { en: 'The Seeker - Analytical, spiritual, introspective', hi: 'खोजी - विश्लेषणात्मक, आध्यात्मिक, आत्मनिरीक्षक' },
            8: { en: 'The Powerhouse - Ambitious, authoritative, goal-oriented', hi: 'शक्तिशाली - महत्वाकांक्षी, अधिकारी, लक्ष्य-उन्मुख' },
            9: { en: 'The Humanitarian - Compassionate, generous, idealistic', hi: 'मानवतावादी - दयालु, उदार, आदर्शवादी' },
            11: { en: 'Master Number - Intuitive, inspiring, visionary', hi: 'मास्टर अंक - सहज ज्ञानी, प्रेरणादायक, दूरदर्शी' },
            22: { en: 'Master Builder - Practical visionary, great achiever', hi: 'मास्टर बिल्डर - व्यावहारिक दूरदर्शी, महान उपलब्धि' },
            33: { en: 'Master Teacher - Selfless, spiritual, enlightened', hi: 'मास्टर शिक्षक - निस्वार्थ, आध्यात्मिक, प्रबुद्ध' }
        };

        const lang = '<?= $lang ?>';
        const meaning = meanings[sum] || meanings[sum % 10] || { en: 'Unique energy', hi: 'अद्वितीय ऊर्जा' };

        resultDiv.innerHTML = `
        <div style="text-align:center;">
            <div style="font-size:0.9rem;color:rgba(255,255,255,0.6);margin-bottom:var(--space-2);">${lang === 'hi' ? 'आपका जीवन पथ अंक' : 'Your Life Path Number'}</div>
            <div class="calc-number">${sum}</div>
            <div style="font-size:1.1rem;color:var(--primary-light);font-weight:600;">${meaning[lang] || meaning.en}</div>
            <a href="${'<?= BASE_URL ?>'}free-numerology" class="btn btn-outline-light btn-sm" style="margin-top:var(--space-4);">${lang === 'hi' ? 'विस्तृत नाम विश्लेषण देखें' : 'Get Free Name Analysis'} →</a>
        </div>
    `;
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>

<!-- FAQ Section -->
<section class="section" id="faq">
    <div class="container container-narrow">
        <div class="section-header reveal">
            <h2><?= $lang === 'hi' ? 'अक्सर पूछे जाने वाले प्रश्न' : 'Frequently Asked Questions' ?></h2>
            <p><?= $lang === 'hi' ? 'परामर्श को लेकर आपके सवालों के जवाब' : 'Answers to your common queries about our consultations.' ?></p>
        </div>

        <div class="faq-accordion">
            <div class="faq-item">
                <input type="checkbox" id="faq1" class="faq-toggle">
                <label for="faq1" class="faq-question"><?= $lang === 'hi' ? 'क्या ऑनलाइन वास्तु परामर्श प्रभावी है?' : 'Are online consultations as effective as in-person?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'हां, बिल्कुल! उन्नत मैपिंग और डिजिटल टूल्स के साथ, हमारा ऑनलाइन वास्तु परामर्श उतना ही सटीक है जितना व्यक्तिगत दौरा, और यह विश्व स्तर पर उपलब्ध है।' : 'Yes, absolutely! With advanced digital mapping and video consultations, our online Vastu analysis is just as precise as an in-person visit, allowing us to serve you globally.' ?></p>
                </div>
            </div>

            <div class="faq-item">
                <input type="checkbox" id="faq2" class="faq-toggle">
                <label for="faq2" class="faq-question"><?= $lang === 'hi' ? 'परामर्श के लिए मुझे क्या तैयार करना चाहिए?' : 'What do I need to prepare before a session?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'वास्तु के लिए एक सटीक फ्लोर प्लान (नक्शा) आवश्यक है। अंक ज्योतिष के लिए अपना पूरा मूल नाम और जन्म तिथि तैयार रखें।' : 'For Vastu, an accurate floor plan/layout is required. For Numerology, have your complete birth name (as per birth certificate) and exact date of birth ready.' ?></p>
                </div>
            </div>

            <div class="faq-item">
                <input type="checkbox" id="faq3" class="faq-toggle">
                <label for="faq3" class="faq-question"><?= $lang === 'hi' ? 'परामर्श सत्र कितने समय का होता है?' : 'How long does a consultation session take?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'आमतौर पर एक मानक परामर्श सत्र 45 से 60 मिनट तक चलता है, जो आपके भवन के आकार और सवालों पर निर्भर करता है।' : 'A standard consultation typically lasts between 45 to 60 minutes, depending on the complexity of your property and queries.' ?></p>
                </div>
            </div>

            <div class="faq-item">
                <input type="checkbox" id="faq4" class="faq-toggle">
                <label for="faq4" class="faq-question"><?= $lang === 'hi' ? 'परामर्श की कीमत क्या है?' : 'What is the pricing range for your services?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'हमारी सेवाएं ₹1500 से शुरू होती हैं। कृपया विस्तृत जानकारी के लिए हमारे पैकेजेस पृष्ठ को देखें।' : 'Our services start from ₹1500 for basic numerology reading. Please visit our Packages page for detailed pricing tiers depending on your exact requirements.' ?></p>
                </div>
            </div>

            <div class="faq-item">
                <input type="checkbox" id="faq5" class="faq-toggle">
                <label for="faq5" class="faq-question"><?= $lang === 'hi' ? 'वास्तु और अंक ज्योतिष में क्या अंतर है?' : 'What is the difference between Vastu and Numerology?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'वास्तु आपके भौतिक वातावरण (घर/कार्यालय) की ऊर्जाओं को संतुलित करता है, जबकि अंक ज्योतिष आपके नाम और जन्म तिथि के आधार पर आपकी व्यक्तिगत ऊर्जा (ग्रहों) का विश्लेषण करता है।' : 'Vastu aligns the environmental energies of your physical space (home/office), while Numerology analyzes your personal energy based on the vibratory resonance of your name and birth date.' ?></p>
                </div>
            </div>

            <div class="faq-item">
                <input type="checkbox" id="faq6" class="faq-toggle">
                <label for="faq6" class="faq-question"><?= $lang === 'hi' ? 'क्या सत्र के बाद फॉलो-अप नीति है?' : 'Do you have a follow-up policy after the session?' ?></label>
                <div class="faq-answer">
                    <p><?= $lang === 'hi' ? 'हां। हम आपके पैकेज के आधार पर 30 दिनों के भीतर मुफ्त फॉलो-अप प्रश्नों की अनुमति देते हैं ताकि यह सुनिश्चित हो सके कि उपाय सही ढंग से लागू हों।' : 'Yes. Depending on your chosen package, we provide up to 30 days of follow-up support via chat to ensure you implement the remedies correctly.' ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="section section-alt" id="contact-form">
    <div class="container container-narrow reveal">
        <div class="card" style="padding: var(--space-8); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: var(--space-4);">📞</div>
            <h2><?= $lang === 'hi' ? 'मुफ़्त परामर्श बुक करें' : 'Book Your Free Consultation' ?></h2>
            <p class="text-muted" style="margin-bottom: var(--space-6);">
                <?= $lang === 'hi' ? 'आज ही अपने घर या व्यवसाय में सकारात्मक बदलाव की शुरुआत करें।' : 'Take the first step towards transforming the energy of your space and life.' ?>
            </p>
            <form action="<?= BASE_URL ?>contact" method="POST" style="max-width: 400px; margin: 0 auto; text-align: left;">
                <input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" required placeholder="<?= $lang === 'hi' ? 'आपका नाम' : 'Your Name' ?>">
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" class="form-control" required placeholder="<?= $lang === 'hi' ? 'मोबाइल नंबर' : 'Mobile Number' ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-full btn-lg" style="background: var(--accent-green); border: none;">
                    <?= $lang === 'hi' ? 'अभी बुक करें' : 'Request Consultation' ?>
                </button>
            </form>
        </div>
    </div>
</section>

<style>
/* Pure CSS Accordion */
.faq-accordion {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
    margin-top: var(--space-8);
}
.faq-item {
    background: var(--surface);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
}
.faq-toggle {
    display: none;
}
.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-4) var(--space-6);
    cursor: pointer;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    transition: background 0.2s;
}
.faq-question::after {
    content: '+';
    font-size: 1.5rem;
    font-weight: 300;
    color: var(--primary);
    transition: transform 0.3s ease;
}
.faq-toggle:checked + .faq-question {
    background: var(--bg-color);
    color: var(--primary);
}
.faq-toggle:checked + .faq-question::after {
    transform: rotate(45deg);
}
.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, padding 0.3s ease;
    background: var(--bg-color);
}
.faq-answer p {
    margin: 0;
    padding: 0 var(--space-6);
    color: var(--text-secondary);
    line-height: 1.6;
}
.faq-toggle:checked ~ .faq-answer {
    max-height: 300px;
    padding: 0 0 var(--space-4) 0;
}
</style>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>