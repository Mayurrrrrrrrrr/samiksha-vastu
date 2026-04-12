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

<!-- Hero Section -->
<section class="hero">
    <div class="hero-decoration hero-decoration-1"></div>
    <div class="hero-decoration hero-decoration-2"></div>
    <div class="hero-decoration hero-decoration-3"></div>

    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="pulse-dot"></span>
                <?= $lang === 'hi' ? '🙏 ऑनलाइन परामर्श उपलब्ध' : '🙏 Online Consultations Available' ?>
            </div>

            <h1>
                <?= t('hero_title') ?>
            </h1>
            <p class="hero-text">
                <?= t('hero_subtitle') ?>
            </p>

            <div class="hero-cta">
                <a href="<?= BASE_URL ?>services" class="btn btn-primary btn-lg">
                    <?= t('hero_cta') ?> →
                </a>
                <a href="<?= BASE_URL ?>about" class="btn btn-outline-light btn-lg">
                    <?= t('hero_cta2') ?>
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">10+</div>
                    <div class="hero-stat-label">
                        <?= t('about_experience') ?>
                    </div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">2000+</div>
                    <div class="hero-stat-label">
                        <?= t('about_clients') ?>
                    </div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">50+</div>
                    <div class="hero-stat-label">
                        <?= t('about_cities') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-mandala" style="width:400px;height:400px;position:relative;">
                <div class="hero-mandala-ring"></div>
                <div class="hero-mandala-ring"></div>
                <div class="hero-mandala-ring"></div>
                <div class="hero-mandala-center">🏠</div>
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

<!-- Services Section (Moved Down) -->
<section class="section" id="services">
    <div class="container">
        <div class="section-header reveal">
            <h2>
                <?= t('section_services') ?>
            </h2>
            <p>
                <?= t('section_services_sub') ?>
            </p>
        </div>

        <div class="grid grid-3">
            <div class="service-card reveal">
                <div class="service-icon">🏠</div>
                <h3>
                    <?= t('service_vastu_home') ?>
                </h3>
                <p>
                    <?= t('service_vastu_home_desc') ?>
                </p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🏢</div>
                <h3>
                    <?= t('service_vastu_office') ?>
                </h3>
                <p>
                    <?= t('service_vastu_office_desc') ?>
                </p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🔢</div>
                <h3>
                    <?= t('service_numerology') ?>
                </h3>
                <p>
                    <?= t('service_numerology_desc') ?>
                </p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🗺️</div>
                <h3>
                    <?= t('service_vastu_plot') ?>
                </h3>
                <p>
                    <?= t('service_vastu_plot_desc') ?>
                </p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">✨</div>
                <h3>
                    <?= t('service_remedies') ?>
                </h3>
                <p>
                    <?= t('service_remedies_desc') ?>
                </p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">📝</div>
                <h3>
                    <?= t('service_name_correction') ?>
                </h3>
                <p>
                    <?= t('service_name_correction_desc') ?>
                </p>
            </div>
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
            <a href="${'<?= BASE_URL ?>'}numerology-calculator" class="btn btn-outline-light btn-sm" style="margin-top:var(--space-4);">${lang === 'hi' ? 'विस्तृत विश्लेषण देखें' : 'See Full Analysis'} →</a>
        </div>
    `;
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>