<?php
/**
 * Blog Detail Page
 */
$db = getDB();
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT b.*, c.name as category_name, c.name_hi as category_name_hi, c.slug as category_slug, u.name as author_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id LEFT JOIN users u ON b.author_id = u.id WHERE b.slug = ? AND b.status = 'published'");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

// Increment views
$db->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?")->execute([$blog['id']]);

// Related posts
$related = $db->prepare("SELECT id, title, title_hi, slug, excerpt, excerpt_hi, image, published_at FROM blogs WHERE category_id = ? AND id != ? AND status = 'published' ORDER BY published_at DESC LIMIT 3");
$related->execute([$blog['category_id'], $blog['id']]);
$relatedPosts = $related->fetchAll();

// Fetch approved comments
try {
    $stmtComments = $db->prepare("SELECT * FROM blog_comments WHERE blog_id = ? AND is_approved = 1 ORDER BY created_at DESC");
    $stmtComments->execute([$blog['id']]);
    $comments = $stmtComments->fetchAll();
} catch (Exception $e) {
    $comments = []; // Fail gracefully if table not created
}

$dispLang = isset($_GET['lang']) ? $lang : DEFAULT_BLOG_LANG;
$title = $dispLang === 'hi' && $blog['title_hi'] ? $blog['title_hi'] : $blog['title'];
$content = $dispLang === 'hi' && $blog['content_hi'] ? $blog['content_hi'] : $blog['content'];
$pageTitle = $title;
$pageDescription = $blog['meta_description'] ?? ($lang === 'hi' && $blog['excerpt_hi'] ? $blog['excerpt_hi'] : $blog['excerpt']);

// Reading time estimate
$wordCount = str_word_count(strip_tags($content));
$readingTime = max(1, ceil($wordCount / 200));

// Sanitize blog content - allow safe HTML tags
function sanitizeBlogContent($html)
{
    $allowed = '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><s><a><ul><ol><li><blockquote><pre><code><img><iframe><figure><figcaption><table><thead><tbody><tr><th><td><div><span><hr><sub><sup>';
    return strip_tags($html, $allowed);
}
$safeContent = sanitizeBlogContent($content);

// Structured data for blog detail
$blogJsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $blog['title'],
    'description' => $pageDescription,
    'image' => $blog['image'] ? UPLOADS_URL . $blog['image'] : ASSETS_URL . 'images/og-image.jpg',
    'author' => [
        '@type' => 'Person',
        'name' => $blog['author_name'] ?? CONSULTANT_NAME,
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SITE_NAME,
    ],
    'datePublished' => date('c', strtotime($blog['published_at'])),
    'dateModified' => date('c', strtotime($blog['updated_at'] ?? $blog['published_at'])),
    'mainEntityOfPage' => BASE_URL . 'blog/' . $blog['slug'],
    'wordCount' => $wordCount,
    'inLanguage' => $lang === 'hi' ? 'hi' : 'en',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$extraHeadTags = '
<script type="application/ld+json">' . $blogJsonLd . '</script>
<link rel="alternate" hreflang="en" href="' . BASE_URL . 'blog/' . $blog['slug'] . '?lang=en" />
<link rel="alternate" hreflang="hi" href="' . BASE_URL . 'blog/' . $blog['slug'] . '?lang=hi" />
<meta property="og:type" content="article" />
<meta property="article:published_time" content="' . date('c', strtotime($blog['published_at'])) . '" />
<meta property="og:image" content="' . ($blog['image'] ? UPLOADS_URL . $blog['image'] : '') . '" />
';

require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> /
        <a href="<?= BASE_URL ?>blogs">
            <?= t('nav_blogs') ?>
        </a> /
        <span>
            <?= clean($title) ?>
        </span>
    </div>
</div>

