<?php
// Student Management Handler - For Admin and Program Head
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once 'config.php';

// Allow both admin and program_head
$allowed_roles = ['admin', 'program_head'];
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
    header('Location: ../login.php');
    exit;
}

$action = $_GET['action'] ?? '';

// Add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_student') {
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $major_id = intval($_POST['major_id'] ?? 0);
    $year_level = $_POST['year_level'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($student_id) || empty($email) || empty($major_id) || empty($year_level)) {
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Please fill in all required fields'));
        exit;
    }
    
    $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
    $gradient_from = '#3b82f6';
    $gradient_to = '#60a5fa';
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, middle_name, last_name, suffix, student_id, email, major_id, year_level, avatar_initials, avatar_gradient_from, avatar_gradient_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $middle_name ?: null, $last_name, $suffix ?: null, $student_id, $email, $major_id, $year_level, $initials, $gradient_from, $gradient_to]);
            header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode('Student enrolled successfully!'));
        } catch (PDOException $e) {
            header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Email or Student ID already exists'));
        }
    } else {
        header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode('Student enrolled successfully! (Demo Mode)'));
    }
    exit;
}

// Search students
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'search') {
    header('Content-Type: application/json');
    $query = trim($_GET['q'] ?? '');
    
    if (strlen($query) < 2) {
        echo json_encode(['success' => false, 'message' => 'Query too short']);
        exit;
    }
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT s.*, m.display_name as major_display, m.major_name 
                                  FROM students s 
                                  LEFT JOIN majors m ON s.major_id = m.id 
                                  WHERE (s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_id LIKE ? OR s.email LIKE ?)
                                  ORDER BY s.last_name, s.first_name 
                                  LIMIT 15");
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $formatted = array_map(function($s) {
                $initials = strtoupper(substr($s['first_name'] ?? '', 0, 1) . substr($s['last_name'] ?? '', 0, 1));
                return [
                    'id' => $s['id'],
                    'first_name' => $s['first_name'],
                    'last_name' => $s['last_name'],
                    'student_id' => $s['student_id'],
                    'email' => $s['email'],
                    'year_level' => $s['year_level'] ?? '',
                    'major_display' => $s['major_display'] ?? $s['major_name'] ?? 'N/A',
                    'initials' => $initials ?: 'NA'
                ];
            }, $students);
            
            echo json_encode(['success' => true, 'students' => $formatted]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database not available']);
    }
    exit;
}

// Get single student
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    header('Content-Type: application/json');
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT s.*, m.display_name as major_display FROM students s LEFT JOIN majors m ON s.major_id = m.id WHERE s.id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                echo json_encode(['success' => true, 'student' => $student]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Student not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database not available']);
    }
    exit;
}

// Update student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_student') {
    $id = intval($_POST['id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $suffix = trim($_POST['suffix'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $major_id = intval($_POST['major_id'] ?? 0);
    $year_level = $_POST['year_level'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($student_id) || empty($email) || empty($major_id) || empty($year_level)) {
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Please fill in all required fields'));
        exit;
    }
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, suffix = ?, student_id = ?, email = ?, major_id = ?, year_level = ? WHERE id = ?");
            $stmt->execute([$first_name, $middle_name ?: null, $last_name, $suffix ?: null, $student_id, $email, $major_id, $year_level, $id]);
            header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode('Student updated successfully!'));
        } catch (PDOException $e) {
            header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Failed to update student'));
        }
    } else {
        header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode('Student updated successfully! (Demo Mode)'));
    }
    exit;
}

// Delete student (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_student') {
    header('Content-Type: application/json');
    $student_id = intval($_POST['student_id'] ?? 0);
    
    if ($student_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
        exit;
    }
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete student']);
        }
    } else {
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully! (Demo Mode)']);
    }
    exit;
}

