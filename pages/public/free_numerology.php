<?php
/**
 * Free Numerology Calculator (Lead Generation Widget)
 */
$pageTitle = $lang === 'hi' ? 'मुफ़्त नाम अंक ज्योतिष गणना' : 'Free Name Numerology Calculator';
$pageDescription = $lang === 'hi' ? 'चैल्डियन अंक ज्योतिष के अनुसार अपने नाम का रहस्य जानें।' : 'Discover the hidden vibration of your name using Chaldean Numerology.';

require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
    <h1><?= $pageTitle ?></h1>
    <p style="color: rgba(255,255,255,0.9);"><?= $pageDescription ?></p>
</div>

<section class="section" style="background-color: var(--bg-color);">
    <div class="container container-narrow reveal">
        
        <div id="calculator-widget" class="card" style="padding: var(--space-8); max-width: 500px; margin: 0 auto; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border-top: 5px solid var(--accent-orange);">
            
            <div id="input-step">
                <div style="text-align: center; margin-bottom: var(--space-6);">
                    <div style="font-size: 3rem; margin-bottom: var(--space-2);">🔢</div>
                    <h3><?= $lang === 'hi' ? 'अपना नाम दर्ज करें' : 'Enter Your Details' ?></h3>
                    <p class="text-muted text-sm"><?= $lang === 'hi' ? 'सटीक परिणाम और मुफ्त परामर्श सहायता प्राप्त करने के लिए विवरण भरें।' : 'Fill in your details to get an accurate reading and free consultation support.' ?></p>
                </div>

                <form id="numForm" onsubmit="calculateNumerology(event)">
                    <div class="form-group">
                        <label class="form-label"><?= $lang === 'hi' ? 'पूरा नाम (स्पेलिंग के अनुसार)' : 'Full Name (Exact Spelling)' ?> <span class="text-danger">*</span></label>
                        <input type="text" id="numName" class="form-control" required placeholder="e.g. Rahul Sharma" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $lang === 'hi' ? 'व्हाट्सएप (परिणाम भेजने के लिए)' : 'WhatsApp Number' ?> <span class="text-danger">*</span></label>
                        <input type="tel" id="numMobile" class="form-control" required placeholder="e.g. 9876543210" pattern="[0-9]{10}" title="Must be a valid 10-digit mobile number">
                        <small class="text-muted" style="display:block; margin-top:4px;"><i style="opacity:0.7;">🔒 <?= $lang === 'hi' ? 'आपका डेटा 100% सुरक्षित है।' : 'Your data is 100% secure.' ?></i></small>
                    </div>
                    
                    <button type="submit" id="numSubmitBtn" class="btn btn-primary btn-full btn-lg mt-4" style="background: var(--accent-orange); border: none;">
                        <?= $lang === 'hi' ? 'मेरा अंक जानें' : 'Reveal My Number!' ?> ✨
                    </button>
                    
                    <div id="numError" class="text-danger mt-3 text-center" style="display:none; font-size: 0.9em;"></div>
                </form>
            </div>

            <!-- Result Step, hidden initially -->
            <div id="result-step" style="display: none; text-align: center;">
                <h3 style="color: var(--text-secondary); margin-bottom: var(--space-2);"><?= $lang === 'hi' ? 'आपका नाम अंक है' : 'Your Name Number Is' ?></h3>
                
                <div style="font-size: 6rem; font-weight: 900; line-height: 1; color: var(--primary); margin: var(--space-4) 0; position: relative;">
                    <span id="resNumber"></span>
                </div>
                
                <div style="background: var(--surface); border: 1px solid var(--border-color); padding: var(--space-4); border-radius: 12px; margin-bottom: var(--space-6);">
                    <div style="font-weight: 600; font-size: 1.2rem; color: var(--accent-orange); margin-bottom: 8px;">
                        <?= $lang === 'hi' ? 'शासक ग्रह: ' : 'Ruling Planet: ' ?> <span id="resPlanet"></span>
                    </div>
                    <p id="resDesc" style="margin: 0; color: var(--text-secondary);"></p>
                </div>
                
                <div style="border-top: 1px dashed var(--border-color); padding-top: var(--space-4);">
                    <h4 style="margin-bottom: var(--space-2);"><?= $lang === 'hi' ? 'क्या यह आपके जन्म अंक से मेल खाता है?' : 'Does this vibrate well with your Birth Date?' ?></h4>
                    <p class="text-sm text-muted" style="margin-bottom: var(--space-4);">
                        <?= $lang === 'hi' ? 'अंक ज्योतिष में, यदि आपका नाम अंक आपके भाग्य अंक से मेल नहीं खाता है, तो यह संघर्ष पैदा कर सकता है। हमारी प्रीमियम रिपोर्ट से गहराई से समझें।' : 'In Numerology, if your Name Number doesn\'t harmonize with your Birth Date, it can cause unnecessary struggles. Find out more!' ?>
                    </p>
                    <a href="<?= BASE_URL ?>packages" class="btn btn-primary btn-full">
                        <?= $lang === 'hi' ? 'प्रीमियम विश्लेषण देखें' : 'View Premium Analysis' ?> →
                    </a>
                </div>
            </div>

        </div>
        
    </div>
</section>

<script>
async function calculateNumerology(e) {
    e.preventDefault();
    const btn = document.getElementById('numSubmitBtn');
    const err = document.getElementById('numError');
    const name = document.getElementById('numName').value.trim();
    const mobile = document.getElementById('numMobile').value.trim();

    err.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Calculating...';

    try {
        const response = await fetch('<?= BASE_URL ?>api/numerology_calculate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, mobile })
        });

        const result = await response.json();
        
        if (result.success) {
            // Hide input, show result
            document.getElementById('input-step').style.display = 'none';
            document.getElementById('result-step').style.display = 'block';
            
            // Populate
            document.getElementById('resNumber').innerText = result.data.number;
            document.getElementById('resPlanet').innerText = result.data.planet;
            document.getElementById('resDesc').innerText = result.data.description;
            
            // Re-trigger reveal animation smoothly
            document.getElementById('result-step').animate([
                { opacity: 0, transform: 'translateY(20px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ], { duration: 600, easing: 'ease-out' });

        } else {
            err.innerText = result.message || 'Something went wrong. Please try again.';
            err.style.display = 'block';
        }
    } catch (error) {
        err.innerText = 'Network error. Please try again.';
        err.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<?= $lang === 'hi' ? 'मेरा अंक जानें' : 'Reveal My Number!' ?> ✨';
    }
}
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