<article class="section" itemscope itemtype="https://schema.org/BlogPosting">
    <div class="container container-narrow">
        <!-- Category -->
        <?php if ($blog['category_name']): ?>
            <div class="mb-4">
                <a href="<?= BASE_URL ?>blogs?category=<?= $blog['category_slug'] ?>" class="badge badge-primary">
                    <?= clean($dispLang === 'hi' ? ($blog['category_name_hi'] ?? $blog['category_name']) : $blog['category_name']) ?>
                </a>
            </div>
        <?php endif; ?>

        <h1 style="font-size:var(--font-size-4xl);margin-bottom:var(--space-6);" itemprop="headline">
            <?= clean($title) ?>
        </h1>

        <!-- Meta -->
        <div class="flex gap-6 mb-8" style="color:var(--text-muted);font-size:var(--font-size-sm);">
            <span itemprop="author">
                <?= t('posted_by') ?> <strong>
                    <?= clean($blog['author_name']) ?>
                </strong>
            </span>
            <span>
                <?= t('on') ?> <time itemprop="datePublished">
                    <?= date('M d, Y', strtotime($blog['published_at'])) ?>
                </time>
            </span>
            <span>👁
                <?= $blog['views'] + 1 ?> views
            </span>
            <span>⏱️ <?= $readingTime ?> <?= $lang === 'hi' ? 'मिनट पढ़ने का समय' : 'min read' ?></span>
        </div>

        <!-- Layout with Sidebar for CTA and Popular Posts -->
        <div class="grid grid-3" style="align-items:start;">
            
            <!-- Main Content Area (Spans 2 columns on desktop) -->
            <div style="grid-column: span 2;">
                <!-- Featured Image -->
                <?php if ($blog['image']): ?>
                    <div class="mb-8 rounded-lg overflow-hidden">
                        <img src="<?= UPLOADS_URL . $blog['image'] ?>" alt="<?= clean($title) ?>"
                            style="width:100%;max-height:500px;object-fit:cover;" itemprop="image">
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <div class="blog-content" itemprop="articleBody"
                    style="font-size:var(--font-size-lg);line-height:1.8;color:var(--text-secondary);">
                    <?= $safeContent ?>
                    
                    <!-- Inline Consultation CTA -->
                    <div style="margin:var(--space-8) 0; padding:var(--space-8); background:var(--bg-section); border-left:4px solid var(--primary); border-radius:var(--border-radius);">
                        <h3 style="margin-top:0; color:var(--text-primary);"><?= $lang === 'hi' ? 'वास्तु दोष से परेशान हैं?' : 'Facing Vastu Issues?' ?></h3>
                        <p style="margin-bottom:var(--space-4);"><?= $lang === 'hi' ? 'हमारे विशेषज्ञ से सलाह लें और अपने जीवन में सकारात्मक बदलाव लाएं। पहली सलाह बिल्कुल मुफ्त है!' : 'Consult our expert and bring positive changes to your life. The first consultation is completely free!' ?></p>
                        <a href="<?= BASE_URL ?>services" class="btn btn-primary"><?= $lang === 'hi' ? 'मुफ़्त परामर्श बुक करें' : 'Book Free Consultation' ?> →</a>
                    </div>
                </div>

                <!-- Share -->
        <div style="margin-top:var(--space-12);padding-top:var(--space-8);border-top:1px solid var(--border-color);">
            <h4 class="mb-4">
                <?= t('share_post') ?>
            </h4>
            <div class="social-share">
                <a href="javascript:void(0)" class="social-facebook share-btn" data-platform="facebook"
                    data-url="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" data-title="<?= clean($title) ?>">📘</a>
                <a href="javascript:void(0)" class="social-twitter share-btn" data-platform="twitter"
                    data-url="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" data-title="<?= clean($title) ?>">🐦</a>
                <a href="javascript:void(0)" class="social-whatsapp share-btn" data-platform="whatsapp"
                    data-url="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" data-title="<?= clean($title) ?>">💬</a>
                <a href="javascript:void(0)" class="social-linkedin share-btn" data-platform="linkedin"
                    data-url="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" data-title="<?= clean($title) ?>">💼</a>
                <a href="javascript:void(0)" class="social-telegram share-btn" data-platform="telegram"
                    data-url="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>" data-title="<?= clean($title) ?>">✈️</a>
            </div>
        </div>

        <!-- Comments Section -->
        <div style="margin-top:var(--space-12);padding-top:var(--space-8);border-top:1px solid var(--border-color);" id="comments">
            <h3 class="mb-6">
                <?= $lang === 'hi' ? 'टिप्पणियाँ' : 'Comments' ?> (<?= count($comments) ?>)
            </h3>
            
            <?php if (!empty($comments)): ?>
                <div class="comments-list mb-8">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item" style="background:var(--bg-section); padding:var(--space-4); border-radius:var(--border-radius); margin-bottom:var(--space-4);">
                            <div class="flex" style="justify-content:space-between; margin-bottom:var(--space-2);">
                                <strong><?= clean($comment['name']) ?></strong>
                                <span style="color:var(--text-muted); font-size:var(--font-size-sm);"><?= date('M d, Y', strtotime($comment['created_at'])) ?></span>
                            </div>
                            <p style="margin:0;"><?= nl2br(clean($comment['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-8"><?= $lang === 'hi' ? 'अभी तक कोई टिप्पणी नहीं।' : 'No comments yet. Be the first to comment!' ?></p>
            <?php endif; ?>

            <!-- Leave a Comment Form -->
            <div class="comment-form-box" style="background:var(--bg-card); padding:var(--space-6); border-radius:var(--border-radius); border:1px solid var(--border-color);">
                <h4 class="mb-4"><?= $lang === 'hi' ? 'एक टिप्पणी छोड़ें' : 'Leave a Comment' ?></h4>
                <p class="text-muted text-sm mb-4"><?= $lang === 'hi' ? 'आपकी टिप्पणी मॉडरेटर द्वारा स्वीकृत होने के बाद दिखाई देगी।' : 'Your comment will appear after approval by a moderator.' ?></p>
                
                <form action="<?= BASE_URL ?>api/submit_comment.php" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="blog_id" value="<?= $blog['id'] ?>">
                    <input type="hidden" name="redirect_url" value="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>#comments">
                    
                    <div class="grid grid-2 mb-4">
                        <div class="form-group">
                            <label><?= $lang === 'hi' ? 'नाम' : 'Name' ?> *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label><?= $lang === 'hi' ? 'ईमेल' : 'Email' ?> *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label><?= $lang === 'hi' ? 'टिप्पणी' : 'Comment' ?> *</label>
                        <textarea name="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $lang === 'hi' ? 'टिप्पणी पोस्ट करें' : 'Post Comment' ?></button>
                </form>
            </div>
        </div>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                    <div style="margin-top:var(--space-12);">
                        <h3 class="mb-8">
                            <?= t('related_posts') ?>
                        </h3>
                        <div class="grid grid-2">
                            <?php foreach ($relatedPosts as $rp): ?>
                                <div class="blog-card">
                                    <div class="blog-card-image" style="height:150px;">
                                        <?php if ($rp['image']): ?>
                                            <img src="<?= UPLOADS_URL . $rp['image'] ?>" alt="">
                                        <?php else: ?>📰
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-card-body">
                                        <h4 class="blog-card-title">
                                            <a href="<?= BASE_URL ?>blog/<?= $rp['slug'] ?>">
                                                <?= clean($dispLang === 'hi' && $rp['title_hi'] ? $rp['title_hi'] : $rp['title']) ?>
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div> <!-- End Main Content -->

            <!-- Sidebar -->
            <aside class="blog-sidebar" style="position:sticky; top:100px;">
                <!-- Sidebar CTA -->
                <div class="glass-card mb-8 text-center" style="background:var(--primary); color:white;">
                    <h3 style="color:white; margin-top:0;"><?= $lang === 'hi' ? 'विशेषज्ञ सलाह लें' : 'Get Expert Advice' ?></h3>
                    <p style="opacity:0.9; margin-bottom:var(--space-4); font-size:var(--font-size-sm);">
                        <?= $lang === 'hi' ? 'व्यक्तिगत वास्तु समाधान और मार्गदर्शन के लिए अपॉइंटमेंट बुक करें।' : 'Book an appointment for personalized Vastu solutions and guidance.' ?>
                    </p>
                    <a href="<?= BASE_URL ?>services" class="btn btn-secondary btn-full" style="background:var(--white); color:var(--primary); border:none;"><?= $lang === 'hi' ? 'अभी बुक करें' : 'Book Now' ?></a>
                </div>

                <!-- Ask a Question Quick Form -->
                <div class="glass-card mb-8">
                    <h4 class="mb-4" style="margin-top:0;"><?= $lang === 'hi' ? 'प्रश्न पूछें' : 'Ask a Question' ?></h4>
                    <form onsubmit="event.preventDefault(); showToast('<?= $lang === 'hi' ? 'आपका प्रश्न सबमिट कर दिया गया है।' : 'Your question has been submitted.' ?>', 'success'); this.reset();">
                        <div class="form-group mb-4">
                            <input type="text" class="form-control" placeholder="<?= $lang === 'hi' ? 'नाम' : 'Name' ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <textarea class="form-control" rows="3" placeholder="<?= $lang === 'hi' ? 'आपका प्रश्न...' : 'Your question...' ?>" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-full"><?= t('submit') ?></button>
                    </form>
                </div>
            </aside>
            
        </div> <!-- End Grid Layout -->
    </div>
</article>

<style>
    .blog-content h2 {
        margin: var(--space-8) 0 var(--space-4);
        color: var(--text-primary);
        font-size: var(--font-size-2xl);
    }

    .blog-content h3 {
        margin: var(--space-6) 0 var(--space-3);
        color: var(--text-primary);
        font-size: var(--font-size-xl);
    }

    .blog-content h4 {
        margin: var(--space-4) 0 var(--space-2);
        color: var(--text-primary);
    }

    .blog-content p {
        margin-bottom: var(--space-4);
    }

    .blog-content ul,
    .blog-content ol {
        margin: var(--space-4) 0;
        padding-left: var(--space-8);
    }

    .blog-content li {
        margin-bottom: var(--space-2);
        list-style: disc;
    }

    .blog-content ol li {
        list-style: decimal;
    }

    .blog-content strong {
        color: var(--text-primary);
    }

    .blog-content blockquote {
        border-left: 4px solid var(--primary);
        padding: var(--space-4) var(--space-6);
        margin: var(--space-6) 0;
        background: var(--bg-section);
        border-radius: 0 var(--border-radius) var(--border-radius) 0;
    }
</style>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>