<?php
// data/settings_process.php
header('Content-Type: application/json');
require_once 'config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'save_settings') {
    $settings_key = 'program_head_settings';
    $settings_value = json_encode($_POST);
    
    try {
        // Try to update, if not exists then insert
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$settings_key, $settings_value, $settings_value]);
        echo json_encode(['success' => true, 'message' => 'Settings saved']);
    } catch (PDOException $e) {
        // Table might not exist, try alternative
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                setting_key VARCHAR(100) UNIQUE,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$settings_key, $settings_value, $settings_value]);
            echo json_encode(['success' => true, 'message' => 'Settings saved']);
        } catch (PDOException $e2) {
            // Just save to session as fallback
            $_SESSION['ph_settings'] = $settings_value;
            echo json_encode(['success' => true, 'message' => 'Settings saved (session)']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}