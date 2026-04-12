<?php
/** Ask a Question */
$pageTitle = t('ask_question');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO questions (user_id, title, body, category, is_public) VALUES (?,?,?,?,?)");
    $stmt->execute([currentUserId(), $_POST['title'] ?? '', $_POST['body'] ?? '', $_POST['category'] ?? 'vastu', intval($_POST['is_public'] ?? 1)]);
    setFlash('success', $lang === 'hi' ? 'प्रश्न जमा हो गया!' : 'Question submitted!');
    header('Location:' . BASE_URL . 'user/questions');
    exit;
}
require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <h1>
        <?= t('ask_question') ?>
    </h1>
</div>
<div class="card" style="padding:var(--space-8);max-width:700px;">
    <form method="POST">
        <?= csrfField() ?>
        <div class="form-group"><label class="form-label">
                <?= $lang === 'hi' ? 'श्रेणी' : 'Category' ?>
            </label>
            <select name="category" class="form-control">
                <option value="vastu">
                    <?= $lang === 'hi' ? 'वास्तु' : 'Vastu' ?>
                </option>
                <option value="numerology">
                    <?= $lang === 'hi' ? 'अंक ज्योतिष' : 'Numerology' ?>
                </option>
                <option value="remedies">
                    <?= $lang === 'hi' ? 'उपाय' : 'Remedies' ?>
                </option>
                <option value="general">
                    <?= $lang === 'hi' ? 'सामान्य' : 'General' ?>
                </option>
            </select>
        </div>
        <div class="form-group"><label class="form-label">
                <?= $lang === 'hi' ? 'प्रश्न शीर्षक' : 'Question Title' ?> *
            </label>
            <input type="text" name="title" class="form-control" required
                placeholder="<?= $lang === 'hi' ? 'जैसे: बेडरूम किस दिशा में होना चाहिए?' : 'E.g., Which direction is best for bedroom?' ?>">
        </div>
        <div class="form-group"><label class="form-label">
                <?= $lang === 'hi' ? 'विस्तृत प्रश्न' : 'Detailed Question' ?> *
            </label>
            <textarea name="body" class="form-control" rows="5" required></textarea>
        </div>
        <div class="form-group"><label style="display:flex;align-items:center;gap:var(--space-2);cursor:pointer;">
                <input type="checkbox" name="is_public" value="1" checked>
                <?= $lang === 'hi' ? 'सार्वजनिक करें' : 'Make public' ?>
            </label></div>
        <button type="submit" class="btn btn-primary btn-lg">
            <?= t('submit') ?> →
        </button>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>