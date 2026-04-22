<?php
/** Consultant - View/Respond to Submission */
$pageTitle = $lang === 'hi' ? 'आवश्यकता विवरण' : 'Submission Detail';
$db = getDB();
$subId = intval($_GET['id'] ?? 0);
$sub = $db->prepare("SELECT s.*, u.name as user_name, u.email as user_email, u.phone as user_phone, u.dob as user_dob, u.location_token FROM submissions s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$sub->execute([$subId]);
$sub = $sub->fetch();
if (!$sub) {
    setFlash('error', 'Not found');
    header('Location:' . BASE_URL . 'consultant/submissions');
    exit;
}

// Handle consultation response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $reportFile = null;
    if (!empty($_FILES['report_file']['name'])) {
        $up = uploadFile($_FILES['report_file'], 'reports', ALLOWED_DOC_TYPES);
        if ($up['success'])
            $reportFile = $up['path'];
    }
    $exists = $db->prepare("SELECT id FROM consultations WHERE submission_id = ?");
    $exists->execute([$subId]);
    $exists = $exists->fetch();
    if ($exists) {
        $db->prepare("UPDATE consultations SET analysis=?, remedies=?, numerology_report=?, report_file=COALESCE(?,report_file) WHERE submission_id=?")->execute([$_POST['analysis'] ?? '', $_POST['remedies'] ?? '', $_POST['numerology_report'] ?? '', $reportFile, $subId]);
    } else {
        $db->prepare("INSERT INTO consultations (submission_id, consultant_id, analysis, remedies, numerology_report, report_file) VALUES (?,?,?,?,?,?)")->execute([$subId, currentUserId(), $_POST['analysis'] ?? '', $_POST['remedies'] ?? '', $_POST['numerology_report'] ?? '', $reportFile]);
    }
    $db->prepare("UPDATE submissions SET status = 'completed' WHERE id = ?")->execute([$subId]);
    setFlash('success', $lang === 'hi' ? 'परामर्श सहेजा गया!' : 'Consultation saved!');
    header('Location:' . BASE_URL . 'consultant/submission?id=' . $subId);
    exit;
}

$consultation = $db->prepare("SELECT * FROM consultations WHERE submission_id = ?");
$consultation->execute([$subId]);
$consultation = $consultation->fetch();
require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header">
    <a href="<?= BASE_URL ?>consultant/submissions" class="text-muted text-sm">←
        <?= t('back') ?>
    </a>
    <h1>
        <?= $pageTitle ?> #
        <?= $subId ?>
    </h1>
</div>