// Import students (CSV, Excel, Word)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'import_students') {
    if (!isset($_FILES['import_file'])) {
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('No file uploaded'));
        exit;
    }
    
    $file = $_FILES['import_file'];
    $error = $file['error'];
    
    if ($error !== UPLOAD_ERR_OK) {
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Upload error'));
        exit;
    }
    
    $tmp_path = $file['tmp_name'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['csv', 'xlsx', 'xls', 'docx'];
    
    if (!in_array($extension, $allowed)) {
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Invalid file format'));
        exit;
    }
    
    // Simple CSV import for demo; full implementation would require PhpSpreadsheet library
    $imported = 0;
    $errors = [];
    
    // Pre-load majors lookup table for CSV and Excel imports
    $majors_by_name_csv = [];
    $majors_by_id_csv = [];
    if ($pdo) {
        $majorsStmt = $pdo->query("SELECT id, major_name, display_name FROM majors WHERE is_active = 1");
        $allMajors = $majorsStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allMajors as $m) {
            $majors_by_id_csv[$m['id']] = $m['id'];
            $majors_by_name_csv[strtolower(trim($m['major_name']))] = $m['id'];
            if (!empty($m['display_name'])) {
                $majors_by_name_csv[strtolower(trim($m['display_name']))] = $m['id'];
            }
        }
    }
    
    // Valid year levels mapping
    $valid_year_levels_csv = [
        '1st year' => '1st Year',
        '2nd year' => '2nd Year',
        '3rd year' => '3rd Year',
        '4th year' => '4th Year',
        '1' => '1st Year',
        '2' => '2nd Year',
        '3' => '3rd Year',
        '4' => '4th Year',
        'first year' => '1st Year',
        'second year' => '2nd Year',
        'third year' => '3rd Year',
        'fourth year' => '4th Year',
    ];
    
    if ($extension === 'csv') {
        if (($handle = fopen($tmp_path, 'r')) !== false) {
            $row_num = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $row_num++;
                if ($row_num == 1) continue; // Skip header
                if (count($data) < 6) {
                    $errors[] = "Row $row_num: insufficient columns";
                    continue;
                }
                list($first_name, $last_name, $student_id, $email, $major_raw, $year_level_raw) = $data;
                $first_name = trim($first_name);
                $last_name = trim($last_name);
                $student_id = trim($student_id);
                $email = trim($email);
                $major_raw = trim($major_raw);
                $year_level_raw = trim($year_level_raw);
                
                if (empty($first_name) || empty($last_name) || empty($student_id) || empty($email)) {
                    $errors[] = "Row $row_num: missing required fields";
                    continue;
                }
                
                // Resolve major_id
                $resolved_major_id = 0;
                if (is_numeric($major_raw) && intval($major_raw) > 0) {
                    $resolved_major_id = intval($major_raw);
                } else {
                    $major_lower = strtolower($major_raw);
                    if (isset($majors_by_name_csv[$major_lower])) {
                        $resolved_major_id = $majors_by_name_csv[$major_lower];
                    } else {
                        foreach ($majors_by_name_csv as $name => $mid) {
                            if (strpos($major_lower, $name) !== false || strpos($name, $major_lower) !== false) {
                                $resolved_major_id = $mid;
                                break;
                            }
                        }
                    }
                    if ($resolved_major_id === 0 && !empty($major_raw)) {
                        $errors[] = "Row $row_num: could not find major '$major_raw'";
                        continue;
                    }
                }
                
                // Normalize year level
                $year_level = '';
                $year_level_lower = strtolower($year_level_raw);
                if (isset($valid_year_levels_csv[$year_level_lower])) {
                    $year_level = $valid_year_levels_csv[$year_level_lower];
                } elseif (in_array($year_level_raw, ['1st Year', '2nd Year', '3rd Year', '4th Year'])) {
                    $year_level = $year_level_raw;
                } else {
                    if (preg_match('/(\d)(st|nd|rd|th)\s*year/i', $year_level_raw, $matches)) {
                        $num = $matches[1];
                        $suffixes = ['1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th'];
                        if (isset($suffixes[$num])) {
                            $year_level = $suffixes[$num] . ' Year';
                        }
                    }
                }
                
                if (empty($year_level)) {
                    $errors[] = "Row $row_num: invalid year level '$year_level_raw'";
                    continue;
                }
                
                $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
                $gradient_from = '#3b82f6';
                $gradient_to = '#60a5fa';
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO students (first_name, middle_name, last_name, suffix, student_id, email, major_id, year_level, avatar_initials, avatar_gradient_from, avatar_gradient_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$first_name, '', $last_name, '', $student_id, $email, $resolved_major_id, $year_level, $initials, $gradient_from, $gradient_to]);
                    $imported++;
                } catch (PDOException $e) {
                    $error_msg = $e->getMessage();
                    if (strpos($error_msg, 'Duplicate') !== false || strpos($error_msg, 'duplicate') !== false) {
                        $errors[] = "Row $row_num: student ID '$student_id' or email '$email' already exists";
                    } else {
                        $errors[] = "Row $row_num: " . $error_msg;
                    }
                }
            }
            fclose($handle);
        }
    } elseif ($extension === 'xlsx' || $extension === 'xls') {
        // Excel import using PhpSpreadsheet
        require_once __DIR__ . '/vendor/autoload.php';
        
        try {
            if ($extension === 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }
            
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmp_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, false);
            
            // Pre-load majors lookup table (by name and by id)
            $majors_by_name = [];
            $majors_by_id = [];
            if ($pdo) {
                $majorsStmt = $pdo->query("SELECT id, major_name, display_name FROM majors WHERE is_active = 1");
                $allMajors = $majorsStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($allMajors as $m) {
                    $majors_by_id[$m['id']] = $m['id'];
                    // Index by lowercase name for flexible matching
                    $majors_by_name[strtolower(trim($m['major_name']))] = $m['id'];
                    if (!empty($m['display_name'])) {
                        $majors_by_name[strtolower(trim($m['display_name']))] = $m['id'];
                    }
                }
            }
            
            // Valid year levels mapping (normalize variations)
            $valid_year_levels = [
                '1st year' => '1st Year',
                '2nd year' => '2nd Year',
                '3rd year' => '3rd Year',
                '4th year' => '4th Year',
                '1' => '1st Year',
                '2' => '2nd Year',
                '3' => '3rd Year',
                '4' => '4th Year',
                'first year' => '1st Year',
                'second year' => '2nd Year',
                'third year' => '3rd Year',
                'fourth year' => '4th Year',
            ];
            
            $row_num = 0;
            foreach ($rows as $data) {
                $row_num++;
                if ($row_num == 1) continue; // Skip header row
                
                // Skip completely empty rows
                $filtered = array_filter($data, function($cell) {
                    return $cell !== null && trim((string)$cell) !== '';
                });
                if (empty($filtered)) continue;
                
                if (count($data) < 6) {
                    $errors[] = "Row $row_num: insufficient columns (found " . count($data) . ", need 6)";
                    continue;
                }
                
                $first_name = trim((string)($data[0] ?? ''));
                $last_name = trim((string)($data[1] ?? ''));
                // Student ID might be read as numeric - ensure it's treated as string without scientific notation
                $raw_student_id = $data[2] ?? '';
                if (is_float($raw_student_id) || is_int($raw_student_id)) {
                    $student_id = trim((string)intval($raw_student_id));
                } else {
                    $student_id = trim((string)$raw_student_id);
                }
                $email = trim((string)($data[3] ?? ''));
                $major_raw = trim((string)($data[4] ?? ''));
                $year_level_raw = trim((string)($data[5] ?? ''));
                
                if (empty($first_name) || empty($last_name) || empty($student_id) || empty($email)) {
                    $errors[] = "Row $row_num: missing required fields (first_name='$first_name', last_name='$last_name', student_id='$student_id', email='$email')";
                    continue;
                }
                
                // Resolve major_id: check if it's numeric first, otherwise look up by name
                $resolved_major_id = 0;
                if (is_numeric($major_raw) && intval($major_raw) > 0) {
                    // Numeric ID provided
                    $resolved_major_id = intval($major_raw);
                } else {
                    // Try to match by major name (case-insensitive, partial match)
                    $major_lower = strtolower($major_raw);
                    if (isset($majors_by_name[$major_lower])) {
                        $resolved_major_id = $majors_by_name[$major_lower];
                    } else {
                        // Try partial/fuzzy match - check if any major name contains or is contained in the input
                        foreach ($majors_by_name as $name => $mid) {
                            if (strpos($major_lower, $name) !== false || strpos($name, $major_lower) !== false) {
                                $resolved_major_id = $mid;
                                break;
                            }
                        }
                    }
                    
                    if ($resolved_major_id === 0 && !empty($major_raw)) {
                        $errors[] = "Row $row_num: could not find major '$major_raw' in the system";
                        continue;
                    }
                }
                
                // Normalize year level
                $year_level = '';
                $year_level_lower = strtolower($year_level_raw);
                if (isset($valid_year_levels[$year_level_lower])) {
                    $year_level = $valid_year_levels[$year_level_lower];
                } elseif (in_array($year_level_raw, ['1st Year', '2nd Year', '3rd Year', '4th Year'])) {
                    $year_level = $year_level_raw;
                } else {
                    // Try to extract year number from string like "4th year", "4th Year", etc.
                    if (preg_match('/(\d)(st|nd|rd|th)\s*year/i', $year_level_raw, $matches)) {
                        $num = $matches[1];
                        $suffixes = ['1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th'];
                        if (isset($suffixes[$num])) {
                            $year_level = $suffixes[$num] . ' Year';
                        }
                    }
                }
                
                if (empty($year_level)) {
                    $errors[] = "Row $row_num: invalid year level '$year_level_raw'";
                    continue;
                }
                
                $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));
                $gradient_from = '#3b82f6';
                $gradient_to = '#60a5fa';
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO students (first_name, middle_name, last_name, suffix, student_id, email, major_id, year_level, avatar_initials, avatar_gradient_from, avatar_gradient_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$first_name, '', $last_name, '', $student_id, $email, $resolved_major_id, $year_level, $initials, $gradient_from, $gradient_to]);
                    $imported++;
                } catch (PDOException $e) {
                    $error_msg = $e->getMessage();
                    // Provide user-friendly duplicate error message
                    if (strpos($error_msg, 'Duplicate') !== false || strpos($error_msg, 'duplicate') !== false) {
                        $errors[] = "Row $row_num: student ID '$student_id' or email '$email' already exists";
                    } else {
                        $errors[] = "Row $row_num: " . $error_msg;
                    }
                }
            }
            
            // Free memory
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Failed to read Excel file: ' . $e->getMessage()));
            exit;
        } catch (Exception $e) {
            header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Excel import error: ' . $e->getMessage()));
            exit;
        }
    } else {
        // Word (.docx) import not yet implemented
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode('Word import is not yet supported. Please use CSV or Excel format.'));
        exit;
    }
    
    if ($imported > 0 && empty($errors)) {
        $msg = "Successfully imported $imported students!";
        header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode($msg));
    } elseif ($imported > 0 && !empty($errors)) {
        $msg = "Imported $imported students; " . count($errors) . " errors: " . implode(' | ', array_slice($errors, 0, 3));
        if (count($errors) > 3) $msg .= ' ...and ' . (count($errors) - 3) . ' more';
        header('Location: ../program_head/pages/student_enrollment.php?success=' . urlencode($msg));
    } else {
        $msg = "Import failed - 0 students imported; " . count($errors) . " errors: " . implode(' | ', array_slice($errors, 0, 3));
        if (count($errors) > 3) $msg .= ' ...and ' . (count($errors) - 3) . ' more';
        header('Location: ../program_head/pages/student_enrollment.php?error=' . urlencode($msg));
    }
    exit;
}

// If no valid action, redirect
header('Location: ../program_head/pages/student_enrollment.php');
exit;