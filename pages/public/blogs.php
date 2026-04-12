<?php
/**
 * Blog Listing Page
 */
$pageTitle = t('blog_title');
$pageDescription = $lang === 'hi' ? 'वास्तु शास्त्र और अंक ज्योतिष पर विशेषज्ञ ब्लॉग और टिप्स' : 'Expert blogs and tips on Vastu Shastra & Numerology';

$db = getDB();
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * POSTS_PER_PAGE;
$category = $_GET['category'] ?? '';
$search = $_GET['q'] ?? '';

$where = "WHERE b.status = 'published'";
$params = [];

if ($category) {
    $where .= " AND c.slug = ?";
    $params[] = $category;
}
if ($search) {
    $where .= " AND (b.title LIKE ? OR b.content LIKE ? OR b.title_hi LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / POSTS_PER_PAGE);

$stmt = $db->prepare("SELECT b.*, c.name as category_name, c.name_hi as category_name_hi, u.name as author_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id LEFT JOIN users u ON b.author_id = u.id $where ORDER BY b.published_at DESC LIMIT " . POSTS_PER_PAGE . " OFFSET $offset");
$stmt->execute($params);
$blogs = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();

require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header">
    <h1>
        <?= t('blog_title') ?>
    </h1>
    <p>
        <?= t('blog_subtitle') ?>
    </p>
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> / <span>
            <?= t('nav_blogs') ?>
        </span>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Search & Filter -->
        <div style="display:flex;gap:var(--space-4);margin-bottom:var(--space-8);flex-wrap:wrap;align-items:center;">
            <form style="flex:1;min-width:200px;display:flex;gap:var(--space-2);">
                <input type="text" name="q" class="form-control" placeholder="<?= t('search') ?>..."
                    value="<?= clean($search) ?>">
                <button type="submit" class="btn btn-primary">
                    <?= t('search') ?>
                </button>
            </form>
            <div style="display:flex;gap:var(--space-2);flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>blogs" class="btn btn-sm <?= !$category ? 'btn-primary' : 'btn-outline' ?>">
                    <?= $lang === 'hi' ? 'सभी' : 'All' ?>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= BASE_URL ?>blogs?category=<?= $cat['slug'] ?>"
                        class="btn btn-sm <?= $category === $cat['slug'] ? 'btn-primary' : 'btn-outline' ?>">
                        <?= clean($lang === 'hi' && $cat['name_hi'] ? $cat['name_hi'] : $cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($blogs)): ?>
            <div class="text-center" style="padding:var(--space-20) 0;">
                <p style="font-size:3rem;">📝</p>
                <p class="text-muted mt-4">
                    <?= t('no_results') ?>
                </p>
            </div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($blogs as $blog): ?>
                    <article class="blog-card reveal" itemscope itemtype="https://schema.org/BlogPosting">
                        <div class="blog-card-image">
                            <?php if ($blog['image']): ?>
                                <img src="<?= UPLOADS_URL . $blog['image'] ?>" alt="<?= clean($blog['title']) ?>" itemprop="image">
                            <?php else: ?>
                                📰
                            <?php endif; ?>
                            <?php if ($blog['category_name']): ?>
                                <?php $dispLangCat = isset($_GET['lang']) ? $lang : DEFAULT_BLOG_LANG; ?>
                                <span class="blog-card-category">
                                    <?= clean($dispLangCat === 'hi' ? ($blog['category_name_hi'] ?? $blog['category_name']) : $blog['category_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="blog-card-body">
                            <?php $dispLang = isset($_GET['lang']) ? $lang : DEFAULT_BLOG_LANG; ?>
                            <h3 class="blog-card-title" itemprop="headline">
                                <a href="<?= BASE_URL ?>blog/<?= $blog['slug'] ?>">
                                    <?= clean($dispLang === 'hi' && $blog['title_hi'] ? $blog['title_hi'] : $blog['title']) ?>
                                </a>
                            </h3>
                            <p class="blog-card-excerpt" itemprop="description">
                                <?= clean($dispLang === 'hi' && $blog['excerpt_hi'] ? $blog['excerpt_hi'] : $blog['excerpt']) ?>
                            </p>
                            <div class="blog-card-footer">
                                <span class="blog-card-author" itemprop="author">
                                    <?= clean($blog['author_name']) ?>
                                </span>
                                <span class="text-sm text-muted">
                                    <?= date('M d, Y', strtotime($blog['published_at'])) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $category ? "&category=$category" : '' ?>">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active">
                                <?= $i ?>
                            </span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= $category ? "&category=$category" : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $category ? "&category=$category" : '' ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>