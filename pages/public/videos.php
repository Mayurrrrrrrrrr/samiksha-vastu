<?php
/** Videos Page */
$pageTitle = t('nav_videos');
$db = getDB();
$videos = $db->query("SELECT * FROM videos WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1><?= t('section_videos') ?></h1>
    <p><?= t('section_videos_sub') ?></p>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a> / <span><?= t('nav_videos') ?></span></div>
</div>
<section class="section">
    <div class="container">
        <?php if (empty($videos)): ?>
            <div class="text-center" style="padding:var(--space-20) 0;">
                <p style="font-size:3rem;">🎬</p>
                <p class="text-muted mt-4"><?= t('no_results') ?></p>
            </div>
        <?php else: ?>
            <div class="grid grid-2" id="videoGrid">
                <?php foreach ($videos as $i => $v): ?>
                    <div class="video-card reveal" id="vcard-<?= $i ?>">
                        <!-- Thumbnail (shown by default) -->
                        <div class="video-thumbnail" id="vthumb-<?= $i ?>" onclick="playVideoInline(<?= $i ?>, '<?= $v['youtube_id'] ?>')" style="cursor:pointer;">
                            <img src="https://img.youtube.com/vi/<?= $v['youtube_id'] ?>/hqdefault.jpg"
                                alt="<?= clean($v['title']) ?>" loading="lazy">
                            <div class="video-play-btn">▶</div>
                        </div>
                        <!-- Embedded Player (hidden by default) -->
                        <div class="video-player-wrapper" id="vplayer-<?= $i ?>" style="display:none;">
                            <div class="video-embed-container">
                                <!-- iframe injected by JS -->
                            </div>
                        </div>
                        <div class="video-card-body">
                            <h3 class="video-card-title">
                                <?= clean($lang === 'hi' && $v['title_hi'] ? $v['title_hi'] : $v['title']) ?>
                            </h3>
                            <p class="text-sm text-muted" style="margin-top:var(--space-2);">
                                <?= clean($lang === 'hi' && $v['description_hi'] ? mb_substr($v['description_hi'], 0, 120) : mb_substr($v['description'], 0, 120)) ?>
                            </p>
                            <div style="margin-top:var(--space-4);display:flex;gap:var(--space-3);align-items:center;">
                                <button onclick="playVideoInline(<?= $i ?>, '<?= $v['youtube_id'] ?>')" class="btn btn-sm btn-primary" id="vbtn-<?= $i ?>">
                                    ▶ <?= $lang === 'hi' ? 'देखें' : 'Watch' ?>
                                </button>
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline share-btn" data-platform="whatsapp"
                                    data-url="https://youtube.com/watch?v=<?= $v['youtube_id'] ?>"
                                    data-title="<?= clean($v['title']) ?>">
                                    <?= t('share') ?>
                                </a>
                                <span class="text-xs text-muted"><?= $v['views'] ?> <?= $lang === 'hi' ? 'व्यूज़' : 'views' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
let currentPlaying = -1;

function playVideoInline(index, ytId) {
    // Stop currently playing video (if any)
    if (currentPlaying >= 0 && currentPlaying !== index) {
        stopVideo(currentPlaying);
    }

    const thumb = document.getElementById('vthumb-' + index);
    const player = document.getElementById('vplayer-' + index);
    const btn = document.getElementById('vbtn-' + index);

    if (currentPlaying === index) {
        // Toggle off - stop this video
        stopVideo(index);
        return;
    }

    // Hide thumbnail, show player
    thumb.style.display = 'none';
    player.style.display = 'block';

    // Inject iframe (fresh each time to ensure autoplay works)
    const container = player.querySelector('.video-embed-container');
    container.innerHTML = `<iframe 
        src="https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0&modestbranding=1" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
        allowfullscreen
        style="position:absolute;top:0;left:0;width:100%;height:100%;border-radius:var(--border-radius-lg);">
    </iframe>`;

    // Update button
    btn.innerHTML = '⏹ <?= $lang === "hi" ? "बंद करें" : "Stop" ?>';
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-accent');

    currentPlaying = index;

    // Scroll video into view
    document.getElementById('vcard-' + index).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function stopVideo(index) {
    const thumb = document.getElementById('vthumb-' + index);
    const player = document.getElementById('vplayer-' + index);
    const btn = document.getElementById('vbtn-' + index);

    // Remove iframe to stop playback
    player.querySelector('.video-embed-container').innerHTML = '';
    player.style.display = 'none';
    thumb.style.display = 'block';

    // Restore button
    btn.innerHTML = '▶ <?= $lang === "hi" ? "देखें" : "Watch" ?>';
    btn.classList.remove('btn-accent');
    btn.classList.add('btn-primary');

    currentPlaying = -1;
}
</script>

<section class="cta-section">
    <div class="container" style="position:relative;z-index:1;">
        <h2><?= $lang === 'hi' ? 'हमारा YouTube चैनल सब्सक्राइब करें' : 'Subscribe to Our YouTube Channel' ?></h2>
        <p><?= $lang === 'hi' ? 'नए वीडियो और टिप्स हर हफ्ते' : 'New videos and tips every week' ?></p>
        <a href="<?= SOCIAL_YOUTUBE ?>" target="_blank" class="btn btn-secondary btn-lg" rel="noopener">🎬 YouTube →</a>
    </div>
</section>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>