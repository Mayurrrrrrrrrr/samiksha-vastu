<?php
/** Consultation Detail Page */
$pageTitle = t('consultation_report');
$db = getDB();
$subId = intval($_GET['id'] ?? 0);
$sub = $db->prepare("SELECT * FROM submissions WHERE id = ? AND user_id = ?");
$sub->execute([$subId, currentUserId()]);
$sub = $sub->fetch();
if (!$sub) {
    setFlash('error', 'Not found');
    header('Location:' . BASE_URL . 'user/submissions');
    exit;
}

$consultation = $db->prepare("SELECT c.*, u.name as consultant_name FROM consultations c JOIN users u ON c.consultant_id = u.id WHERE c.submission_id = ?");
$consultation->execute([$subId]);
$consultation = $consultation->fetch();

require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <a href="<?= BASE_URL ?>user/submissions" class="text-muted text-sm">←
        <?= t('back') ?>
    </a>
    <h1>
        <?= t('consultation_report') ?> #
        <?= $subId ?>
    </h1>
</div>

<div class="grid grid-2" style="gap:var(--space-6);">
    <!-- Submission Details -->
    <div class="card" style="padding:var(--space-6);">
        <h3 class="mb-4">
            <?= $lang === 'hi' ? 'आवश्यकता विवरण' : 'Submission Details' ?>
        </h3>
        <div style="display:grid;gap:var(--space-3);">
            <div class="flex-between" style="padding:var(--space-2) 0;border-bottom:1px solid var(--border-color);">
                <span class="text-muted">
                    <?= t('property_type') ?>
                </span>
                <span class="font-bold">
                    <?= t($sub['property_type'] ?? 'house') ?>
                </span>
            </div>
            <div class="flex-between" style="padding:var(--space-2) 0;border-bottom:1px solid var(--border-color);">
                <span class="text-muted">
                    <?= t('facing_direction') ?>
                </span>
                <span class="font-bold">
                    <?= $sub['facing_direction'] ? t($sub['facing_direction']) : '-' ?>
                </span>
            </div>
            <div class="flex-between" style="padding:var(--space-2) 0;border-bottom:1px solid var(--border-color);">
                <span class="text-muted">
                    <?= t('area_sqft') ?>
                </span>
                <span class="font-bold">
                    <?= $sub['area_sqft'] ?? '-' ?>
                </span>
            </div>
            <div class="flex-between" style="padding:var(--space-2) 0;border-bottom:1px solid var(--border-color);">
                <span class="text-muted">
                    <?= t('address') ?>
                </span>
                <span class="font-bold">
                    <?= clean($sub['address'] ?? '-') ?>
                </span>
            </div>
            <div class="flex-between" style="padding:var(--space-2) 0;border-bottom:1px solid var(--border-color);">
                <span class="text-muted">
                    <?= t('submission_status') ?>
                </span>
                <span
                    class="badge badge-<?= $sub['status'] === 'completed' ? 'success' : ($sub['status'] === 'in_progress' ? 'warning' : 'info') ?>">
                    <?= t('status_' . $sub['status']) ?>
                </span>
            </div>
            <?php if ($sub['specific_concerns']): ?>
                <div style="padding:var(--space-2) 0;">
                    <span class="text-muted">
                        <?= t('specific_concerns') ?>
                    </span>
                    <p class="mt-2">
                        <?= clean($sub['specific_concerns']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($sub['latitude'] && $sub['longitude']): ?>
            <div id="subMap" style="height:200px;border-radius:var(--border-radius);margin-top:var(--space-4);"></div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                const m = L.map('subMap').setView([<?= $sub['latitude'] ?>, <?= $sub['longitude'] ?>], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM' }).addTo(m);
                L.marker([<?= $sub['latitude'] ?>, <?= $sub['longitude'] ?>]).addTo(m);
            </script>
        <?php endif; ?>
    </div>

    <!-- Consultation Response -->
    <div>
        <?php if ($consultation): ?>
            <div class="card" style="padding:var(--space-6);border-left:4px solid var(--accent-green);">
                <div class="flex-between mb-4">
                    <h3>
                        <?= $lang === 'hi' ? 'परामर्श रिपोर्ट' : 'Consultation Report' ?>
                    </h3>
                    <span class="text-sm text-muted">
                        <?= date('d M Y', strtotime($consultation['created_at'])) ?>
                    </span>
                </div>
                <p class="text-sm text-muted mb-4">
                    <?= $lang === 'hi' ? 'द्वारा' : 'By' ?>
                    <?= clean($consultation['consultant_name']) ?>
                </p>

                <?php if ($consultation['analysis']): ?>
                    <div class="mb-6">
                        <h4 class="mb-2">📊
                            <?= $lang === 'hi' ? 'विश्लेषण' : 'Analysis' ?>
                        </h4>
                        <div style="line-height:1.8;color:var(--text-secondary);">
                            <?= nl2br(clean($consultation['analysis'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($consultation['remedies']): ?>
                    <div class="mb-6">
                        <h4 class="mb-2">✨
                            <?= $lang === 'hi' ? 'उपाय' : 'Remedies' ?>
                        </h4>
                        <div style="line-height:1.8;color:var(--text-secondary);">
                            <?= nl2br(clean($consultation['remedies'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($consultation['numerology_report']): ?>
                    <div class="mb-6">
                        <h4 class="mb-2">🔢
                            <?= $lang === 'hi' ? 'अंक ज्योतिष रिपोर्ट' : 'Numerology Report' ?>
                        </h4>
                        <div style="line-height:1.8;color:var(--text-secondary);">
                            <?= nl2br(clean($consultation['numerology_report'])) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($consultation['report_file']): ?>
                    <a href="<?= UPLOADS_URL . $consultation['report_file'] ?>" class="btn btn-primary" download>📥
                        <?= t('download') ?> PDF
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding:var(--space-12);">
                <div style="font-size:3rem;">⏳</div>
                <h3 class="mt-4">
                    <?= $lang === 'hi' ? 'परामर्श लंबित' : 'Consultation Pending' ?>
                </h3>
                <p class="text-muted mt-2">
                    <?= $lang === 'hi' ? 'सलाहकार जल्द ही जवाब देंगे।' : 'The consultant will respond shortly.' ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>