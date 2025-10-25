<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id'=>$id]);
  }
  header('Location: dashboard.php');
  exit;
} else {
  $stmt = $pdo->prepare("SELECT id, title FROM events WHERE id = :id");
  $stmt->execute([':id'=>$id]);
  $e = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>حذف فعالية</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<div class="container my-5" style="max-width:600px;">
  <div class="card card-body shadow-sm">
    <?php if(!$e): ?>
      <div class="alert alert-danger">الفعالية غير موجودة.</div>
      <a href="dashboard.php" class="btn btn-secondary btn-sm">عودة</a>
    <?php else: ?>
      <h1 class="h5 mb-3">تأكيد حذف الفعالية:</h1>
      <p class="mb-4"><strong>#<?= (int)$e['id'] ?> — <?= htmlentities($e['title']) ?></strong></p>
      <form method="post" class="d-flex gap-2">
        <input type="hidden" name="confirm" value="yes">
        <button class="btn btn-danger">حذف</button>
        <a href="dashboard.php" class="btn btn-secondary">إلغاء</a>
      </form>
    <?php endif; ?>
  </div>
</div>
</body></html>
