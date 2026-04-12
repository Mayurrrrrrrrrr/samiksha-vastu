<?php
/** Quiz Page */
$db = getDB();
$quizId = intval($_GET['id'] ?? 0);
if (!$quizId) {
    header('Location: ' . BASE_URL . 'games');
    exit;
}

$quiz = $db->prepare("SELECT * FROM quizzes WHERE id = ? AND is_active = 1");
$quiz->execute([$quizId]);
$quiz = $quiz->fetch();
if (!$quiz) {
    header('Location: ' . BASE_URL . 'games');
    exit;
}

$questions = $db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY sort_order");
$questions->execute([$quizId]);
$questions = $questions->fetchAll();

$leaderboard = $db->prepare("SELECT player_name, score, total_points, time_taken, completed_at FROM quiz_attempts WHERE quiz_id = ? ORDER BY score DESC, time_taken ASC LIMIT 10");
$leaderboard->execute([$quizId]);
$leaderboard = $leaderboard->fetchAll();

$pageTitle = $lang === 'hi' && $quiz['title_hi'] ? $quiz['title_hi'] : $quiz['title'];
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= clean($pageTitle) ?>
    </h1>
    <p>
        <?= clean($lang === 'hi' && $quiz['description_hi'] ? $quiz['description_hi'] : $quiz['description']) ?>
    </p>
</div>

