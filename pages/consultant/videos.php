<?php
/** Consultant - Manage Videos */
$pageTitle = $lang === 'hi' ? 'वीडियो प्रबंधित करें' : 'Manage Videos';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $ytUrl = $_POST['youtube_url'] ?? '';
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([a-zA-Z0-9_-]{11})/', $ytUrl, $m);
    $ytId = $m[1] ?? '';

    if (!empty($_POST['video_id'])) {
        $sql = "UPDATE videos SET title=?, title_hi=?, youtube_url=?, youtube_id=?, description=?, description_hi=?, category=?, status=? WHERE id=?";
        $db->prepare($sql)->execute([$_POST['title'], $_POST['title_hi'] ?? '', $ytUrl, $ytId, $_POST['description'] ?? '', $_POST['description_hi'] ?? '', $_POST['category'] ?? 'vastu', $_POST['status'] ?? 'published', $_POST['video_id']]);
    } else {
        $db->prepare("INSERT INTO videos (title,title_hi,youtube_url,youtube_id,description,description_hi,category,status) VALUES (?,?,?,?,?,?,?,?)")->execute([$_POST['title'], $_POST['title_hi'] ?? '', $ytUrl, $ytId, $_POST['description'] ?? '', $_POST['description_hi'] ?? '', $_POST['category'] ?? 'vastu', $_POST['status'] ?? 'published']);
    }
    setFlash('success', 'Saved!');
    header('Location:' . BASE_URL . 'consultant/videos');
    exit;
}
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM videos WHERE id=?")->execute([intval($_GET['delete'])]);
    setFlash('success', 'Deleted!');
    header('Location:' . BASE_URL . 'consultant/videos');
    exit;
}

$videos = $db->query("SELECT * FROM videos ORDER BY created_at DESC")->fetchAll();
$editV = null;
if (isset($_GET['edit']) && $_GET['edit'] !== 'new') {
    $s = $db->prepare("SELECT * FROM videos WHERE id=?");
    $s->execute([intval($_GET['edit'])]);
    $editV = $s->fetch();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div><a href="?edit=new" class="btn btn-primary">
        <?= $lang === 'hi' ? 'नया वीडियो' : 'New Video' ?> +
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>
    <div class="card" style="padding:var(--space-6);max-width:700px;">
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editV): ?><input type="hidden" name="video_id" value="<?= $editV['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Title (EN)</label><input type="text" name="title"
                        class="form-control" required value="<?= clean($editV['title'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">शीर्षक (HI)</label><input type="text" name="title_hi"
                        class="form-control" value="<?= clean($editV['title_hi'] ?? '') ?>"></div>
            </div>
            <div class="form-group"><label class="form-label">YouTube URL *</label><input type="url" name="youtube_url"
                    class="form-control" required value="<?= clean($editV['youtube_url'] ?? '') ?>"
                    placeholder="https://youtube.com/watch?v=..."></div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Description (EN)</label><textarea name="description"
                        class="form-control" rows="3"><?= clean($editV['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">विवरण (HI)</label><textarea name="description_hi"
                        class="form-control" rows="3"><?= clean($editV['description_hi'] ?? '') ?></textarea></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="vastu">Vastu</option>
                        <option value="numerology">Numerology</option>
                        <option value="remedies">Remedies</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3"><button type="submit" class="btn btn-primary">
                    <?= t('save') ?>
                </button><a href="<?= BASE_URL ?>consultant/videos" class="btn btn-outline">
                    <?= t('cancel') ?>
                </a></div>
        </form>
    </div>
<?php else: ?>
    <?php if (empty($videos)): ?>
        <div class="card text-center" style="padding:var(--space-12);">
            <p class="text-muted">
                <?= t('no_results') ?>
            </p>
        </div>
    <?php else: ?>
        <div class="card" style="overflow:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>YouTube ID</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $v): ?>
                        <tr>
                            <td><strong>
                                    <?= clean(mb_substr($v['title'], 0, 40)) ?>
                                </strong></td>
                            <td><code><?= $v['youtube_id'] ?></code></td>
                            <td><span class="badge badge-info">
                                    <?= $v['category'] ?>
                                </span></td>
                            <td>
                                <?= $v['views'] ?>
                            </td>
                            <td><span class="badge badge-<?= $v['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $v['status'] ?>
                                </span></td>
                            <td>
                                <div class="flex gap-2"><a href="?edit=<?= $v['id'] ?>" class="btn btn-sm btn-outline">✏️</a><a
                                        href="?delete=<?= $v['id'] ?>" class="btn btn-sm btn-outline"
                                        onclick="return confirm('Delete?')">🗑️</a></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>