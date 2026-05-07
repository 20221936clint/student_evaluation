<?php
$pdo = new PDO('mysql:host=localhost;dbname=checkmate', 'root', '');
$stmt = $pdo->query("DESCRIBE students");
while($row = $stmt->fetch()) {
    if($row['Field'] === 'student_type') {
        echo "student_type: " . $row['Type'] . "\n";
        echo "Default: " . $row['Default'] . "\n";
    }
}
?>