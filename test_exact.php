<?php
require_once 'data/config.php';

$major_id = 1;

echo "Exact same query as major_process.php getMajorSubjects():<br>";
$stmt = $pdo->prepare("
    SELECT s.*, ms.id as major_subject_id, ms.year_level, ms.semester, ms.is_required, ms.is_prerequisite, ms.sort_order
    FROM subjects s
    INNER JOIN major_subjects ms ON s.id = ms.subject_id
    WHERE ms.major_id = ?
    ORDER BY ms.sort_order, ms.year_level, ms.semester
");
$stmt->execute([$major_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($subjects as $i=>$s) {
    echo ($i+1) . ". " . $s['subject_code'] . " (" . $s['sort_order'] . ")<br>";
    if ($i == 17) break;
}
?>