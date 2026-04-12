<?php
/** Consultant - Manage Submissions */
$pageTitle = $lang === 'hi' ? 'आवश्यकताएं प्रबंधित करें' : 'Manage Submissions';
$db = getDB();

$status = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($status) {
    $where = "WHERE s.status = ?";
    $params[] = $status;
}

$subs = $db->prepare("SELECT s.*, u.name as user_name, u.phone as user_phone FROM submissions s JOIN users u ON s.user_id = u.id $where ORDER BY s.created_at DESC");
$subs->execute($params);
$subs = $subs->fetchAll();

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div>
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>consultant/submissions" class="btn btn-sm <?= !$status ? 'btn-primary' : 'btn-outline' ?>">
            <?= $lang === 'hi' ? 'सभी' : 'All' ?>
        </a>
        <a href="?status=pending" class="btn btn-sm <?= $status === 'pending' ? 'btn-primary' : 'btn-outline' ?>">
            <?= t('status_pending') ?>
        </a>
        <a href="?status=in_progress" class="btn btn-sm <?= $status === 'in_progress' ? 'btn-primary' : 'btn-outline' ?>">
            <?= t('status_in_progress') ?>
        </a>
        <a href="?status=completed" class="btn btn-sm <?= $status === 'completed' ? 'btn-primary' : 'btn-outline' ?>">
            <?= t('status_completed') ?>
        </a>
    </div>
</div>

<?php if (empty($subs)): ?>
    <div class="card text-center" style="padding:var(--space-12);">
        <div style="font-size:3rem;">📋</div>
        <p class="text-muted mt-4">
            <?= t('no_results') ?>
        </p>
    </div>
<?php else: ?>
    <div class="card" style="overflow:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <?= $lang === 'hi' ? 'उपयोगकर्ता' : 'User' ?>
                    </th>
                    <th>
                        <?= t('phone') ?>
                    </th>
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
                        <td><strong>
                                <?= clean($sub['user_name']) ?>
                            </strong></td>
                        <td>
                            <?= clean($sub['user_phone'] ?: '-') ?>
                        </td>
                        <td><span class="badge badge-info">
                                <?= t($sub['property_type'] ?? 'house') ?>
                            </span></td>
                        <td>
                            <?= $sub['facing_direction'] ? t($sub['facing_direction']) : '-' ?>
                        </td>
                        <td>
                            <?= clean(mb_substr($sub['address'] ?? '-', 0, 40)) ?>
                        </td>
                        <td>
                            <select class="form-control" style="width:auto;font-size:12px;padding:4px 8px;"
                                onchange="updateStatus(<?= $sub['id'] ?>, this.value)">
                                <option value="pending" <?= $sub['status'] === 'pending' ? 'selected' : '' ?>>
                                    <?= t('status_pending') ?>
                                </option>
                                <option value="in_progress" <?= $sub['status'] === 'in_progress' ? 'selected' : '' ?>>
                                    <?= t('status_in_progress') ?>
                                </option>
                                <option value="completed" <?= $sub['status'] === 'completed' ? 'selected' : '' ?>>
                                    <?= t('status_completed') ?>
                                </option>
                            </select>
                        </td>
                        <td class="text-muted">
                            <?= timeAgo($sub['created_at']) ?>
                        </td>
                        <td><a href="<?= BASE_URL ?>consultant/submission?id=<?= $sub['id'] ?>" class="btn btn-sm btn-primary">
                                <?= $lang === 'hi' ? 'देखें' : 'View' ?>
                            </a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
    async function updateStatus(id, status) {
        try {
            await fetch('<?= BASE_URL ?>api/submission', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_status', id, status })
            });
            showToast('<?= $lang === "hi" ? "स्थिति अपडेट!" : "Status updated!" ?>');
        } catch (e) { showToast('Error', 'error'); }
    }
</script>

<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>