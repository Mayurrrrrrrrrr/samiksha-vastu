<?php
/**
 * Detailed Intake Form
 */
if (!isLoggedIn()) {
    $_SESSION['flash'] = ['type' => 'info', 'message' => 'Please login to access the Intake Form.'];
    header('Location: ' . BASE_URL . 'login?redirect=intake-form');
    exit;
}

$pageTitle = $lang === 'hi' ? 'वास्तु इनटेक फॉर्म' : 'Vastu Intake Form';
require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header">
    <h1><?= $pageTitle ?></h1>
    <p><?= $lang === 'hi' ? 'विस्तृत वास्तु विश्लेषण के लिए कृपया निम्नलिखित जानकारी प्रदान करें।' : 'Please provide the following information for detailed Vastu analysis.' ?></p>
</div>

<section class="section">
    <div class="container container-narrow">
        <div class="glass-card">
            <form action="<?= BASE_URL ?>api/submit_intake.php" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>

                <h3 class="mb-4 text-primary"><?= $lang === 'hi' ? 'संपत्ति का विवरण' : 'Property Details' ?></h3>
                <div class="form-group mb-4">
                    <label><?= $lang === 'hi' ? 'संपत्ति का स्थान / पता' : 'Location / Address of Property' ?> *</label>
                    <textarea name="location_address" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="grid grid-2 mb-4">
                    <div class="form-group">
                        <label><?= $lang === 'hi' ? 'गूगल अर्थ / मैप्स स्नैपशॉट' : 'Google Earth / Maps Snapshot' ?></label>
                        <input type="file" name="google_earth_snapshot" class="form-control" accept="image/*">
                        <small class="text-muted"><?= $lang === 'hi' ? 'संपत्ति या भूखंड का स्पष्ट स्क्रीनशॉट अपलोड करें।' : 'Upload a clear screenshot of the property or plot.' ?></small>
                    </div>
                </div>

                <hr style="margin:var(--space-8) 0; border-color:var(--border-color);">

                <h3 class="mb-4 text-primary">
                    <?= $lang === 'hi' ? 'परिवार के सदस्यों का विवरण' : 'Family Members Details' ?>
                </h3>
                <p class="text-muted mb-4"><?= $lang === 'hi' ? 'कृपया घर में रहने वाले सभी सदस्यों या व्यापार भागीदारों का विवरण जोड़ें।' : 'Please add details of all members living in the house or business partners.' ?></p>

                <div id="persons_container">
                    <!-- Dynamic Person Box 1 -->
                    <div class="person-box mb-6" style="background:var(--bg-section); padding:var(--space-6); border-radius:var(--border-radius); border:1px solid var(--border-color); position:relative;">
                        <h4 class="mb-4">Person 1</h4>
                        <div class="grid grid-2 mb-4">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="persons[0][name]" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="persons[0][dob]" class="form-control" required>
                            </div>
                        </div>
                        <div class="grid grid-2 mb-4">
                            <div class="form-group">
                                <label>Place of Birth</label>
                                <input type="text" name="persons[0][place_of_birth]" class="form-control" required placeholder="City, State">
                            </div>
                            <div class="form-group">
                                <label>Time of Birth</label>
                                <input type="time" name="persons[0][time_of_birth]" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8 text-right">
                    <button type="button" id="add_person_btn" class="btn btn-outline btn-sm">
                        + <?= $lang === 'hi' ? 'व्यक्ति जोड़ें' : 'Add Person' ?>
                    </button>
                </div>

                <hr style="margin:var(--space-8) 0; border-color:var(--border-color);">

                <h3 class="mb-4 text-primary"><?= $lang === 'hi' ? 'अन्य विवरण' : 'Other Details (Optional)' ?></h3>
                <div class="form-group mb-6">
                    <label><?= $lang === 'hi' ? 'कोई विशिष्ट चिंताएं या स्वास्थ्य समस्याएं' : 'Any specific concerns or health issues' ?></label>
                    <textarea name="other_concerns" class="form-control" rows="4"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-full text-center">
                    <?= $lang === 'hi' ? 'डेटा सबमिट करें' : 'Submit Data' ?>
                </button>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let personCount = 1;
        const container = document.getElementById('persons_container');
        const addBtn = document.getElementById('add_person_btn');

        addBtn.addEventListener('click', function() {
            personCount++;
            const div = document.createElement('div');
            div.className = 'person-box mb-6';
            div.style.cssText = 'background:var(--bg-section); padding:var(--space-6); border-radius:var(--border-radius); border:1px solid var(--border-color); position:relative;';
            
            div.innerHTML = `
                <button type="button" class="remove-person-btn" style="position:absolute; top:var(--space-4); right:var(--space-4); background:red; color:white; border:none; border-radius:50%; width:24px; height:24px; cursor:pointer;" title="Remove">×</button>
                <h4 class="mb-4">Person ${personCount}</h4>
                <div class="grid grid-2 mb-4">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="persons[${personCount-1}][name]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="persons[${personCount-1}][dob]" class="form-control" required>
                    </div>
                </div>
                <div class="grid grid-2 mb-4">
                    <div class="form-group">
                        <label>Place of Birth</label>
                        <input type="text" name="persons[${personCount-1}][place_of_birth]" class="form-control" required placeholder="City, State">
                    </div>
                    <div class="form-group">
                        <label>Time of Birth</label>
                        <input type="time" name="persons[${personCount-1}][time_of_birth]" class="form-control" required>
                    </div>
                </div>
            `;
            
            container.appendChild(div);

            div.querySelector('.remove-person-btn').addEventListener('click', function() {
                container.removeChild(div);
            });
        });
    });
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
