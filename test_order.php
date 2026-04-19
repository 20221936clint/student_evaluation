<?php
require_once 'data/config.php';

$major_id = 1;

echo "<h3>Department Page Order</h3>";
$stmt = $pdo->prepare("
    SELECT s.subject_code, ms.sort_order
    FROM subjects s
    INNER JOIN major_subjects ms ON s.id = ms.subject_id
    WHERE ms.major_id = ?
    ORDER BY ms.sort_order, ms.year_level, ms.semester
");
$stmt->execute([$major_id]);
$dept = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($dept as $i=>$s) echo ($i+1) . ". " . $s['subject_code'] . " (" . $s['sort_order'] . ")<br>";

echo "<br><h3>Evaluation Page Order</h3>";
$stmt2 = $pdo->prepare("
    SELECT s.subject_code, ms.sort_order
    FROM major_subjects ms
    JOIN subjects s ON ms.subject_id = s.id
    WHERE ms.major_id = ?
    ORDER BY ms.sort_order, ms.year_level, ms.semester, s.subject_name
");
$stmt2->execute([$major_id]);
$eval = $stmt2->fetchAll(PDO::FETCH_ASSOC);
foreach ($eval as $i=>$s) echo ($i+1) . ". " . $s['subject_code'] . " (" . $s['sort_order'] . ")<br>";

echo "<br>Match? " . (json_encode($dept) === json_encode($eval) ? "YES ✅" : "NO ❌");
?>