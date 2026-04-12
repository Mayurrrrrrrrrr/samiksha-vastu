<?php
/** Consultant - Manage Quizzes */
$pageTitle = $lang === 'hi' ? 'प्रश्नोत्तरी' : 'Manage Quizzes';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    if (!empty($_POST['quiz_id'])) {
        $db->prepare("UPDATE quizzes SET title=?, title_hi=?, description=?, description_hi=?, time_limit=?, is_active=? WHERE id=?")->execute([$_POST['title'], $_POST['title_hi'] ?? '', $_POST['description'] ?? '', $_POST['description_hi'] ?? '', intval($_POST['time_limit'] ?? 300), intval($_POST['is_active'] ?? 1), $_POST['quiz_id']]);
    } else {
        $db->prepare("INSERT INTO quizzes (title,title_hi,description,description_hi,time_limit,is_active) VALUES (?,?,?,?,?,?)")->execute([$_POST['title'], $_POST['title_hi'] ?? '', $_POST['description'] ?? '', $_POST['description_hi'] ?? '', intval($_POST['time_limit'] ?? 300), intval($_POST['is_active'] ?? 1)]);
    }
    setFlash('success', 'Saved!');
    header('Location:' . BASE_URL . 'consultant/quizzes');
    exit;
}

$quizzes = $db->query("SELECT q.*, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as qcount, (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as attempts FROM quizzes q ORDER BY q.created_at DESC")->fetchAll();
$editQ = null;
if (isset($_GET['edit']) && $_GET['edit'] !== 'new') {
    $s = $db->prepare("SELECT * FROM quizzes WHERE id=?");
    $s->execute([intval($_GET['edit'])]);
    $editQ = $s->fetch();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div><a href="?edit=new" class="btn btn-primary">
        <?= $lang === 'hi' ? 'नई क्विज़' : 'New Quiz' ?> +
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>
    <div class="card" style="padding:var(--space-6);max-width:600px;">
        <form method="POST">
            <?= csrfField() ?>
            <?php if ($editQ): ?><input type="hidden" name="quiz_id" value="<?= $editQ['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Title (EN)</label><input type="text" name="title"
                        class="form-control" required value="<?= clean($editQ['title'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">शीर्षक (HI)</label><input type="text" name="title_hi"
                        class="form-control" value="<?= clean($editQ['title_hi'] ?? '') ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Description</label><textarea name="description"
                        class="form-control" rows="2"><?= clean($editQ['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">विवरण</label><textarea name="description_hi"
                        class="form-control" rows="2"><?= clean($editQ['description_hi'] ?? '') ?></textarea></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Time Limit (seconds)</label><input type="number"
                        name="time_limit" class="form-control" value="<?= $editQ['time_limit'] ?? 300 ?>"></div>
                <div class="form-group"><label class="form-label">Active</label><select name="is_active"
                        class="form-control">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select></div>
            </div>
            <div class="flex gap-3"><button type="submit" class="btn btn-primary">
                    <?= t('save') ?>
                </button><a href="<?= BASE_URL ?>consultant/quizzes" class="btn btn-outline">
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
                    <th>Questions</th>
                    <th>Attempts</th>
                    <th>Time</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $q): ?>
                    <tr>
                        <td><strong>
                                <?= clean($q['title']) ?>
                            </strong></td>
                        <td>
                            <?= $q['qcount'] ?>
                        </td>
                        <td>
                            <?= $q['attempts'] ?>
                        </td>
                        <td>
                            <?= floor($q['time_limit'] / 60) ?>m
                        </td>
                        <td>
                            <?= $q['is_active'] ? '✅' : '❌' ?>
                        </td>
                        <td><a href="?edit=<?= $q['id'] ?>" class="btn btn-sm btn-outline">✏️</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>