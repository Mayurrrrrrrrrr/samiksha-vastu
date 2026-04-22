<?php
/**
 * Vastu Quiz Game
 */
$pageTitle = 'Vastu Quiz | ' . SITE_NAME;
require __DIR__ . '/../../layouts/public_header.php';

$db = getDB();
$stmt = $db->query("SELECT id, question_text, option_a, option_b, option_c, option_d FROM game_questions WHERE is_active = 1 ORDER BY RAND() LIMIT 10");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$questionsJson = json_encode($questions);

$isLoggedIn = isLoggedIn();
$userName = $isLoggedIn ? currentUserName() : '';
?>

<style>
.quiz-container { max-width: 600px; margin: var(--space-12) auto; background: var(--surface); padding: var(--space-8); border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); border-top: 4px solid var(--primary); text-align: center; }
.quiz-option { display: block; width: 100%; padding: var(--space-4); margin-bottom: var(--space-3); background: var(--bg-body); border: 2px solid var(--border-color); border-radius: var(--border-radius-sm); font-size: 1.1rem; cursor: pointer; transition: all 0.2s; font-weight: 500; }
.quiz-option:hover { background: var(--primary-glow); border-color: var(--primary); }
.quiz-option.selected { background: var(--primary); color: white; border-color: var(--primary-dark); }
.quiz-scoreboard { display: flex; justify-content: space-between; margin-bottom: var(--space-6); font-weight: bold; color: var(--text-muted); }
</style>

<div class="container" style="min-height: 70vh;">
    <!-- Start Screen -->
    <div id="screen-start" class="quiz-container animate-slide-up">
        <h1 style="color: var(--primary); font-family: var(--font-devanagari); font-size: 3rem;">वास्तु प्रश्नोत्तरी</h1>
        <h2 style="margin-bottom: var(--space-6);">Test your Vastu Knowledge!</h2>
        
        <?php if (!$isLoggedIn): ?>
            <div class="form-group">
                <input type="text" id="guestName" class="form-control" placeholder="Enter your name to start..." style="text-align:center; font-size: 1.1rem;">
            </div>
        <?php endif; ?>
        
        <button class="btn btn-primary btn-lg" onclick="startQuiz()" style="padding: 1rem 3rem; font-size: 1.2rem;">Start Game 🚀</button>
        
        <div style="margin-top: var(--space-8);">
            <h3>Top Players 🏆</h3>
            <div id="leaderboard-preview" style="text-align: left; background: var(--bg-body); padding: var(--space-4); border-radius: 8px; margin-top: var(--space-2);">
                Loading leaderboard...
            </div>
        </div>
    </div>

    <!-- Quiz Screen -->
    <div id="screen-quiz" class="quiz-container" style="display: none;">
        <div class="quiz-scoreboard">
            <div>Question: <span id="qIdx">1</span>/<span id="qTotal">10</span></div>
            <div>Time: <span id="qTime">0</span>s</div>
        </div>
        
        <h2 id="qText" style="margin-bottom: var(--space-8); min-height: 80px;">Loading question...</h2>
        
        <div id="qOptions">
            <!-- Options injected via JS -->
        </div>
        
        <button id="btnNext" class="btn btn-primary btn-lg mt-8" style="display: none; width: 100%;" onclick="nextQuestion()">Next Question ➡️</button>
    </div>

    <!-- Result Screen -->
    <div id="screen-result" class="quiz-container" style="display: none;">
        <h1 style="color: var(--primary); font-size: 4rem; margin-bottom: 0;">🎉</h1>
        <h2>Quiz Completed!</h2>
        <div style="font-size: 2.5rem; font-weight: bold; margin: var(--space-4) 0; color: var(--accent-green);">
            <span id="finalScore">0</span> / <span id="finalTotal">0</span>
        </div>
        <p style="color: var(--text-muted); margin-bottom: var(--space-8);">Total Time: <span id="finalTime">0</span> seconds</p>
        
        <div style="display:flex; gap: var(--space-4); justify-content: center;">
            <button class="btn btn-primary" onclick="location.reload()">Play Again</button>
            <a href="<?= BASE_URL ?>packages" class="btn btn-outline">Explore Vastu Services</a>
        </div>
    </div>
</div>

<script>
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
let playerName = "<?= addslashes($userName) ?>";
const qs = <?= $questionsJson ?>;
let currentQ = 0;
let score = 0;
let startTime = 0;
let timerInt = null;

