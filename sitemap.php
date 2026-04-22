<?php
/**
 * Dynamic XML Sitemap Generator - Vastu Samiksha
 * Generates a sitemap.xml for Google and other search engines
 */

require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/xml; charset=utf-8');

$db = getDB();
$baseUrl = rtrim(BASE_URL, '/');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">

    <!-- Static Pages -->
    <url>
        <loc>
            <?= $baseUrl ?>/
        </loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <xhtml:link rel="alternate" hreflang="en" href="<?= $baseUrl ?>/?lang=en" />
        <xhtml:link rel="alternate" hreflang="hi" href="<?= $baseUrl ?>/?lang=hi" />
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/about
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/services
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/blogs
        </loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/videos
        </loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/ebooks
        </loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/contact
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/games
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/numerology-calculator
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/vastu-checker
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/free-numerology
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/packages
        </loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>
            <?= $baseUrl ?>/questions
        </loc>
        <changefreq>daily</changefreq>
        <priority>0.6</priority>
    </url>

    <!-- Published Blog Posts -->
    <?php
    $blogs = $db->query("SELECT slug, updated_at, published_at FROM blogs WHERE status = 'published' ORDER BY published_at DESC")->fetchAll();
    foreach ($blogs as $blog):
        $lastmod = date('Y-m-d', strtotime($blog['updated_at'] ?? $blog['published_at']));
        ?>
        <url>
            <loc>
                <?= $baseUrl ?>/blog/
                <?= htmlspecialchars($blog['slug']) ?>
            </loc>
            <lastmod>
                <?= $lastmod ?>
            </lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
            <xhtml:link rel="alternate" hreflang="en"
                href="<?= $baseUrl ?>/blog/<?= htmlspecialchars($blog['slug']) ?>?lang=en" />
            <xhtml:link rel="alternate" hreflang="hi"
                href="<?= $baseUrl ?>/blog/<?= htmlspecialchars($blog['slug']) ?>?lang=hi" />
        </url>
    <?php endforeach; ?>

</urlset>