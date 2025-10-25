<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="messages_export.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','name','email','topic','body','created_at']);

$stmt = $pdo->query("SELECT id,name,email,topic,body,created_at FROM messages ORDER BY created_at DESC");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($out, $r);
}
fclose($out);
