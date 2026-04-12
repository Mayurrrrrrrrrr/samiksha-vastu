<?php
/**
 * Book Appointment Page
 */
$pageTitle = $lang === 'hi' ? 'अपॉइंटमेंट बुक करें' : 'Book Appointment';
require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header">
    <h1><?= $pageTitle ?></h1>
    <p><?= $lang === 'hi' ? 'विशेषज्ञ वास्तु और अंक ज्योतिष परामर्श लें' : 'Get expert Vastu and Numerology consultation' ?></p>
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a> / 
        <span><?= $pageTitle ?></span>
    </div>
</div>

<section class="section">
    <div class="container container-narrow">
        <div class="glass-card">
            <?php if (!isLoggedIn()): ?>
                <div class="alert alert-info mb-6">
                    <?= $lang === 'hi' ? 'अपॉइंटमेंट बुक करने के लिए कृपया पहले लॉगिन करें।' : 'Please login first to book an appointment.' ?>
                    <a href="<?= BASE_URL ?>login?redirect=book-appointment" style="font-weight:bold; color:inherit; text-decoration:underline;">
                        <?= t('nav_login') ?>
                    </a>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>api/submit_appointment.php" method="POST">
                <?= csrfField() ?>
                <div class="form-group mb-4">
                    <label><?= $lang === 'hi' ? 'परामर्श का प्रकार' : 'Consultation Type' ?> *</label>
                    <select name="type" class="form-control" required <?= !isLoggedIn() ? 'disabled' : '' ?>>
                        <option value="residential"><?= $lang === 'hi' ? 'आवासीय वास्तु (Residential Vastu)' : 'Residential Vastu' ?></option>
                        <option value="commercial"><?= $lang === 'hi' ? 'कमर्शियल वास्तु (Commercial Vastu)' : 'Commercial Vastu' ?></option>
                        <option value="numerology"><?= $lang === 'hi' ? 'अंक ज्योतिष (Numerology)' : 'Numerology' ?></option>
                        <option value="name_correction"><?= $lang === 'hi' ? 'नाम सुधार (Name Correction)' : 'Name Correction' ?></option>
                        <option value="other"><?= $lang === 'hi' ? 'अन्य (Other)' : 'Other' ?></option>
                    </select>
                </div>
                
                <div class="grid grid-2 mb-4">
                    <div class="form-group">
                        <label><?= $lang === 'hi' ? 'नाम' : 'Name' ?> *</label>
                        <input type="text" name="name" class="form-control" required value="<?= isLoggedIn() ? $_SESSION['user_name'] : '' ?>" <?= !isLoggedIn() ? 'disabled' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label><?= $lang === 'hi' ? 'फ़ोन नंबर' : 'Phone Number' ?> *</label>
                        <input type="tel" name="phone" class="form-control" required <?= !isLoggedIn() ? 'disabled' : '' ?>>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label><?= $lang === 'hi' ? 'पसंदीदा तिथि' : 'Preferred Date' ?></label>
                    <input type="date" name="preferred_date" class="form-control" min="<?= date('Y-m-d') ?>" <?= !isLoggedIn() ? 'disabled' : '' ?>>
                </div>

                <div class="form-group mb-6">
                    <label><?= $lang === 'hi' ? 'संक्षिप्त विवरण' : 'Brief Description' ?></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="<?= $lang === 'hi' ? 'अपनी मुख्य समस्या/चिंताएं साझा करें...' : 'Share your main concerns...' ?>" <?= !isLoggedIn() ? 'disabled' : '' ?>></textarea>
                </div>

                <?php if (isLoggedIn()): ?>
                    <button type="submit" class="btn btn-primary btn-full btn-lg">
                        <?= $lang === 'hi' ? 'बुक करें' : 'Book Now' ?>
                    </button>
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <?= $lang === 'hi' ? 'विस्तृत वास्तु विश्लेषण के लिए, आप हमारा' : 'For detailed Vastu analysis, you can also fill out our' ?> 
                            <a href="<?= BASE_URL ?>intake-form" style="color:var(--primary);text-decoration:underline;"><?= $lang === 'hi' ? 'विस्तृत इनटेक फॉर्म' : 'Comprehensive Intake Form' ?></a>
                            <?= $lang === 'hi' ? 'भी भर सकते हैं।' : '.' ?>
                        </small>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-full btn-lg" disabled>
                        <?= $lang === 'hi' ? 'लॉगिन आवश्यक' : 'Login Required' ?>
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
