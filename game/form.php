<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Vastu Quest</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; padding: 20px; }
        .card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 400px; margin: auto; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #1e293b; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card" id="main-box">
        <h2 style="text-align: center; color: #1e293b;">🕉️ Vastu Wisdom</h2>
        <form id="regForm">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="number" name="phone" placeholder="WhatsApp Number" required>
            <button type="submit">Get My Magic Code</button>
        </form>
    </div>

    <script>
        const regForm = document.getElementById('regForm');
        if (regForm) {
            regForm.onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const userPhone = formData.get('phone');
                
                const res = await fetch('register.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if(data.status === 'success') {
                    document.getElementById('main-box').innerHTML = `
                        <h3>Verification</h3>
                        <p>Enter the 4-digit code shown next to your name on the big screen.</p>
                        <input type="number" id="otp_input" placeholder="Enter Code">
                        <button onclick="verifyCode('${userPhone}')">Verify & Start</button>`;
                }
            };
        }

        async function verifyCode(phone) {
            const otp = document.getElementById('otp_input').value;
            const res = await fetch('verify_otp.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: "phone=" + phone + "&otp=" + otp
            });
            const data = await res.json();
            if(data.status === 'success') {
                localStorage.setItem('userPhone', phone);
                window.location.href = 'quiz.php';
            } else { alert("Invalid Code!"); }
        }
    </script>
</body>
</html>