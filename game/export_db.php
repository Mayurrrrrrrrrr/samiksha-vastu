<?php
// export_db.php - The Portability Engine
$host = "localhost";
$user = "root";
$pass = "";
$name = "quiz_app";

// Connect to local database
$conn = new mysqli($host, $user, $pass, $name);
$tables = array('questions', 'choices', 'users'); // All critical tables
$return = "";

foreach($tables as $table) {
    // 1. Structure
    $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
    $return .= "\n\n" . $row2[1] . ";\n\n";
    
    // 2. Data
    $result = $conn->query("SELECT * FROM $table");
    while($row = $result->fetch_row()) {
        $return .= "INSERT INTO $table VALUES(";
        for($j=0; $j<count($row); $j++) {
            $row[$j] = addslashes($row[$j]);
            if (isset($row[$j])) { $return .= '"'.$row[$j].'"' ; } else { $return .= '""'; }
            if ($j<(count($row)-1)) { $return .= ','; }
        }
        $return .= ");\n";
    }
}

// Download the file
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"vastu_kiosk_backup.sql\"");
echo $return; exit;
?>