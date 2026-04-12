<?php
/** Consultant - Manage Testimonials */
$pageTitle = $lang === 'hi' ? 'प्रशंसापत्र' : 'Manage Testimonials';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    if (!empty($_POST['test_id'])) {
        $db->prepare("UPDATE testimonials SET name=?, location=?, content=?, content_hi=?, rating=?, is_active=? WHERE id=?")->execute([$_POST['name'], $_POST['location'] ?? '', $_POST['content'], $_POST['content_hi'] ?? '', intval($_POST['rating'] ?? 5), intval($_POST['is_active'] ?? 1), $_POST['test_id']]);
    } else {
        $db->prepare("INSERT INTO testimonials (name,location,content,content_hi,rating,is_active) VALUES (?,?,?,?,?,?)")->execute([$_POST['name'], $_POST['location'] ?? '', $_POST['content'], $_POST['content_hi'] ?? '', intval($_POST['rating'] ?? 5), intval($_POST['is_active'] ?? 1)]);
    }
    setFlash('success', 'Saved!');
    header('Location:' . BASE_URL . 'consultant/testimonials');
    exit;
}
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM testimonials WHERE id=?")->execute([intval($_GET['delete'])]);
    setFlash('success', 'Deleted!');
    header('Location:' . BASE_URL . 'consultant/testimonials');
    exit;
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
$editT = null;
if (isset($_GET['edit']) && $_GET['edit'] !== 'new') {
    $s = $db->prepare("SELECT * FROM testimonials WHERE id=?");
    $s->execute([intval($_GET['edit'])]);
    $editT = $s->fetch();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div><a href="?edit=new" class="btn btn-primary">
        <?= $lang === 'hi' ? 'नया प्रशंसापत्र' : 'New Testimonial' ?> +
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>
    <div class="card" style="padding:var(--space-6);max-width:600px;">
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editT): ?><input type="hidden" name="test_id" value="<?= $editT['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Name</label><input type="text" name="name"
                        class="form-control" required value="<?= clean($editT['name'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">Location</label><input type="text" name="location"
                        class="form-control" value="<?= clean($editT['location'] ?? '') ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Content (EN)</label><textarea name="content"
                        class="form-control" rows="3" required><?= clean($editT['content'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">सामग्री (HI)</label><textarea name="content_hi"
                        class="form-control" rows="3"><?= clean($editT['content_hi'] ?? '') ?></textarea></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Rating</label><select name="rating" class="form-control">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($editT['rating'] ?? 5) == $i ? 'selected' : '' ?>>
                                <?= str_repeat('⭐', $i) ?>
                            </option>
                        <?php endfor; ?>
                    </select></div>
                <div class="form-group"><label class="form-label">Active</label><select name="is_active"
                        class="form-control">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select></div>
            </div>
            <div class="flex gap-3"><button type="submit" class="btn btn-primary">
                    <?= t('save') ?>
                </button><a href="<?= BASE_URL ?>consultant/testimonials" class="btn btn-outline">
                    <?= t('cancel') ?>
                </a></div>
        </form>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:var(--space-4);">
        <?php foreach ($testimonials as $tt): ?>
            <div class="card" style="padding:var(--space-6);">
                <div class="flex-between mb-2">
                    <div><strong>
                            <?= clean($tt['name']) ?>
                        </strong> <span class="text-sm text-muted">—
                            <?= clean($tt['location']) ?>
                        </span></div>
                    <div class="flex gap-2"><a href="?edit=<?= $tt['id'] ?>" class="btn btn-sm btn-outline">✏️</a><a
                            href="?delete=<?= $tt['id'] ?>" class="btn btn-sm btn-outline"
                            onclick="return confirm('Delete?')">🗑️</a></div>
                </div>
                <p class="text-sm">
                    <?= str_repeat('⭐', $tt['rating']) ?>
                </p>
                <p class="text-muted text-sm mt-2">
                    <?= clean(mb_substr($tt['content'], 0, 150)) ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>