<?php
include 'config.php';
header('Content-Type: application/json');

$sql = "SELECT q.id, q.question_text, q.question_image, q.correct_option_id, 
               GROUP_CONCAT(c.choice_text SEPARATOR '|') as opts, 
               GROUP_CONCAT(c.id SEPARATOR '|') as opt_ids 
        FROM questions q 
        JOIN choices c ON q.id = c.question_id 
        GROUP BY q.id";

$res = $conn->query($sql);
$questions = [];

while($r = $res->fetch_assoc()) {
    $questions[] = [
        "text" => $r['question_text'],
        "image" => $r['question_image'],
        "options" => explode('|', $r['opts']),
        "ids" => explode('|', $r['opt_ids']),
        "correct" => $r['correct_option_id']
    ];
}
echo json_encode($questions);
?>