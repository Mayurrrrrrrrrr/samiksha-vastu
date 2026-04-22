<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vastu Wisdom Kiosk - Live Energy Grid</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #fbbf24;
            --deep-blue: #1e293b;
            --accent-blue: #3b82f6;
            --soft-bg: #f0f4f8;
        }

        body { 
            margin: 0; 
            background: var(--soft-bg); 
            color: var(--deep-blue); 
            font-family: 'Outfit', sans-serif; 
            overflow: hidden;
            /* Animated Energy Background */
            background: linear-gradient(-45deg, #f0f4f8, #e0e7ff, #fffbeb, #f0f4f8);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .grid { display: grid; grid-template-columns: 1fr 1.5fr; height: 100vh; }

        /* Left Panel - QR Branding */
        .qr-panel { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
            padding: 40px;
        }

        .qr-wrapper {
            background: white;
            padding: 30px;
            border-radius: 40px;
            box-shadow: 0 20px 50px rgba(30, 41, 59, 0.1);
            border: 10px solid var(--gold);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 20px 50px rgba(30, 41, 59, 0.1); }
            50% { transform: scale(1.02); box-shadow: 0 20px 60px rgba(251, 191, 36, 0.3); }
            100% { transform: scale(1); box-shadow: 0 20px 50px rgba(30, 41, 59, 0.1); }
        }

        /* Right Panel - Data */
        .feed-panel { padding: 60px; overflow-y: auto; }

        .section-card {
            background: white;
            border-radius: 30px;
            padding: 35px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        h2 { 
            font-size: 2rem; 
            margin-top: 0; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            color: var(--deep-blue);
        }

        .user-row { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 20px; 
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 20px;
            animation: slideIn 0.5s ease forwards;
            border: 1px solid transparent;
            transition: all 0.3s;
        }

        .user-row:hover { border-color: var(--gold); background: #fffbeb; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .otp-pill { 
            background: var(--gold); 
            color: var(--deep-blue); 
            padding: 10px 25px; 
            border-radius: 50px; 
            font-weight: 800; 
            font-size: 1.4rem;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
        }

        .score-val {
            font-weight: 800;
            color: var(--accent-blue);
            font-size: 1.2rem;
        }

        .rank-badge {
            width: 40px;
            height: 40px;
            background: var(--deep-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="grid">
        <div class="qr-panel">
            <h1 style="font-weight: 800; font-size: 2.5rem; margin-bottom: 10px; color: var(--deep-blue);">Begin Your Quest</h1>
            <p style="color: #64748b; font-size: 1.2rem; margin-bottom: 40px;">Scan to unlock your home's energy secrets</p>
            
            <div class="qr-wrapper">
                <div id="qrcode"></div>
            </div>
            
            <div style="margin-top: 50px; text-align: center;">
                <p style="font-weight: 600; color: var(--deep-blue); font-size: 1.1rem;">Scan • Verify • Discover</p>
                <p style="color: #94a3b8; font-size: 0.9rem;">The "Magic Code" will appear on the right</p>
            </div>
        </div>

        <div class="feed-panel">
            
            <div class="section-card">
                <h2 style="color: var(--accent-blue);">⚡ Waiting for Magic Code</h2>
                <div id="live-joiners">
                    <p style="color: #94a3b8;">Scanning for new participants...</p>
                </div>
            </div>

            <div class="section-card">
                <h2 style="color: var(--gold);">👑 Vastu Energy Masters</h2>
                <div id="top-scores">
                    <p style="color: #94a3b8;">Calculating top wisdom scores...</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $site_url; ?>/form.php",
            width: 280,
            height: 280,
            colorDark : "#1e293b",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        async function refresh() {
            try {
                const res = await fetch('get_updates.php');
                const data = await res.json();
                
                // Update Magic Codes (Joiners)
                const joinersDiv = document.getElementById('live-joiners');
                if (data.new_users.length > 0) {
                    joinersDiv.innerHTML = data.new_users.map(u => `
                        <div class="user-row">
                            <span style="font-weight: 600; font-size: 1.2rem;">✨ ${u.name}</span>
                            <span class="otp-pill">${u.otp}</span>
                        </div>
                    `).join('');
                } else {
                    joinersDiv.innerHTML = '<p style="color: #94a3b8; padding: 20px;">Waiting for someone to scan the code...</p>';
                }

                // Update Leaderboard
                const scoresDiv = document.getElementById('top-scores');
                if (data.top_scores.length > 0) {
                    scoresDiv.innerHTML = data.top_scores.map((u, i) => `
                        <div class="user-row">
                            <div style="display:flex; align-items:center;">
                                <div class="rank-badge" style="${i === 0 ? 'background:var(--gold); color:var(--deep-blue);' : ''}">${i+1}</div>
                                <span style="font-weight: 600;">${u.name}</span>
                            </div>
                            <span class="score-val">${u.final_score} <small>pts</small></span>
                        </div>
                    `).join('');
                } else {
                    scoresDiv.innerHTML = '<p style="color: #94a3b8; padding: 20px;">No scores recorded yet. Be the first!</p>';
                }

            } catch(e) { 
                console.error("Live update error: ", e); 
            }
        }

        // Refresh every 3 seconds for live experience
        setInterval(refresh, 3000);
        refresh(); // Initial load
    </script>
</body>
</html>