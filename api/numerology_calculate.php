<?php
/**
 * Free Numerology Calculator API
 * Calculates Chaldean Numerology Name Number and saves leads.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
$mobile = trim($input['mobile'] ?? '');

if (empty($name) || empty($mobile)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name and mobile number are required.']);
    exit;
}

// Chaldean Numerology logic
function getChaldeanNumber($str) {
    $map = [
        'A'=>1, 'B'=>2, 'C'=>3, 'D'=>4, 'E'=>5, 'F'=>8, 'G'=>2, 'H'=>5,
        'I'=>1, 'J'=>1, 'K'=>2, 'L'=>3, 'M'=>4, 'N'=>5, 'O'=>7, 'P'=>8,
        'Q'=>1, 'R'=>2, 'S'=>3, 'T'=>4, 'U'=>6, 'V'=>6, 'W'=>6, 'X'=>6,
        'Y'=>1, 'Z'=>7
    ];
    $cleanStr = strtoupper(preg_replace('/[^a-zA-Z]/', '', $str));
    $sum = 0;
    for ($i = 0; $i < strlen($cleanStr); $i++) {
        $sum += $map[$cleanStr[$i]] ?? 0;
    }
    
    // Reduce logic
    while ($sum > 9 && $sum !== 11 && $sum !== 22 && $sum !== 33) {
        $newSum = 0;
        $digits = str_split((string)$sum);
        foreach ($digits as $d) {
            $newSum += (int)$d;
        }
        $sum = $newSum;
    }
    return $sum;
}

$result_number = getChaldeanNumber($name);

// Define rulings and brief interpretations
$rulings = [
    1 => ['planet' => 'Sun (सूर्य)', 'desc' => 'Independent, strong-willed, and a natural leader. You attract success through ambition.'],
    2 => ['planet' => 'Moon (चंद्र)', 'desc' => 'Sensitive, diplomatic, and intuitive. You excel in partnerships and bringing peace.'],
    3 => ['planet' => 'Jupiter (गुरु)', 'desc' => 'Creative, expressive, and optimistic. Your communication skills are your biggest asset.'],
    4 => ['planet' => 'Uranus / Rahu (राहु)', 'desc' => 'Practical, disciplined, and hard-working. You build solid foundations for the future.'],
    5 => ['planet' => 'Mercury (बुध)', 'desc' => 'Dynamic, adventurous, and freedom-loving. Change is your constant companion.'],
    6 => ['planet' => 'Venus (शुक्र)', 'desc' => 'Loving, responsible, and harmonious. Family and artistic pursuits bring you joy.'],
    7 => ['planet' => 'Neptune / Ketu (केतु)', 'desc' => 'Analytical, spiritual, and deep-thinking. You seek truth and inner wisdom.'],
    8 => ['planet' => 'Saturn (शनि)', 'desc' => 'Authoritative, goal-oriented, and resilient. You have great potential for material success.'],
    9 => ['planet' => 'Mars (मंगल)', 'desc' => 'Compassionate, generous, and idealistic. You are here to serve humanity and lead by example.'],
    11 => ['planet' => 'Master Number', 'desc' => 'Highly intuitive and spiritual. You are a visionary capable of inspiring many.'],
    22 => ['planet' => 'Master Builder', 'desc' => 'Practical idealist. You have the power to turn grand dreams into massive realities.']
];

$ruling = $rulings[$result_number] ?? $rulings[$result_number % 10] ?? ['planet' => 'Unknown', 'desc' => 'Unique vibrating energy.'];

try {
    $db = getDB();
    
    // Create leads table automatically if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS numerology_leads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        mobile VARCHAR(50) NOT NULL,
        calculated_name VARCHAR(255),
        result_number INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    
    // Save lead
    $stmt = $db->prepare("INSERT INTO numerology_leads (name, mobile, calculated_name, result_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $mobile, strtoupper(preg_replace('/[^a-zA-Z]/', '', $name)), $result_number]);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'name' => $name,
            'number' => $result_number,
            'planet' => $ruling['planet'],
            'description' => $ruling['desc']
        ]
    ]);

} catch (PDOException $e) {
    error_log("Numerology API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error while capturing lead.']);
}
