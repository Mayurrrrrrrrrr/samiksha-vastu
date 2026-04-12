<?php
/** User Profile */
$pageTitle = t('nav_profile');
$db = getDB();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $updates = ['name = ?', 'phone = ?', 'dob = ?', 'gender = ?'];
    $params = [$_POST['name'], $_POST['phone'] ?? '', $_POST['dob'] ?? null, $_POST['gender'] ?? null];

    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 6) {
            setFlash('error', 'Password must be at least 6 characters');
            header('Location:' . BASE_URL . 'user/profile');
            exit;
        }
        $updates[] = 'password = ?';
        $params[] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    }

    $params[] = currentUserId();
    $db->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?")->execute($params);
    $_SESSION['user_name'] = $_POST['name'];
    setFlash('success', $lang === 'hi' ? 'प्रोफ़ाइल अपडेट!' : 'Profile updated!');
    header('Location:' . BASE_URL . 'user/profile');
    exit;
}
require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <h1>
        <?= t('nav_profile') ?>
    </h1>
</div>
<div class="card" style="padding:var(--space-8);max-width:600px;">
    <form method="POST">
        <?= csrfField() ?>
        <div class="form-group"><label class="form-label">
                <?= t('full_name') ?>
            </label><input type="text" name="name" class="form-control" value="<?= clean($user['name']) ?>"></div>
        <div class="form-group"><label class="form-label">
                <?= t('email') ?>
            </label><input type="email" class="form-control" value="<?= clean($user['email']) ?>" disabled></div>
        <div class="grid grid-2">
            <div class="form-group"><label class="form-label">
                    <?= t('phone') ?>
                </label><input type="tel" name="phone" class="form-control" value="<?= clean($user['phone'] ?? '') ?>">
            </div>
            <div class="form-group"><label class="form-label">
                    <?= t('dob') ?>
                </label><input type="date" name="dob" class="form-control" value="<?= $user['dob'] ?? '' ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">
                <?= t('gender') ?>
            </label>
            <select name="gender" class="form-control">
                <option value="">--</option>
                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>
                    <?= t('male') ?>
                </option>
                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>
                    <?= t('female') ?>
                </option>
                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>
                    <?= t('other') ?>
                </option>
            </select>
        </div>
        <hr style="margin:var(--space-6) 0;">
        <h4 class="mb-4">
            <?= $lang === 'hi' ? 'पासवर्ड बदलें' : 'Change Password' ?>
        </h4>
        <div class="form-group"><label class="form-label">
                <?= $lang === 'hi' ? 'नया पासवर्ड' : 'New Password' ?>
            </label><input type="password" name="new_password" class="form-control" minlength="6"
                placeholder="<?= $lang === 'hi' ? 'खाली छोड़ें अगर बदलना नहीं' : 'Leave blank to keep current' ?>"></div>
        <button type="submit" class="btn btn-primary btn-lg">
            <?= t('save') ?>
        </button>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>