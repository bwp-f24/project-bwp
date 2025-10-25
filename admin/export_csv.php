<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="events_export.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','title','description','category','location','event_date','image']);

$stmt = $pdo->query("SELECT id,title,description,category,location,event_date,image FROM events ORDER BY event_date DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($out, $row);
}
fclose($out);
