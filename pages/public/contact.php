<?php
/** Contact Page */
$pageTitle = t('contact_title');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !str_starts_with(trim($_GET['route'] ?? ''), 'api/')) {
    if (verifyCSRF($_POST['csrf_token'] ?? '')) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?,?,?,?,?)");
        $stmt->execute([$_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['phone'] ?? '', $_POST['subject'] ?? '', $_POST['message'] ?? '']);
        setFlash('success', $lang === 'hi' ? 'संदेश भेज दिया गया!' : 'Message sent successfully!');
        header('Location: ' . BASE_URL . 'contact');
        exit;
    }
}
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('contact_title') ?>
    </h1>
    <p>
        <?= t('contact_subtitle') ?>
    </p>
</div>
<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-12);">
            <div class="reveal">
                <h3 style="margin-bottom:var(--space-6);">
                    <?= t('send_message') ?>
                </h3>
                <form method="POST">
                    <?= csrfField() ?>
                    <div class="grid grid-2">
                        <div class="form-group"><label class="form-label">
                                <?= t('your_name') ?> *
                            </label><input type="text" name="name" class="form-control" required></div>
                        <div class="form-group"><label class="form-label">
                                <?= t('your_email') ?> *
                            </label><input type="email" name="email" class="form-control" required></div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group"><label class="form-label">
                                <?= t('your_phone') ?>
                            </label><input type="tel" name="phone" class="form-control"></div>
                        <div class="form-group"><label class="form-label">
                                <?= t('subject') ?>
                            </label><input type="text" name="subject" class="form-control"></div>
                    </div>
                    <div class="form-group"><label class="form-label">
                            <?= t('message') ?> *
                        </label><textarea name="message" class="form-control" required rows="5"></textarea></div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <?= t('send_message') ?> →
                    </button>
                </form>
            </div>
            <div class="reveal">
                <h3 style="margin-bottom:var(--space-6);">
                    <?= t('footer_contact') ?>
                </h3>
                <div style="display:flex;flex-direction:column;gap:var(--space-6);">
                    <div style="display:flex;gap:var(--space-4);align-items:flex-start;">
                        <div
                            style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--border-radius);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                            📧</div>
                        <div>
                            <h4>Email</h4>
                            <p class="text-muted"><a href="mailto:<?= SITE_EMAIL ?>">
                                    <?= SITE_EMAIL ?>
                                </a></p>
                        </div>
                    </div>
                    <div style="display:flex;gap:var(--space-4);align-items:flex-start;">
                        <div
                            style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--border-radius);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                            📞</div>
                        <div>
                            <h4>
                                <?= t('phone') ?>
                            </h4>
                            <p class="text-muted"><a href="tel:<?= SITE_PHONE ?>">
                                    <?= SITE_PHONE ?>
                                </a></p>
                        </div>
                    </div>
                    <div style="display:flex;gap:var(--space-4);align-items:flex-start;">
                        <div
                            style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--border-radius);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                            📍</div>
                        <div>
                            <h4>
                                <?= t('address') ?>
                            </h4>
                            <p class="text-muted">
                                <?= SITE_ADDRESS ?>
                            </p>
                        </div>
                    </div>
                    <div style="display:flex;gap:var(--space-4);align-items:flex-start;">
                        <div
                            style="width:50px;height:50px;background:linear-gradient(135deg,#25d366,#128c7e);border-radius:var(--border-radius);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                            💬</div>
                        <div>
                            <h4>WhatsApp</h4>
                            <p><a href="<?= SOCIAL_WHATSAPP ?>" target="_blank" class="btn btn-sm btn-outline">
                                    <?= $lang === 'hi' ? 'WhatsApp पर चैट करें' : 'Chat on WhatsApp' ?>
                                </a></p>
                        </div>
                    </div>
                </div>
                <div style="margin-top:var(--space-8);">
                    <h4 style="margin-bottom:var(--space-4);">
                        <?= t('footer_social') ?>
                    </h4>
                    <div class="social-share">
                        <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" class="social-facebook">📘</a>
                        <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" class="social-instagram">📸</a>
                        <a href="<?= SOCIAL_YOUTUBE ?>" target="_blank" class="social-youtube">🎬</a>
                        <a href="<?= SOCIAL_TWITTER ?>" target="_blank" class="social-twitter">🐦</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Embedded Map -->
<section style="height:400px;background:var(--gray-200);">
    <div id="contactMap" style="height:100%;width:100%;"></div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('contactMap').setView([28.6139, 77.2090], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
        L.marker([28.6139, 77.2090]).addTo(map).bindPopup('<b><?= SITE_NAME ?></b><br><?= SITE_ADDRESS ?>').openPopup();
    </script>
</section>
<style>
    @media(max-width:768px) {
        .section>div>div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>