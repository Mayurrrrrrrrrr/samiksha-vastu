<?php
/** Consultant Game Questions Manager */
$pageTitle = 'Manage Quiz Questions';
require __DIR__ . '/../../layouts/consultant_header.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $db->prepare("INSERT INTO game_questions (question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$_POST['question'], $_POST['optA'], $_POST['optB'], $_POST['optC'], $_POST['optD'], $_POST['correct']]);
        setFlash('Question added successfully.', 'success');
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $db->prepare("DELETE FROM game_questions WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        setFlash('Question deleted.', 'success');
    }
    header('Location: ' . BASE_URL . 'consultant/game_questions');
    exit;
}

$questions = $db->query("SELECT * FROM game_questions ORDER BY id DESC")->fetchAll();
?>

<div class="dash-header">
    <h1>📝 Manage Vastu Quiz Questions</h1>
    <p>Add or remove questions for the public Vastu Quiz portal.</p>
</div>

<div class="grid grid-2">
    <!-- Add Form -->
    <div class="card p-6">
        <h3 class="mb-4">Add New Question</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label class="form-label">Question Text (English)</label>
                <textarea name="question" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-group grid grid-2">
                <div>
                    <label class="form-label">Option A</label>
                    <input type="text" name="optA" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Option B</label>
                    <input type="text" name="optB" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Option C</label>
                    <input type="text" name="optC" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Option D</label>
                    <input type="text" name="optD" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correct Option</label>
                <select name="correct" class="form-control" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Save Question</button>
        </form>
    </div>

    <!-- Question List -->
    <div class="card p-6" style="max-height: 80vh; overflow-y: auto;">
        <h3 class="mb-4">Existing Questions</h3>
        <?php foreach ($questions as $q): ?>
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: var(--space-4); margin-bottom: var(--space-4);">
                <strong><?= htmlspecialchars($q['question_text']) ?></strong>
                <ul style="list-style: disc; margin: 8px 0 8px 20px; color: var(--text-muted); font-size: 0.9em;">
                    <li <?= $q['correct_option']=='A'?'style="color:var(--accent-green);font-weight:bold;"':'' ?>>A: <?= htmlspecialchars($q['option_a']) ?></li>
                    <li <?= $q['correct_option']=='B'?'style="color:var(--accent-green);font-weight:bold;"':'' ?>>B: <?= htmlspecialchars($q['option_b']) ?></li>
                    <li <?= $q['correct_option']=='C'?'style="color:var(--accent-green);font-weight:bold;"':'' ?>>C: <?= htmlspecialchars($q['option_c']) ?></li>
                    <li <?= $q['correct_option']=='D'?'style="color:var(--accent-green);font-weight:bold;"':'' ?>>D: <?= htmlspecialchars($q['option_d']) ?></li>
                </ul>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this question?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $q['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline text-danger">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
