<?php
/** Games Page */
$pageTitle = t('games_title');
$db = getDB();
$quizzes = $db->query("SELECT q.*, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count, (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as attempt_count FROM quizzes q WHERE q.is_active = 1")->fetchAll();
require __DIR__ . '/../../layouts/public_header.php';
?>
<div class="page-header">
    <h1>
        <?= t('games_title') ?>
    </h1>
    <p>
        <?= t('games_subtitle') ?>
    </p>
    <div class="breadcrumb"><a href="<?= BASE_URL ?>">
            <?= t('nav_home') ?>
        </a> / <span>
            <?= t('nav_games') ?>
        </span></div>
</div>
<section class="section">
    <div class="container">
        <div class="grid grid-3">
            <!-- Quizzes -->
            <?php foreach ($quizzes as $quiz): ?>
                <div class="card reveal" style="padding:0;overflow:hidden;">
                    <div
                        style="padding:var(--space-8);background:linear-gradient(135deg,var(--secondary),var(--secondary-light));text-align:center;">
                        <div style="font-size:3rem;margin-bottom:var(--space-3);">🧠</div>
                        <h3 style="color:var(--white);">
                            <?= clean($lang === 'hi' && $quiz['title_hi'] ? $quiz['title_hi'] : $quiz['title']) ?>
                        </h3>
                    </div>
                    <div style="padding:var(--space-6);">
                        <p class="text-muted text-sm" style="margin-bottom:var(--space-4);">
                            <?= clean($lang === 'hi' && $quiz['description_hi'] ? $quiz['description_hi'] : $quiz['description']) ?>
                        </p>
                        <div class="flex gap-4 mb-4 text-sm text-muted">
                            <span>📝
                                <?= $quiz['question_count'] ?>
                                <?= $lang === 'hi' ? 'प्रश्न' : 'Questions' ?>
                            </span>
                            <span>⏱
                                <?= floor($quiz['time_limit'] / 60) ?> min
                            </span>
                            <span>👥
                                <?= $quiz['attempt_count'] ?>
                            </span>
                        </div>
                        <a href="<?= BASE_URL ?>quiz/<?= $quiz['id'] ?>" class="btn btn-primary btn-full">
                            <?= t('quiz_start') ?> →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Numerology Calculator -->
            <div class="card reveal" style="padding:0;overflow:hidden;">
                <div
                    style="padding:var(--space-8);background:linear-gradient(135deg,#e67e22,#d35400);text-align:center;">
                    <div style="font-size:3rem;margin-bottom:var(--space-3);">🔢</div>
                    <h3 style="color:var(--white);">
                        <?= t('numerology_calc') ?>
                    </h3>
                </div>
                <div style="padding:var(--space-6);">
                    <p class="text-muted text-sm" style="margin-bottom:var(--space-4);">
                        <?= t('numerology_calc_desc') ?>
                    </p>
                    <a href="<?= BASE_URL ?>numerology-calculator" class="btn btn-primary btn-full">
                        <?= t('calculate') ?> →
                    </a>
                </div>
            </div>

            <!-- Vastu Checker -->
            <div class="card reveal" style="padding:0;overflow:hidden;">
                <div
                    style="padding:var(--space-8);background:linear-gradient(135deg,#27ae60,#1abc9c);text-align:center;">
                    <div style="font-size:3rem;margin-bottom:var(--space-3);">🧭</div>
                    <h3 style="color:var(--white);">
                        <?= t('vastu_checker') ?>
                    </h3>
                </div>
                <div style="padding:var(--space-6);">
                    <p class="text-muted text-sm" style="margin-bottom:var(--space-4);">
                        <?= t('vastu_checker_desc') ?>
                    </p>
                    <a href="<?= BASE_URL ?>vastu-checker" class="btn btn-primary btn-full">
                        <?= $lang === 'hi' ? 'जांचें' : 'Check Now' ?> →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../../layouts/public_footer.php'; ?>