<section class="section">
    <div class="container container-narrow">
        <!-- Quiz Start Screen -->
        <div id="quizStart" class="text-center">
            <div style="font-size:5rem;margin-bottom:var(--space-4);">🧠</div>
            <p class="text-muted mb-8">
                <?= count($questions) ?>
                <?= $lang === 'hi' ? 'प्रश्न' : 'Questions' ?> •
                <?= floor($quiz['time_limit'] / 60) ?>
                <?= $lang === 'hi' ? 'मिनट' : 'minutes' ?>
            </p>
            <div class="form-group" style="max-width:300px;margin:0 auto var(--space-6);">
                <input type="text" id="playerName" class="form-control"
                    placeholder="<?= $lang === 'hi' ? 'अपना नाम दर्ज करें' : 'Enter your name' ?>"
                    value="<?= isLoggedIn() ? clean(currentUserName()) : '' ?>">
            </div>
            <button class="btn btn-primary btn-lg" onclick="startQuiz()">
                <?= t('quiz_start') ?> →
            </button>
        </div>

        <!-- Quiz Questions -->
        <div id="quizPlay" style="display:none;">
            <div class="flex-between mb-8">
                <span id="questionCounter" class="font-bold"></span>
                <span id="timer" class="badge badge-warning"
                    style="font-size:var(--font-size-lg);padding:var(--space-2) var(--space-4);">⏱
                    <?= floor($quiz['time_limit'] / 60) ?>:00
                </span>
            </div>
            <div id="questionArea"></div>
        </div>

        <!-- Quiz Result -->
        <div id="quizResult" style="display:none;">
            <div class="calc-result">
                <div style="font-size:3rem;margin-bottom:var(--space-4);">🎉</div>
                <h2 style="color:var(--white);margin-bottom:var(--space-4);">
                    <?= t('quiz_score') ?>
                </h2>
                <div class="calc-number" id="finalScore">0</div>
                <p id="scoreText" style="color:rgba(255,255,255,0.7);"></p>
                <div style="margin-top:var(--space-6);display:flex;gap:var(--space-4);justify-content:center;">
                    <button onclick="location.reload()" class="btn btn-outline-light">
                        <?= $lang === 'hi' ? 'फिर से खेलें' : 'Play Again' ?>
                    </button>
                    <a href="javascript:void(0)" class="btn btn-ghost share-btn" data-platform="whatsapp"
                        data-title="<?= $lang === 'hi' ? 'मैंने वास्तु समीक्षा प्रश्नोत्तरी खेली!' : 'I played Vastu Samiksha Quiz!' ?>">
                        <?= t('share') ?> 💬
                    </a>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <?php if (!empty($leaderboard)): ?>
            <div style="margin-top:var(--space-12);">
                <h3 class="mb-4">
                    <?= t('quiz_leaderboard') ?> 🏆
                </h3>
                <div class="card" style="overflow:hidden;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:var(--bg-section);">
                                <th style="padding:var(--space-3) var(--space-4);text-align:left;">#</th>
                                <th style="padding:var(--space-3) var(--space-4);text-align:left;">
                                    <?= $lang === 'hi' ? 'नाम' : 'Name' ?>
                                </th>
                                <th style="padding:var(--space-3) var(--space-4);text-align:center;">
                                    <?= $lang === 'hi' ? 'स्कोर' : 'Score' ?>
                                </th>
                                <th style="padding:var(--space-3) var(--space-4);text-align:center;">⏱</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard as $i => $entry): ?>
                                <tr style="border-top:1px solid var(--border-color);">
                                    <td style="padding:var(--space-3) var(--space-4);">
                                        <?= ['🥇', '🥈', '🥉'][$i] ?? ($i + 1) ?>
                                    </td>
                                    <td style="padding:var(--space-3) var(--space-4);font-weight:600;">
                                        <?= clean($entry['player_name'] ?? 'Anonymous') ?>
                                    </td>
                                    <td style="padding:var(--space-3) var(--space-4);text-align:center;"><span
                                            class="badge badge-primary">
                                            <?= $entry['score'] ?>/
                                            <?= $entry['total_points'] ?>
                                        </span></td>
                                    <td
                                        style="padding:var(--space-3) var(--space-4);text-align:center;color:var(--text-muted);">
                                        <?= floor($entry['time_taken'] / 60) ?>:
                                        <?= str_pad($entry['time_taken'] % 60, 2, '0', STR_PAD_LEFT) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const questions = <?= json_encode(array_map(function ($q) use ($lang) {
        return [
            'id' => $q['id'],
            'question' => ($lang === 'hi' && $q['question_hi']) ? $q['question_hi'] : $q['question'],
            'options' => json_decode(($lang === 'hi' && $q['options_hi']) ? $q['options_hi'] : $q['options']),
            'correct' => $q['correct_answer'],
            'explanation' => ($lang === 'hi' && $q['explanation_hi']) ? $q['explanation_hi'] : $q['explanation'],
            'points' => $q['points']
        ];
    }, $questions)) ?>;

    let currentQ = 0, score = 0, totalPoints = 0, answers = {}, timerInterval, timeLeft = <?= $quiz['time_limit'] ?>;
    const lang = '<?= $lang ?>';

    function startQuiz() {
        const name = document.getElementById('playerName').value.trim();
        if (!name) { showToast(lang === 'hi' ? 'कृपया अपना नाम दर्ज करें' : 'Please enter your name', 'warning'); return; }
        document.getElementById('quizStart').style.display = 'none';
        document.getElementById('quizPlay').style.display = 'block';
        showQuestion();
        timerInterval = setInterval(updateTimer, 1000);
    }

    function updateTimer() {
        timeLeft--;
        const min = Math.floor(timeLeft / 60), sec = timeLeft % 60;
        document.getElementById('timer').textContent = `⏱ ${min}:${String(sec).padStart(2, '0')}`;
        if (timeLeft <= 0) { clearInterval(timerInterval); finishQuiz(); }
    }

    function showQuestion() {
        const q = questions[currentQ];
        document.getElementById('questionCounter').textContent = `${lang === 'hi' ? 'प्रश्न' : 'Question'} ${currentQ + 1}/${questions.length}`;
        let html = `<div class="card" style="padding:var(--space-8);">
        <h3 style="margin-bottom:var(--space-6);">${q.question}</h3>
        <div style="display:flex;flex-direction:column;gap:var(--space-3);">`;
        q.options.forEach((opt, i) => {
            html += `<button class="btn btn-outline btn-full" style="justify-content:flex-start;text-align:left;" onclick="selectAnswer(${i})" id="opt${i}">${String.fromCharCode(65 + i)}. ${opt}</button>`;
        });
        html += `</div><div id="feedback" style="margin-top:var(--space-4);display:none;"></div></div>`;
        document.getElementById('questionArea').innerHTML = html;
    }

    function selectAnswer(idx) {
        const q = questions[currentQ];
        totalPoints += q.points;
        const correct = idx === q.correct;
        if (correct) score += q.points;
        answers[q.id] = idx;

        document.querySelectorAll('#questionArea button').forEach((btn, i) => {
            btn.disabled = true;
            if (i === q.correct) btn.style.background = '#27ae60', btn.style.color = '#fff', btn.style.borderColor = '#27ae60';
            else if (i === idx && !correct) btn.style.background = '#c0392b', btn.style.color = '#fff', btn.style.borderColor = '#c0392b';
        });

        const fb = document.getElementById('feedback');
        fb.style.display = 'block';
        fb.innerHTML = `<div class="flash-message flash-${correct ? 'success' : 'error'}" style="position:static;transform:none;">${correct ? (lang === 'hi' ? '✓ सही!' : '✓ Correct!') : (lang === 'hi' ? '✗ गलत!' : '✗ Wrong!')} ${q.explanation}</div>`;

        setTimeout(() => {
            currentQ++;
            if (currentQ < questions.length) showQuestion();
            else finishQuiz();
        }, 2000);
    }

    function finishQuiz() {
        clearInterval(timerInterval);
        document.getElementById('quizPlay').style.display = 'none';
        document.getElementById('quizResult').style.display = 'block';
        document.getElementById('finalScore').textContent = score + '/' + totalPoints;
        const pct = Math.round((score / totalPoints) * 100);
        document.getElementById('scoreText').textContent = pct >= 80 ? (lang === 'hi' ? 'शानदार! आप वास्तु विशेषज्ञ हैं!' : 'Excellent! You are a Vastu expert!') : pct >= 50 ? (lang === 'hi' ? 'अच्छा! और अभ्यास करें!' : 'Good! Keep learning!') : (lang === 'hi' ? 'और पढ़ें और फिर से कोशिश करें!' : 'Read more and try again!');

        // Save attempt
        fetch('<?= BASE_URL ?>api/quiz', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'save_attempt',
                quiz_id: <?= $quizId ?>,
                player_name: document.getElementById('playerName').value,
                score, total_points: totalPoints,
                answers, time_taken: <?= $quiz['time_limit'] ?> - timeLeft
        })
        }).catch(() => { });
    }
</script>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>