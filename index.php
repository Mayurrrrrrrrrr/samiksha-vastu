<?php
/**
 * Front Controller / Router - Vastu Samiksha
 */

require_once __DIR__ . '/includes/auth.php';

// Get the route
$route = trim($_GET['route'] ?? '', '/');
if (empty($route))
    $route = 'home';

// Serve dynamic sitemap
if ($route === 'sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}

// Track page views (non-API)
if (!str_starts_with($route, 'api/') && !str_starts_with($route, 'assets/')) {
    // Simple view tracking could be added here
}

// Route mapping
$routes = [
    // Public pages
    'home' => 'pages/public/home.php',
    'about' => 'pages/public/about.php',
    'services' => 'pages/public/services.php',
    'blogs' => 'pages/public/blogs.php',
    'blog' => 'pages/public/blog_detail.php',
    'videos' => 'pages/public/videos.php',
    'ebooks' => 'pages/public/ebooks.php',
    'contact' => 'pages/public/contact.php',
    'login' => 'pages/public/login.php',
    'register' => 'pages/public/register.php',
    'logout' => 'pages/public/logout.php',
    'games' => 'pages/public/games.php',
    'quiz' => 'pages/public/quiz.php',
    'numerology-calculator' => 'pages/public/numerology_calc.php',
    'vastu-checker' => 'pages/public/vastu_checker.php',
    'questions' => 'pages/public/questions.php',
    'book-appointment' => 'pages/public/book_appointment.php',
    'intake-form' => 'pages/user/intake_form.php',
    'capture_location' => 'pages/public/capture_location.php',
    'public/capture_location.php' => 'pages/public/capture_location.php',

    // User pages
    'user/dashboard' => 'pages/user/dashboard.php',
    'user/submit' => 'pages/user/submit_requirement.php',
    'user/submissions' => 'pages/user/my_submissions.php',
    'user/consultation' => 'pages/user/consultation_detail.php',
    'user/questions' => 'pages/user/my_questions.php',
    'user/ask' => 'pages/user/ask_question.php',
    'user/chat' => 'pages/user/chat.php',
    'user/profile' => 'pages/user/profile.php',

    // Consultant pages
    'consultant/dashboard' => 'pages/consultant/dashboard.php',
    'consultant/blogs' => 'pages/consultant/blogs.php',
    'consultant/submissions' => 'pages/consultant/submissions.php',
    'consultant/submission' => 'pages/consultant/submission_detail.php',
    'consultant/questions' => 'pages/consultant/questions.php',
    'consultant/chat' => 'pages/consultant/chat.php',
    'consultant/contacts' => 'pages/consultant/contacts.php',
    'consultant/videos' => 'pages/consultant/videos.php',
    'consultant/ebooks' => 'pages/consultant/ebooks.php',
    'consultant/quizzes' => 'pages/consultant/quizzes.php',
    'consultant/testimonials' => 'pages/consultant/testimonials.php',
    'consultant/users' => 'pages/consultant/users.php',

    // API endpoints
    'api/chat' => 'api/chat.php',
    'api/quiz' => 'api/quiz.php',
    'api/submission' => 'api/submission.php',
    'api/newsletter' => 'api/newsletter.php',
];

// Handle blog detail with slug
if (preg_match('/^blog\/(.+)$/', $route, $matches)) {
    $_GET['slug'] = $matches[1];
    $route = 'blog';
}

// Handle quiz with ID
if (preg_match('/^quiz\/(\d+)$/', $route, $matches)) {
    $_GET['id'] = $matches[1];
    $route = 'quiz';
}

// Resolve route
if (isset($routes[$route])) {
    $file = __DIR__ . '/' . $routes[$route];
    if (file_exists($file)) {
        require $file;
    } else {
        http_response_code(404);
        require __DIR__ . '/pages/public/404.php';
    }
} else {
    http_response_code(404);
    require __DIR__ . '/pages/public/404.php';
}