<div class="grid grid-2" style="gap:var(--space-6);">
    <!-- Submission Info -->
    <div>
        <div class="card" style="padding:var(--space-6);margin-bottom:var(--space-4);">
            <h3 class="mb-4">👤
                <?= $lang === 'hi' ? 'व्यक्तिगत जानकारी' : 'Personal Info' ?>
            </h3>
            <div style="display:grid;gap:var(--space-2);">
                <div class="flex-between"><span class="text-muted">
                        <?= t('full_name') ?>
                    </span><strong>
                        <?= clean($sub['name']) ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">Email</span><strong>
                        <?= clean($sub['user_email']) ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('phone') ?>
                    </span><strong>
                        <?= clean($sub['user_phone'] ?: $sub['phone']) ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('dob') ?>
                    </span><strong>
                        <?= $sub['dob'] ?? ($sub['user_dob'] ?? '-') ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('gender') ?>
                    </span><strong>
                        <?= $sub['gender'] ? t($sub['gender']) : '-' ?>
                    </strong></div>
            </div>
        </div>
        <div class="card" style="padding:var(--space-6);margin-bottom:var(--space-4);">
            <div class="flex-between mb-4">
                <h3 style="margin:0;">🏠 <?= $lang === 'hi' ? 'संपत्ति जानकारी' : 'Property Info' ?></h3>
                <?php if (!empty($sub['location_token'])): ?>
                    <?php 
                        $phoneNum = preg_replace('/[^0-9]/', '', $sub['user_phone'] ?: $sub['phone'] ?? '');
                        $waUrl = "https://wa.me/" . $phoneNum . "?text=" . urlencode("Hello, please share the exact location of the property for Vastu analysis by clicking this link: " . BASE_URL . "capture_location?token=" . $sub['location_token'] . "&submission_id=" . $sub['id']); 
                    ?>
                    <?php if($phoneNum): ?>
                        <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-outline" style="color:#25D366;border-color:#25D366;text-decoration:none;" title="Request Location on WhatsApp">📍 Request Location Link</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div style="display:grid;gap:var(--space-2);">
                <div class="flex-between"><span class="text-muted">
                        <?= t('property_type') ?>
                    </span><span class="badge badge-info">
                        <?= t($sub['property_type'] ?? 'house') ?>
                    </span></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('area_sqft') ?>
                    </span><strong>
                        <?= $sub['area_sqft'] ?? '-' ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('floors') ?>
                    </span><strong>
                        <?= $sub['floors'] ?? '-' ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('year_built') ?>
                    </span><strong>
                        <?= $sub['year_built'] ?? '-' ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('facing_direction') ?>
                    </span><strong>
                        <?= $sub['facing_direction'] ? t($sub['facing_direction']) : '-' ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('entrance_direction') ?>
                    </span><strong>
                        <?= $sub['entrance_direction'] ? t($sub['entrance_direction']) : '-' ?>
                    </strong></div>
                <div class="flex-between"><span class="text-muted">
                        <?= t('address') ?>
                    </span><strong>
                        <?= clean($sub['address'] ?? '-') ?>
                    </strong></div>
            </div>
        </div>
        <?php if ($sub['latitude'] && $sub['longitude']): ?>
            <div class="card" style="padding:0;overflow:hidden;margin-bottom:var(--space-4);">
                <div id="subMap" style="height:300px;"></div>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>const m = L.map('subMap').setView([<?= $sub['latitude'] ?>,<?= $sub['longitude'] ?>], 16); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM' }).addTo(m); L.marker([<?= $sub['latitude'] ?>,<?= $sub['longitude'] ?>]).addTo(m).bindPopup('<?= clean($sub['address'] ?? 'Property') ?>').openPopup();</script>
            </div>
        <?php endif; ?>
        <?php if ($sub['specific_concerns']): ?>
            <div class="card" style="padding:var(--space-6);">
                <h3 class="mb-3">📝
                    <?= t('specific_concerns') ?>
                </h3>
                <p style="line-height:1.8;">
                    <?= nl2br(clean($sub['specific_concerns'])) ?>
                </p>
            </div>
        <?php endif; ?>
        <?php if ($sub['numerology_request']): ?>
            <div class="card mt-4" style="padding:var(--space-6);">
                <h3 class="mb-3">🔢
                    <?= $lang === 'hi' ? 'अंक ज्योतिष अनुरोध' : 'Numerology Request' ?>
                </h3>
                <p style="line-height:1.8;">
                    <?= nl2br(clean($sub['numerology_request'])) ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Consultation Form -->
    <div>
        <div class="card" style="padding:var(--space-6);border-left:4px solid var(--primary);">
            <h3 class="mb-6">📊
                <?= $lang === 'hi' ? 'परामर्श जवाब' : 'Consultation Response' ?>
            </h3>
            <form method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <div class="form-group"><label class="form-label">📊
                        <?= $lang === 'hi' ? 'विश्लेषण' : 'Analysis' ?>
                    </label>
                    <textarea name="analysis" class="form-control" rows="6"
                        placeholder="<?= $lang === 'hi' ? 'वास्तु विश्लेषण लिखें...' : 'Write Vastu analysis...' ?>"><?= clean($consultation['analysis'] ?? '') ?></textarea>
                </div>
                <div class="form-group"><label class="form-label">✨
                        <?= $lang === 'hi' ? 'उपाय' : 'Remedies' ?>
                    </label>
                    <textarea name="remedies" class="form-control" rows="6"
                        placeholder="<?= $lang === 'hi' ? 'सुधारात्मक उपाय लिखें...' : 'Write corrective remedies...' ?>"><?= clean($consultation['remedies'] ?? '') ?></textarea>
                </div>
                <div class="form-group"><label class="form-label">🔢
                        <?= $lang === 'hi' ? 'अंक ज्योतिष रिपोर्ट' : 'Numerology Report' ?>
                    </label>
                    <textarea name="numerology_report" class="form-control" rows="4"
                        placeholder="<?= $lang === 'hi' ? 'अंक विश्लेषण...' : 'Number analysis...' ?>"><?= clean($consultation['numerology_report'] ?? '') ?></textarea>
                </div>
                <div class="form-group"><label class="form-label">📄
                        <?= $lang === 'hi' ? 'रिपोर्ट फ़ाइल' : 'Report File' ?> (PDF)
                    </label>
                    <input type="file" name="report_file" class="form-control" accept=".pdf,.doc,.docx">
                    <?php if ($consultation && $consultation['report_file']): ?>
                        <small class="text-muted">
                            <?= $lang === 'hi' ? 'मौजूदा:' : 'Current:' ?>
                            <?= basename($consultation['report_file']) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-full">
                    <?= $consultation ? ($lang === 'hi' ? 'अपडेट करें' : 'Update') : ($lang === 'hi' ? 'परामर्श सहेजें' : 'Save Consultation') ?>
                    ✓
                </button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>