<?php
/** Consultant - View Users */
$pageTitle = $lang === 'hi' ? 'उपयोगकर्ता' : 'Users';
$db = getDB();
$users = $db->query("SELECT u.*, (SELECT COUNT(*) FROM submissions WHERE user_id = u.id) as sub_count, (SELECT COUNT(*) FROM questions WHERE user_id = u.id) as q_count FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC")->fetchAll();
require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <h1>
        <?= $pageTitle ?> (
        <?= count($users) ?>)
    </h1>
</div>
<div class="card" style="overflow:auto;">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>
                    <?= $lang === 'hi' ? 'नाम' : 'Name' ?>
                </th>
                <th>Email</th>
                <th>
                    <?= t('phone') ?>
                </th>
                <th>
                    <?= t('dob') ?>
                </th>
                <th>
                    <?= $lang === 'hi' ? 'आवश्यकताएं' : 'Submissions' ?>
                </th>
                <th>
                    <?= $lang === 'hi' ? 'प्रश्न' : 'Questions' ?>
                </th>
                <th>
                    <?= $lang === 'hi' ? 'जुड़े' : 'Joined' ?>
                </th>
                <th>
                    <?= $lang === 'hi' ? 'स्थान' : 'Location' ?>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <?= $u['id'] ?>
                    </td>
                    <td><strong>
                            <?= clean($u['name']) ?>
                        </strong></td>
                    <td>
                        <?= clean($u['email']) ?>
                    </td>
                    <td>
                        <?= clean($u['phone'] ?? '-') ?>
                    </td>
                    <td>
                        <?= $u['dob'] ?? '-' ?>
                    </td>
                    <td><span class="badge badge-info">
                            <?= $u['sub_count'] ?>
                        </span></td>
                    <td><span class="badge badge-info">
                            <?= $u['q_count'] ?>
                        </span></td>
                    <td class="text-muted">
                        <?= timeAgo($u['created_at']) ?>
                    </td>
                    <td>
                        <?php if(!empty($u['latitude']) && !empty($u['longitude'])): ?>
                            <a href="https://maps.google.com/?q=<?= $u['latitude'] ?>,<?= $u['longitude'] ?>" target="_blank" class="btn btn-sm" style="background:#e8f4f8;color:#3498db;padding:4px 8px;font-size:12px;">Map</a>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:12px;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space: nowrap;">
                        <a href="<?= BASE_URL ?>consultant/chat?user=<?= $u['id'] ?>" class="btn btn-sm btn-outline" title="Chat">💬</a>
                        <?php if (!empty($u['location_token'])): ?>
                            <?php 
                                $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $u['phone']) . "?text=" . urlencode("Hello, please share your location for Vastu analysis by clicking this link: " . BASE_URL . "capture_location?token=" . $u['location_token']); 
                            ?>
                            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-outline" style="color:#25D366;border-color:#25D366;" title="Request Location on WhatsApp">📍</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>