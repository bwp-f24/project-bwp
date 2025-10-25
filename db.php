<?php
$DB_HOST = 'localhost';
$DB_NAME = 'city_events';
$DB_USER = 'root';
$DB_PASS = ''; // غيّرها إذا عندك كلمة مرور

try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  // أثناء التطوير ممكن تطبع رسالة، لكن عندنا الـ includes/data.php عامل fallback تلقائي لـ JSON
}
