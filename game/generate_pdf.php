<?php
include 'config.php';

$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if (!$user) { die("Participant not found."); }

// Fetch Wisdom Guide
$wisdom = $conn->query("SELECT q.question_text, c.choice_text FROM questions q JOIN choices c ON q.correct_option_id = c.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vastu Wisdom Report - <?= $user['name'] ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f8fafc; }
        .page { background: white; width: 210mm; min-height: 297mm; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .poster-page { height: 297mm; display: flex; align-items: center; justify-content: center; overflow: hidden; page-break-after: always; }
        .report-page { padding: 50px; }
        img.full { width: 100%; height: 100%; object-fit: contain; }
        .header { border-bottom: 5px solid #fbbf24; padding-bottom: 10px; margin-bottom: 30px; text-align: center; }
        @media print {
            .no-print { display: none; }
            .page { box-shadow: none; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background:#1e293b; color:white; padding:15px; text-align:center;">
        <strong>Wisdom Report Ready!</strong> 
        <button onclick="window.print()" style="margin-left:20px; padding:10px 20px; background:#25d366; border:none; color:white; border-radius:5px; cursor:pointer; font-weight:bold;">Click to Print/Save as PDF</button>
    </div>

    <div class="page">
        <div class="poster-page">
            <img src="vastupage.jpg" class="full">
        </div>

        <div class="report-page">
            <div class="header">
                <h1 style="margin:0;">Vastu Wisdom Report</h1>
                <p>Generated for <strong><?= $user['name'] ?></strong></p>
            </div>

            <div style="background:#f1f5f9; padding:20px; border-radius:15px; margin-bottom:30px;">
                <p>Energy Score: <strong><?= $user['final_score'] ?> Pts</strong></p>
                <p>Vastu Title: <strong><?= $user['badge'] ?> <?= $user['persona'] ?></strong></p>
            </div>

            <h3>The Wisdom Guide</h3>
            <?php while($w = $wisdom->fetch_assoc()): ?>
                <div style="margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;">
                    <p style="font-size:14px; margin:0;"><strong>Q: <?= $w['question_text'] ?></strong></p>
                    <p style="font-size:13px; color:#16a34a; margin:3px 0;">✓ Correct: <?= $w['choice_text'] ?></p>
                </div>
            <?php endwhile; ?>
            
            <p style="margin-top:50px; text-align:center; font-size:12px; color:#94a3b8;">
                Fun with Vastu • Contact: 7000208511
            </p>
        </div>
    </div>
</body>
</html>