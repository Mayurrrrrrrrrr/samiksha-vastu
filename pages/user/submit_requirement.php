<?php
/** Submit Requirement */
$pageTitle = t('submission_title');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $db = getDB();
    $photos = null;
    $floorPlan = null;

    // Handle file uploads
    if (!empty($_FILES['floor_plan']['name'])) {
        $up = uploadFile($_FILES['floor_plan'], 'submissions', ALLOWED_DOC_TYPES);
        if ($up['success'])
            $floorPlan = $up['path'];
    }

    $stmt = $db->prepare("INSERT INTO submissions (user_id, name, email, phone, dob, time_of_birth, gender, property_type, area_sqft, floors, year_built, facing_direction, entrance_direction, latitude, longitude, address, specific_concerns, floor_plan, additional_notes, numerology_request) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        currentUserId(),
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['dob'] ?? null,
        $_POST['time_of_birth'] ?? null,
        $_POST['gender'] ?? null,
        $_POST['property_type'] ?? null,
        $_POST['area_sqft'] ?? null,
        $_POST['floors'] ?? null,
        $_POST['year_built'] ?? null,
        $_POST['facing_direction'] ?? null,
        $_POST['entrance_direction'] ?? null,
        $_POST['latitude'] ?? null,
        $_POST['longitude'] ?? null,
        $_POST['address'] ?? '',
        $_POST['specific_concerns'] ?? '',
        $floorPlan,
        $_POST['additional_notes'] ?? '',
        $_POST['numerology_request'] ?? ''
    ]);
    setFlash('success', $lang === 'hi' ? 'आवश्यकता सफलतापूर्वक जमा!' : 'Requirement submitted successfully!');
    header('Location: ' . BASE_URL . 'user/submissions');
    exit;
}

$user = currentUser();
require __DIR__ . '/../../layouts/user_header.php';
?>
<div class="dash-header">
    <h1>
        <?= t('submission_title') ?>
    </h1>
    <p>
        <?= t('submission_subtitle') ?>
    </p>
</div>

