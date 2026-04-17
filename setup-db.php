<?php

require_once __DIR__ . '/db.php';

try {
    initializeDatabase();
    echo "<strong style='color: green;'>Database setup completed successfully.</strong><br>";
    echo "You can now open <code>index.php</code> and submit registrations.";
} catch (PDOException $e) {
    echo "<strong style='color: red;'>Error:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
