<?php include 'header.php'; ?>
<style>
    #quiz-container { max-width: 500px; margin: 20px auto; padding: 15px; perspective: 1000px; }
    .card { background: white; padding: 25px; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: 4px solid var(--gold); transition: transform 0.6s; transform-style: preserve-3d; }
    .energy-meter-bg { background: #e2e8f0; height: 12px; border-radius: 10px; margin-bottom: 25px; overflow: hidden; border: 1px solid #cbd5e1; }
    #energy-fill { background: linear-gradient(90deg, #fbbf24, #f59e0b); width: 0%; height: 100%; transition: width 0.5s ease-in-out; }
    .q-img { width: 100%; max-height: 180px; object-fit: contain; border-radius: 15px; margin-bottom: 20px; display: none; }
    .opt-btn { width: 100%; padding: 18px; margin: 8px 0; border: 2px solid #e2e8f0; border-radius: 15px; background: #fff; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: all 0.2s; color: var(--primary); }
    .opt-btn:hover { border-color: var(--gold); background: #fffbeb; transform: translateY(-2px); }
    .correct-flash { background: #dcfce7 !important; border-color: #22c55e !important; }
    .bounce-in { animation: fadeIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div id="quiz-container">
    <div class="energy-meter-bg">
        <div id="energy-fill"></div>
    </div>
    
    <div id="game-ui" class="card bounce-in">
        <div id="progress-text" style="color: #64748b; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;"></div>
        <img id="q-image" class="q-img" src="">
        <h2 id="q-text" style="color: var(--primary); font-size: 1.4rem; line-height: 1.4;">Summoning Wisdom...</h2>
        <div id="options-grid" style="margin-top: 20px;"></div>
    </div>
</div>

<script>
    let allQuestions = [];
    let questPool = [];
    let currentIdx = 0;
    let score = 0;
    const phone = localStorage.getItem('userPhone');

    async function startQuest() {
        try {
            const res = await fetch('get_questions.php');
            allQuestions = await res.json();
            
            // Randomly pick 15 questions for variety
            questPool = allQuestions.sort(() => 0.5 - Math.random()).slice(0, 15);
            
            if (questPool.length > 0) {
                renderStep();
            } else {
                document.getElementById('q-text').innerText = "Question bank is empty.";
            }
        } catch (e) {
            document.getElementById('q-text').innerText = "Energy grid connection failed.";
        }
    }

    function renderStep() {
        const q = questPool[currentIdx];
        const progress = ((currentIdx) / questPool.length) * 100;
        document.getElementById('energy-fill').style.width = progress + "%";
        document.getElementById('progress-text').innerText = `Step ${currentIdx + 1} of ${questPool.length}`;
        document.getElementById('q-text').innerText = q.text;
        
        const img = document.getElementById('q-image');
        if (q.image) {
            img.src = q.image;
            img.style.display = 'block';
        } else {
            img.style.display = 'none';
        }

        const container = document.getElementById('options-grid');
        container.innerHTML = q.options.map((opt, i) => 
            `<button class="opt-btn" onclick="checkAnswer(this, ${q.ids[i]}, ${q.correct})">${opt}</button>`
        ).join('');
    }

    async function checkAnswer(btn, chosenId, correctId) {
        const buttons = document.querySelectorAll('.opt-btn');
        buttons.forEach(b => b.disabled = true);

        if (chosenId == correctId) {
            score += 100;
            btn.classList.add('correct-flash');
        } else {
            btn.style.borderColor = "#ef4444";
        }

        setTimeout(() => {
            currentIdx++;
            if (currentIdx < questPool.length) {
                renderStep();
            } else {
                finalizeQuest();
            }
        }, 600);
    }

    async function finalizeQuest() {
        document.getElementById('energy-fill').style.width = "100%";
        
        // Save the results to the database
        await fetch('submit_score.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `phone=${phone}&score=${score}`
        });

        document.getElementById('game-ui').innerHTML = `
            <div style="padding: 10px; text-align: center;">
                <h1 style="color: #059669;">Quest Complete! 🏆</h1>
                <p style="font-size: 1.2rem; margin: 20px 0;">Final Score: <strong style="color:var(--gold); font-size:1.8rem;">${score}</strong></p>
                <div style="background:#f8fafc; border-radius:15px; padding:15px; margin-bottom:20px;">
                    <p>Rank achieved:<br><strong>${score >= 1200 ? 'Vastu Master Architect' : 'Energy Explorer'}</strong></p>
                </div>
                <button onclick="location.href='http://vastu.yuktaa.com/'" class="opt-btn" style="background:var(--primary); color:white; border:none;">Visit Vastu Portal</button>
            </div>`;
    }

    startQuest();
</script>
</body></html>