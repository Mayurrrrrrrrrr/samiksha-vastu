<?php
/** Consultant - Manage Ebooks */
$pageTitle = $lang === 'hi' ? 'ई-बुक्स प्रबंधित करें' : 'Manage E-books';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $filePath = null;
    $coverPath = null;
    if (!empty($_FILES['file_path']['name'])) {
        $up = uploadFile($_FILES['file_path'], 'ebooks', ALLOWED_EBOOK_TYPES);
        if ($up['success'])
            $filePath = $up['path'];
    }
    if (!empty($_FILES['cover_image']['name'])) {
        $up = uploadFile($_FILES['cover_image'], 'ebooks/covers', ALLOWED_IMAGE_TYPES);
        if ($up['success'])
            $coverPath = $up['path'];
    }

    if (!empty($_POST['ebook_id'])) {
        $sql = "UPDATE ebooks SET title=?, title_hi=?, description=?, description_hi=?, pages=?, is_free=?, status=?";
        $params = [$_POST['title'], $_POST['title_hi'] ?? '', $_POST['description'] ?? '', $_POST['description_hi'] ?? '', intval($_POST['pages'] ?? 0), intval($_POST['is_free'] ?? 1), $_POST['status'] ?? 'published'];
        if ($filePath) {
            $sql .= ", file_path=?";
            $params[] = $filePath;
        }
        if ($coverPath) {
            $sql .= ", cover_image=?";
            $params[] = $coverPath;
        }
        $sql .= " WHERE id=?";
        $params[] = $_POST['ebook_id'];
        $db->prepare($sql)->execute($params);
    } else {
        $db->prepare("INSERT INTO ebooks (title,title_hi,description,description_hi,file_path,cover_image,pages,is_free,status) VALUES (?,?,?,?,?,?,?,?,?)")->execute([$_POST['title'], $_POST['title_hi'] ?? '', $_POST['description'] ?? '', $_POST['description_hi'] ?? '', $filePath ?? '', $coverPath, intval($_POST['pages'] ?? 0), intval($_POST['is_free'] ?? 1), $_POST['status'] ?? 'published']);
    }
    setFlash('success', 'Saved!');
    header('Location:' . BASE_URL . 'consultant/ebooks');
    exit;
}
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM ebooks WHERE id=?")->execute([intval($_GET['delete'])]);
    setFlash('success', 'Deleted!');
    header('Location:' . BASE_URL . 'consultant/ebooks');
    exit;
}

$ebooks = $db->query("SELECT * FROM ebooks ORDER BY created_at DESC")->fetchAll();
$editE = null;
if (isset($_GET['edit']) && $_GET['edit'] !== 'new') {
    $s = $db->prepare("SELECT * FROM ebooks WHERE id=?");
    $s->execute([intval($_GET['edit'])]);
    $editE = $s->fetch();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div><a href="?edit=new" class="btn btn-primary">
        <?= $lang === 'hi' ? 'नई ई-बुक' : 'New E-book' ?> +
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>
    <div class="card" style="padding:var(--space-6);max-width:700px;">
        <form method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            <?php if ($editE): ?><input type="hidden" name="ebook_id" value="<?= $editE['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Title (EN)</label><input type="text" name="title"
                        class="form-control" required value="<?= clean($editE['title'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">शीर्षक (HI)</label><input type="text" name="title_hi"
                        class="form-control" value="<?= clean($editE['title_hi'] ?? '') ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Description (EN)</label><textarea name="description"
                        class="form-control" rows="3"><?= clean($editE['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">विवरण (HI)</label><textarea name="description_hi"
                        class="form-control" rows="3"><?= clean($editE['description_hi'] ?? '') ?></textarea></div>
            </div>
            <div class="grid grid-3">
                <div class="form-group"><label class="form-label">PDF File</label><input type="file" name="file_path"
                        class="form-control" accept=".pdf">
                    <?php if ($editE && $editE['file_path']): ?><small class="text-muted">
                            <?= basename($editE['file_path']) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="form-group"><label class="form-label">Cover Image</label><input type="file" name="cover_image"
                        class="form-control" accept="image/*"></div>
                <div class="form-group"><label class="form-label">Pages</label><input type="number" name="pages"
                        class="form-control" value="<?= $editE['pages'] ?? '' ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label><input type="checkbox" name="is_free" value="1"
                            <?= ($editE['is_free'] ?? 1) ? 'checked' : '' ?>> Free</label></div>
                <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select></div>
            </div>
            <div class="flex gap-3"><button type="submit" class="btn btn-primary">
                    <?= t('save') ?>
                </button><a href="<?= BASE_URL ?>consultant/ebooks" class="btn btn-outline">
                    <?= t('cancel') ?>
                </a></div>
        </form>
    </div>
<?php else: ?>
    <div class="card" style="overflow:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Pages</th>
                    <th>Downloads</th>
                    <th>Free</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ebooks as $e): ?>
                    <tr>
                        <td><strong>
                                <?= clean($e['title']) ?>
                            </strong></td>
                        <td>
                            <?= $e['pages'] ?>
                        </td>
                        <td>
                            <?= $e['downloads'] ?>
                        </td>
                        <td>
                            <?= $e['is_free'] ? '✅' : '💰' ?>
                        </td>
                        <td><span class="badge badge-<?= $e['status'] === 'published' ? 'success' : 'warning' ?>">
                                <?= $e['status'] ?>
                            </span></td>
                        <td>
                            <div class="flex gap-2"><a href="?edit=<?= $e['id'] ?>" class="btn btn-sm btn-outline">✏️</a><a
                                    href="?delete=<?= $e['id'] ?>" class="btn btn-sm btn-outline"
                                    onclick="return confirm('Delete?')">🗑️</a></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>