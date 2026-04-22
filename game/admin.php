<?php
session_start();
// This includes your config.php connection and the Vastu branding
include 'header.php'; 

// 1. Authentication Logic
if (isset($_POST['login'])) {
    if ($_POST['user'] == 'admin' && $_POST['pass'] == 'vastu2026') {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = "Invalid Credentials.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// 2. Administrative Actions (Protected by Session)
if (isset($_SESSION['admin_auth'])) {
    
    // UPDATE CORRECT ANSWER Logic
    if (isset($_POST['update_correct'])) {
        $q_id = intval($_POST['q_id']);
        $c_id = intval($_POST['correct_option_id']);
        $conn->query("UPDATE questions SET correct_option_id = $c_id WHERE id = $q_id");
        $status = "Question #$q_id updated.";
    }
    
    // RESET LEADERBOARD Logic
    if (isset($_POST['reset_quiz'])) {
        $conn->query("TRUNCATE TABLE users");
        $status = "Leaderboard Cleared.";
    }
    
    // PURGE BANK Logic
    if (isset($_POST['purge_questions'])) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        $conn->query("TRUNCATE TABLE choices");
        $conn->query("TRUNCATE TABLE questions");
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        $status = "Bank Purged.";
    }
    
    // ADD NEW QUESTION Logic
    if (isset($_POST['add_question'])) {
        $q_text = $conn->real_escape_string($_POST['question_text']);
        $image = "";
        if (!empty($_FILES['q_image']['name'])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $image = $target_dir . time() . "_" . basename($_FILES["q_image"]["name"]);
            move_uploaded_file($_FILES["q_image"]["tmp_name"], $image);
        }
        $conn->query("INSERT INTO questions (question_text, question_image) VALUES ('$q_text', '$image')");
        $q_id = $conn->insert_id;
        foreach ($_POST['options'] as $idx => $opt) {
            $opt = $conn->real_escape_string($opt);
            $conn->query("INSERT INTO choices (question_id, choice_text) VALUES ('$q_id', '$opt')");
            if ($idx == $_POST['correct_index']) {
                $c_id = $conn->insert_id;
                $conn->query("UPDATE questions SET correct_option_id = $c_id WHERE id = $q_id");
            }
        }
        $status = "New question saved.";
    }

    // 3. Data Retrieval for Dashboard
    $users = $conn->query("SELECT * FROM users ORDER BY final_score DESC");
    $questions = $conn->query("SELECT q.id, q.question_text, q.correct_option_id, GROUP_CONCAT(c.choice_text SEPARATOR '|') as opts, GROUP_CONCAT(c.id SEPARATOR '|') as ids FROM questions q LEFT JOIN choices c ON q.id = c.question_id GROUP BY q.id");
}

// --- VIEW LOGIC ---
if (!isset($_SESSION['admin_auth'])): ?>
    <div style="display:flex; justify-content:center; padding-top:100px;">
        <form method="POST" class="card" style="width:320px; text-align:center;">
            <h2>Admin Login</h2>
            <?php if(isset($error)): ?><p style="color:#ef4444"><?= $error ?></p><?php endif; ?>
            <input type="text" name="user" placeholder="Admin" required style="width:100%; padding:10px; margin:10px 0;">
            <input type="password" name="pass" placeholder="Password" required style="width:100%; padding:10px; margin:10px 0;">
            <button type="submit" name="login" style="width:100%; padding:12px; background:var(--primary); color:white; border:none; border-radius:8px; cursor:pointer;">Login</button>
        </form>
    </div>
<?php else: ?>
    <script>
    // Automates PDF generation and opens WhatsApp chat
    function sendReport(userId, name, phone) {
        window.open('generate_pdf.php?id=' + userId, '_blank');
        const msg = `Hi ${name}, thanks for visiting our Kiosk- Fun with Vastu, your test results with correct answers attached. We will send you new games which can help you learn more vastu concepts easiely. Do connect with us for any help or concept clarity.`;
        setTimeout(() => {
            window.open(`https://wa.me/91${phone}?text=${encodeURIComponent(msg)}`, '_blank');
        }, 500);
    }

    // New function to ONLY download/view the PDF
    function downloadOnly(userId) {
        window.open('generate_pdf.php?id=' + userId, '_blank');
    }
    </script>

    <div style="padding: 20px; max-width: 1400px; margin: auto;">
        <?php if(isset($status)): ?><div style="background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;"><?= $status ?></div><?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="color:var(--primary)">Vastu Command Center</h2>
            <a href="?logout=1" style="color:#64748b; font-weight:bold; text-decoration:none;">Logout</a>
        </div>

        <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
            <div class="card">
                <h3>Live Participants</h3>
                <div style="max-height:600px; overflow-y:auto;">
                    <table width="100%" style="border-collapse:collapse; font-size:13px;">
                        <tr style="background:#f1f5f9; text-align:left;">
                            <th style="padding:10px;">Name</th>
                            <th style="padding:10px;">Score</th>
                            <th style="padding:10px;">Actions</th>
                        </tr>
                        <?php while($u = $users->fetch_assoc()): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:10px;"><strong><?= $u['name'] ?></strong></td>
                            <td style="padding:10px; color:var(--accent); font-weight:bold;"><?= $u['final_score'] ?></td>
                            <td style="padding:10px; display:flex; gap:5px;">
                                <button onclick="downloadOnly(<?= $u['id'] ?>)" style="padding:6px 10px; background:#64748b; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size:10px;">📄 View PDF</button>
                                
                                <button onclick="sendReport(<?= $u['id'] ?>, '<?= addslashes($u['name']) ?>', '<?= $u['phone_number'] ?>')" style="padding:6px 10px; background:#25d366; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size:10px;">🟢 WhatsApp PDF</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>Vastu Bank (Edit Answers)</h3>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php while($q = $questions->fetch_assoc()): 
                        $opts = explode('|', $q['opts']); $ids = explode('|', $q['ids']); ?>
                        <div style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
                            <form method="POST">
                                <input type="hidden" name="q_id" value="<?= $q['id'] ?>">
                                <strong style="font-size:14px;"><?= $q['question_text'] ?></strong>
                                <div style="font-size:12px; margin-top:5px;">
                                    <?php foreach($opts as $idx => $o): ?>
                                        <label style="display:block; padding:3px 0; cursor:pointer; <?= ($ids[$idx] == $q['correct_option_id']) ? 'color:#22c55e; font-weight:bold;' : '' ?>">
                                            <input type="radio" name="correct_option_id" value="<?= $ids[$idx] ?>" <?= ($ids[$idx] == $q['correct_option_id']) ? 'checked' : '' ?>> <?= $o ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" name="update_correct" style="margin-top:5px; font-size:10px; cursor:pointer;">Update Wisdom</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <hr style="margin:20px 0;">
                
                <h4 style="margin:0 0 10px 0;">Add New Question</h4>
                <form method="POST" enctype="multipart/form-data">
                    <textarea name="question_text" placeholder="Question Text" required style="width:100%; height:50px; padding:10px; margin-bottom:10px;"></textarea>
                    <input type="file" name="q_image" accept="image/*" style="margin-bottom:10px;"><br>
                    <div style="background:#f1f5f9; padding:10px; border-radius:8px;">
                        <input type="text" name="options[]" placeholder="Option 1" required style="width:85%; padding:6px; margin-bottom:5px;"> <input type="radio" name="correct_index" value="0" checked><br>
                        <input type="text" name="options[]" placeholder="Option 2" required style="width:85%; padding:6px; margin-bottom:5px;"> <input type="radio" name="correct_index" value="1"><br>
                        <input type="text" name="options[]" placeholder="Option 3" required style="width:85%; padding:6px;"> <input type="radio" name="correct_index" value="2">
                    </div>
                    <button type="submit" name="add_question" style="width:100%; padding:12px; background:var(--accent); color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; margin-top:10px;">Save Question</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
</body></html>