async function loadLeaderboard() {
    try {
        const res = await fetch('<?= BASE_URL ?>api/game/leaderboard.php');
        const data = await res.json();
        const box = document.getElementById('leaderboard-preview');
        if (data.length === 0) {
            box.innerHTML = '<p class="text-muted text-center" style="margin:0;">No entries yet. Be the first!</p>';
            return;
        }
        let html = '<ul style="padding:0; margin:0; list-style:none;">';
        data.forEach((r, idx) => {
            const medal = idx === 0 ? '🥇' : (idx === 1 ? '🥈' : (idx === 2 ? '🥉' : ''));
            const name = r.name || 'Anonymous';
            html += `<li style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <span>${medal} <strong>${name}</strong></span>
                        <span>Score: ${r.score} <small class="text-muted">(${r.total_time}s)</small></span>
                     </li>`;
        });
        html += '</ul>';
        box.innerHTML = html;
    } catch(e) {
        console.error("Leaderboard load failed");
    }
}

function startQuiz() {
    if (!isLoggedIn) {
        const gn = document.getElementById('guestName').value.trim();
        if (!gn) { alert("Please enter your name to start."); return; }
        playerName = gn;
    }
    
    if (qs.length === 0) {
        alert("No questions available at the moment.");
        return;
    }

    document.getElementById('screen-start').style.display = 'none';
    document.getElementById('screen-quiz').style.display = 'block';
    
    startTime = Date.now();
    timerInt = setInterval(() => {
        document.getElementById('qTime').innerText = Math.floor((Date.now() - startTime) / 1000);
    }, 1000);
    
    showQuestion();
}

function showQuestion() {
    const q = qs[currentQ];
    document.getElementById('qIdx').innerText = currentQ + 1;
    document.getElementById('qTotal').innerText = qs.length;
    document.getElementById('qText').innerText = q.question_text;
    
    const optsBox = document.getElementById('qOptions');
    optsBox.innerHTML = '';
    
    const opts = [
        { key: 'A', text: q.option_a },
        { key: 'B', text: q.option_b },
        { key: 'C', text: q.option_c },
        { key: 'D', text: q.option_d }
    ];
    
    opts.forEach(o => {
        const btn = document.createElement('button');
        btn.className = 'quiz-option';
        btn.innerText = o.text;
        btn.onclick = () => selectOption(o.key, btn);
        optsBox.appendChild(btn);
    });
    
    document.getElementById('btnNext').style.display = 'none';
}

function selectOption(selectedKey, btnObj) {
    // Disable all options
    const allBtns = document.getElementById('qOptions').querySelectorAll('.quiz-option');
    allBtns.forEach(b => {
        b.onclick = null;
        b.style.cursor = 'default';
        if (b === btnObj) b.classList.add('selected');
        else b.style.opacity = '0.5';
    });
    
    // Evaluate answer via basic verification visually, but score will be calculated server side on submit?
    // Actually, we pass correct answer to frontend or we score client side to make it fast. 
    // Wait, the SQL fetch didn't include correct_option! Let's score server side or modify SQL.
    // Let's modify SQL in this file to fetch correct_option, but hide it in a secure way?
    // For a simple game, client side scoring is fine but we need the correct_option.
    // Wait, we didn't fetch `correct_option` in SQL above. I must fetch it, or send array of selected answers to save_score.php.
    // Standard secure way: collect answers, submit at end.
    
    qs[currentQ].user_answer = selectedKey;
    document.getElementById('btnNext').style.display = 'block';
}

async function nextQuestion() {
    currentQ++;
    if (currentQ < qs.length) {
        showQuestion();
    } else {
        finishQuiz();
    }
}

async function finishQuiz() {
    clearInterval(timerInt);
    const totalTime = Math.floor((Date.now() - startTime) / 1000);
    
    // Prepare answers payload
    const answers = qs.map(q => ({ id: q.id, answer: q.user_answer || '' }));
    
    document.getElementById('screen-quiz').style.display = 'none';
    const resScreen = document.getElementById('screen-result');
    resScreen.style.display = 'block';
    resScreen.style.opacity = '0.5';
    
    try {
        const res = await fetch('<?= BASE_URL ?>api/game/save_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                guest_name: playerName,
                time: totalTime,
                answers: answers
            })
        });
        const data = await res.json();
        
        resScreen.style.opacity = '1';
        document.getElementById('finalScore').innerText = data.score;
        document.getElementById('finalTotal').innerText = qs.length;
        document.getElementById('finalTime').innerText = totalTime;
        
    } catch(e) {
        alert("Failed to save score.");
    }
}

document.addEventListener("DOMContentLoaded", loadLeaderboard);
</script>

<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>