<!-- Multi-step form -->
<div class="card" style="padding:var(--space-8);">
    <!-- Step Indicators -->
    <div style="display:flex;gap:var(--space-2);margin-bottom:var(--space-8);overflow-x:auto;">
        <?php $steps = ['step_personal', 'step_property', 'step_location', 'step_vastu', 'step_upload'];
        foreach ($steps as $i => $step): ?>
            <div class="step-indicator" data-step="<?= $i + 1 ?>"
                style="flex:1;text-align:center;padding:var(--space-3);border-radius:var(--border-radius);cursor:pointer;background:<?= $i === 0 ? 'var(--primary)' : 'var(--bg-section)' ?>;color:<?= $i === 0 ? 'var(--white)' : 'var(--text-muted)' ?>;font-size:var(--font-size-sm);font-weight:600;transition:all 0.3s;">
                <?= ($i + 1) ?>.
                <?= t($step) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" enctype="multipart/form-data" id="submissionForm">
        <?= csrfField() ?>

        <!-- Step 1: Personal -->
        <div class="form-step" data-step="1">
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">
                        <?= t('full_name') ?> *
                    </label><input type="text" name="name" class="form-control" required
                        value="<?= clean($user['name'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">
                        <?= t('email') ?>
                    </label><input type="email" name="email" class="form-control"
                        value="<?= clean($user['email'] ?? '') ?>"></div>
            </div>
            <div class="grid grid-3">
                <div class="form-group"><label class="form-label">
                        <?= t('phone') ?>
                    </label><input type="tel" name="phone" class="form-control"
                        value="<?= clean($user['phone'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">
                        <?= t('dob') ?> *
                    </label><input type="date" name="dob" class="form-control" required value="<?= $user['dob'] ?? '' ?>">
                </div>
                <div class="form-group"><label class="form-label">
                        <?= $lang === 'hi' ? 'जन्म समय' : 'Time of Birth' ?>
                    </label><input type="time" name="time_of_birth" class="form-control"></div>
            </div>
            <div class="form-group"><label class="form-label">
                    <?= t('gender') ?>
                </label>
                <select name="gender" class="form-control">
                    <option value="">--</option>
                    <option value="male">
                        <?= t('male') ?>
                    </option>
                    <option value="female">
                        <?= t('female') ?>
                    </option>
                    <option value="other">
                        <?= t('other') ?>
                    </option>
                </select>
            </div>
        </div>

        <!-- Step 2: Property -->
        <div class="form-step" data-step="2" style="display:none;">
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">
                        <?= t('property_type') ?>
                    </label>
                    <select name="property_type" class="form-control">
                        <option value="house">
                            <?= t('house') ?>
                        </option>
                        <option value="flat">
                            <?= t('flat') ?>
                        </option>
                        <option value="commercial">
                            <?= t('commercial') ?>
                        </option>
                        <option value="plot">
                            <?= t('plot') ?>
                        </option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">
                        <?= t('area_sqft') ?>
                    </label><input type="number" name="area_sqft" class="form-control"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">
                        <?= t('floors') ?>
                    </label><input type="number" name="floors" class="form-control" min="1" max="100"></div>
                <div class="form-group"><label class="form-label">
                        <?= t('year_built') ?>
                    </label><input type="number" name="year_built" class="form-control" min="1900" max="2030"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">
                        <?= t('facing_direction') ?>
                    </label>
                    <select name="facing_direction" class="form-control">
                        <option value="">--</option>
                        <?php foreach (['north', 'south', 'east', 'west', 'northeast', 'northwest', 'southeast', 'southwest'] as $dir): ?>
                            <option value="<?= $dir ?>">
                                <?= t($dir) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">
                        <?= t('entrance_direction') ?>
                    </label>
                    <select name="entrance_direction" class="form-control">
                        <option value="">--</option>
                        <?php foreach (['north', 'south', 'east', 'west', 'northeast', 'northwest', 'southeast', 'southwest'] as $dir): ?>
                            <option value="<?= $dir ?>">
                                <?= t($dir) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Step 3: Location with Map -->
        <div class="form-step" data-step="3" style="display:none;">
            <p class="text-muted mb-4">
                <?= t('click_map') ?>
            </p>
            <div id="locationMap"
                style="height:400px;border-radius:var(--border-radius);margin-bottom:var(--space-4);border:1px solid var(--border-color);">
            </div>
            <div class="grid grid-3">
                <div class="form-group"><label class="form-label">
                        <?= t('latitude') ?>
                    </label><input type="text" name="latitude" id="lat" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">
                        <?= t('longitude') ?>
                    </label><input type="text" name="longitude" id="lng" class="form-control" readonly></div>
                <div class="form-group"><label class="form-label">
                        <?= t('address') ?>
                    </label><input type="text" name="address" id="address" class="form-control"></div>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        </div>

        <!-- Step 4: Vastu/Numerology -->
        <div class="form-step" data-step="4" style="display:none;">
            <div class="form-group"><label class="form-label">
                    <?= t('specific_concerns') ?>
                </label>
                <textarea name="specific_concerns" class="form-control" rows="4"
                    placeholder="<?= $lang === 'hi' ? 'जैसे: स्वास्थ्य समस्या, वित्तीय परेशानी, रिश्तों में समस्या...' : 'E.g., health issues, financial problems, relationship issues...' ?>"></textarea>
            </div>
            <div class="form-group"><label class="form-label">
                    <?= $lang === 'hi' ? 'अंक ज्योतिष अनुरोध' : 'Numerology Request' ?>
                </label>
                <textarea name="numerology_request" class="form-control" rows="3"
                    placeholder="<?= $lang === 'hi' ? 'नाम विश्लेषण, भाग्यशाली अंक, मोबाइल नंबर विश्लेषण...' : 'Name analysis, lucky numbers, mobile number analysis...' ?>"></textarea>
            </div>
        </div>

        <!-- Step 5: Uploads -->
        <div class="form-step" data-step="5" style="display:none;">
            <div class="form-group"><label class="form-label">
                    <?= t('floor_plan') ?>
                </label><input type="file" name="floor_plan" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
            <div class="form-group"><label class="form-label">
                    <?= t('additional_notes') ?>
                </label><textarea name="additional_notes" class="form-control" rows="4"></textarea></div>
            <div
                style="background:var(--bg-section);padding:var(--space-6);border-radius:var(--border-radius);margin-top:var(--space-4);">
                <h4 class="mb-2">📋
                    <?= $lang === 'hi' ? 'जमा करने से पहले' : 'Before submitting' ?>
                </h4>
                <p class="text-sm text-muted">
                    <?= $lang === 'hi' ? 'कृपया सभी जानकारी जांच लें। जमा करने के बाद, सलाहकार आपसे संपर्क करेंगे।' : 'Please verify all information. After submission, the consultant will contact you.' ?>
                </p>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex-between mt-8">
            <button type="button" id="prevBtn" class="btn btn-outline" style="display:none;" onclick="changeStep(-1)">←
                <?= t('previous') ?>
            </button>
            <div></div>
            <button type="button" id="nextBtn" class="btn btn-primary" onclick="changeStep(1)">
                <?= t('next') ?> →
            </button>
            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" style="display:none;">
                <?= t('submit') ?> ✓
            </button>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 5;
    let mapInitialized = false;

    function changeStep(delta) {
        const steps = document.querySelectorAll('.form-step');
        const indicators = document.querySelectorAll('.step-indicator');

        currentStep += delta;
        if (currentStep < 1) currentStep = 1;
        if (currentStep > totalSteps) currentStep = totalSteps;

        steps.forEach((s, i) => s.style.display = (i + 1 === currentStep) ? 'block' : 'none');
        indicators.forEach((ind, i) => {
            ind.style.background = (i + 1 <= currentStep) ? 'var(--primary)' : 'var(--bg-section)';
            ind.style.color = (i + 1 <= currentStep) ? 'var(--white)' : 'var(--text-muted)';
        });

        document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-flex' : 'none';
        document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

        // Init map on step 3
        if (currentStep === 3 && !mapInitialized) {
            setTimeout(() => {
                const map = L.map('locationMap').setView([20.5937, 78.9629], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
                let marker;
                map.on('click', function (e) {
                    if (marker) marker.setLatLng(e.latlng);
                    else marker = L.marker(e.latlng).addTo(map);
                    document.getElementById('lat').value = e.latlng.lat.toFixed(8);
                    document.getElementById('lng').value = e.latlng.lng.toFixed(8);
                    // Reverse geocode
                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${e.latlng.lat}&lon=${e.latlng.lng}&format=json`)
                        .then(r => r.json()).then(d => { if (d.display_name) document.getElementById('address').value = d.display_name; }).catch(() => { });
                });
                mapInitialized = true;
            }, 200);
        }
    }

    document.querySelectorAll('.step-indicator').forEach(ind => {
        ind.addEventListener('click', () => {
            const target = parseInt(ind.dataset.step);
            const delta = target - currentStep;
            changeStep(delta);
        });
    });
</script>

<?php require __DIR__ . '/../../layouts/user_footer.php'; ?>