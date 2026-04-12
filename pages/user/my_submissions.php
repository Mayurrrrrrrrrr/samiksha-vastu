<?php
/** My Submissions */
$pageTitle = t('my_submissions');
$db = getDB();
$uid = currentUserId();
$subs = $db->prepare("SELECT s.*, (SELECT COUNT(*) FROM consultations WHERE submission_id = s.id) as has_response FROM submissions s WHERE s.user_id = ? ORDER BY s.created_at DESC");
$subs->execute([$uid]);
$subs = $subs->fetchAll();
require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= t('my_submissions') ?>
        </h1>
    </div>
    <a href="<?= BASE_URL ?>user/submit" class="btn btn-primary">
        <?= t('submission_title') ?> +
    </a>
</div>

<?php if (empty($subs)): ?>
    <div class="card text-center" style="padding:var(--space-12);">
        <div style="font-size:3rem;">📋</div>
        <p class="text-muted mt-4">
            <?= $lang === 'hi' ? 'कोई आवश्यकता नहीं' : 'No submissions yet' ?>
        </p>
        <a href="<?= BASE_URL ?>user/submit" class="btn btn-primary mt-4">
            <?= t('submission_title') ?> →
        </a>
    </div>
<?php else: ?>
    <div class="card" style="overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <?= t('property_type') ?>
                    </th>
                    <th>
                        <?= t('facing_direction') ?>
                    </th>
                    <th>
                        <?= t('address') ?>
                    </th>
                    <th>
                        <?= t('submission_status') ?>
                    </th>
                    <th>
                        <?= $lang === 'hi' ? 'तारीख' : 'Date' ?>
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subs as $sub): ?>
                    <tr>
                        <td>
                            <?= $sub['id'] ?>
                        </td>
                        <td><span class="badge badge-info">
                                <?= t($sub['property_type'] ?? 'house') ?>
                            </span></td>
                        <td>
                            <?= $sub['facing_direction'] ? t($sub['facing_direction']) : '-' ?>
                        </td>
                        <td>
                            <?= clean(mb_substr($sub['address'] ?? '-', 0, 50)) ?>
                        </td>
                        <td><span
                                class="badge badge-<?= $sub['status'] === 'completed' ? 'success' : ($sub['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                                <?= t('status_' . $sub['status']) ?>
                            </span></td>
                        <td class="text-muted">
                            <?= date('d/m/Y', strtotime($sub['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($sub['has_response']): ?>
                                <a href="<?= BASE_URL ?>user/consultation?id=<?= $sub['id'] ?>" class="btn btn-sm btn-primary">
                                    <?= t('consultation_report') ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>