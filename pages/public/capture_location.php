<?php
/**
 * Capture Location Public Page
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$token = $_GET['token'] ?? '';
$submissionId = $_GET['submission_id'] ?? '';

$pageTitle = $lang === 'hi' ? 'स्थान साझा करें' : 'Share Location';

require __DIR__ . '/../../layouts/public_header.php';
?>

<div class="container" style="padding: 40px 15px; max-width: 600px; margin: 0 auto; text-align: center;">
    <div class="card" style="padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        
        <div id="status-icon" style="font-size: 60px; margin-bottom: 20px; color: var(--primary);">📍</div>
        
        <h2 id="status-title" style="margin-bottom: 15px;">
            <?= $lang === 'hi' ? 'अपना स्थान साझा करें' : 'Share Your Location' ?>
        </h2>
        
        <p id="status-text" style="color: var(--text-muted); margin-bottom: 30px; font-size: 1.1em; line-height: 1.6;">
            <?= $lang === 'hi' ? 'वास्तु विश्लेषण के लिए आपके सटीक अक्षांश और देशांतर की आवश्यकता है। कृपया नीचे दिए गए बटन पर क्लिक करें और अनुमति दें।' : 'For an accurate Vastu analysis, we need your exact coordinates. Please click the button below and "Allow" location access.' ?>
        </p>

        <button id="btn-share" class="btn btn-primary" style="font-size: 1.1em; padding: 12px 30px; border-radius: 30px; cursor: pointer;">
            <?= $lang === 'hi' ? 'मेरा स्थान साझा करें' : 'Share My Location' ?>
        </button>

        <div id="loader" style="display: none; margin-top: 20px;">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: var(--primary);">
                <?= $lang === 'hi' ? 'स्थान प्राप्त किया जा रहा है...' : 'Fetching location...' ?>
            </p>
        </div>

    </div>
</div>

<style>
.spinner {
    width: 40px;
    height: 40px;
    margin: 0 auto;
    border: 4px solid rgba(var(--primary-rgb), 0.2);
    border-radius: 50%;
    border-top-color: var(--primary);
    animation: spin 1s ease-in-out infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnShare = document.getElementById('btn-share');
    const loader = document.getElementById('loader');
    const statusIcon = document.getElementById('status-icon');
    const statusTitle = document.getElementById('status-title');
    const statusText = document.getElementById('status-text');

    const token = '<?= htmlspecialchars($token, ENT_QUOTES) ?>';
    const submissionId = '<?= htmlspecialchars($submissionId, ENT_QUOTES) ?>';

    btnShare.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('<?= $lang === "hi" ? "आपका ब्राउज़र स्थान साझा करने का समर्थन नहीं करता है।" : "Geolocation is not supported by your browser." ?>');
            return;
        }

        btnShare.style.display = 'none';
        loader.style.display = 'block';

        navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    });

    function successCallback(position) {
        const payload = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            token: token
        };

        if (submissionId) {
            payload.submission_id = submissionId;
        }

        fetch('<?= BASE_URL ?>api/update_location.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.success) {
                statusIcon.textContent = '✅';
                statusIcon.style.color = 'var(--accent-green)';
                statusTitle.textContent = '<?= $lang === "hi" ? "स्थान सफलतापूर्वक सहेजा गया!" : "Location Saved Successfully!" ?>';
                statusText.innerHTML = '<?= $lang === "hi" ? "धन्यवाद। आपका स्थान हमारे सिस्टम में अपडेट कर दिया गया है। अब आप इस टैब को बंद कर सकते हैं।" : "Thank you. Your location has been securely synced with our system. You may now close this page." ?><br><br><small>' + 
                                       'Lat: ' + position.coords.latitude.toFixed(6) + ', Lng: ' + position.coords.longitude.toFixed(6) + '</small>';
            } else {
                showError(data.message || 'Error occurred');
            }
        })
        .catch(err => {
            loader.style.display = 'none';
            showError('Network error occurred. Please try again.');
        });
    }

    function errorCallback(error) {
        loader.style.display = 'none';
        btnShare.style.display = 'inline-block';
        
        let msg = '<?= $lang === "hi" ? "स्थान प्राप्त करने में विफल रहा।" : "Failed to retrieve location." ?>';
        if (error.code === error.PERMISSION_DENIED) {
            msg = '<?= $lang === "hi" ? "स्थान अनुमति अस्वीकृत कर दी गई।" : "Location access was denied. Please allow location permissions in your browser and try again." ?>';
        }
        showError(msg);
    }

    function showError(msg) {
        statusIcon.textContent = '❌';
        statusIcon.style.color = 'red';
        statusText.textContent = msg;
    }
});
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
