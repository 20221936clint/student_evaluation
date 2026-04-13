<?php
require_once 'config.php';

try {
    
    $pdo->exec("ALTER TABLE mentees ADD COLUMN assigned_by_id INT");
    $pdo->exec("ALTER TABLE mentees ADD COLUMN assigned_by_name VARCHAR(255)");
    $pdo->exec("ALTER TABLE mentees ADD COLUMN assignment_notes TEXT");
    echo "Columns added. ";
    
    $mentees = [
        ['student_id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@student.edu', 'mentor_id' => 1, 'assigned_by_id' => 1, 'assigned_by_name' => 'Admin', 'assignment_notes' => 'Best overall performer', 'created_at' => '2026-01-15 10:30:00'],
        ['student_id' => 2, 'first_name' => 'Jane', 'last_name' => 'Wilson', 'email' => 'jane.wilson@student.edu', 'mentor_id' => 1, 'assigned_by_id' => 1, 'assigned_by_name' => 'Admin', 'assignment_notes' => 'Strong analytical skills', 'created_at' => '2026-02-01 14:20:00'],
        ['student_id' => 3, 'first_name' => 'Mike', 'last_name' => 'Johnson', 'email' => 'mike.j@student.edu', 'mentor_id' => 1, 'assigned_by_id' => 1, 'assigned_by_name' => 'Admin', 'assignment_notes' => 'Excellent communication', 'created_at' => '2026-02-10 09:15:00'],
        ['student_id' => 4, 'first_name' => 'Sarah', 'last_name' => 'Williams', 'email' => 'sarah.w@student.edu', 'mentor_id' => 1, 'assigned_by_id' => 1, 'assigned_by_name' => 'Admin', 'assignment_notes' => 'Top performer in Marketing', 'created_at' => '2026-03-05 11:45:00'],
        ['student_id' => 5, 'first_name' => 'Tom', 'last_name' => 'Brown', 'email' => 'tom.b@student.edu', 'mentor_id' => 1, 'assigned_by_id' => 1, 'assigned_by_name' => 'Admin', 'assignment_notes' => 'Shows great potential', 'created_at' => '2026-03-12 16:30:00'],
    ];

    $stmt = $pdo->prepare("INSERT INTO mentees (student_id, first_name, last_name, email, mentor_id, assigned_by_id, assigned_by_name, assignment_notes, created_at) VALUES (:student_id, :first_name, :last_name, :email, :mentor_id, :assigned_by_id, :assigned_by_name, :assignment_notes, :created_at)");
    
    foreach ($mentees as $mentee) {
        $stmt->execute($mentee);
    }
    
    echo "Success: " . count($mentees) . " mentees inserted";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}