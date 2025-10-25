
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

if (empty($_SESSION['csrf_admin'])) {
  $_SESSION['csrf_admin'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_admin'];

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, name, topic FROM messages WHERE id=:id");
$stmt->execute([':id'=>$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

// تنفيذ الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf']) || !hash_equals($csrf, (string)$_POST['csrf'])) {
    die('طلب غير صالح (CSRF).');
  }
  $idp = (int)($_POST['id'] ?? 0);
  $del = $pdo->prepare("DELETE FROM messages WHERE id=:id");
  $del->execute([':id'=>$idp]);
  header('Location: messages.php');
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>حذف رسالة</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<div class="container my-5" style="max-width:600px;">
  <div class="card card-body shadow-sm">
    <?php if(!$m): ?>
      <div class="alert alert-danger">الرسالة غير موجودة.</div>
      <a href="messages.php" class="btn btn-secondary btn-sm">عودة</a>
    <?php else: ?>
      <h1 class="h5 mb-3">تأكيد حذف الرسالة</h1>
      <p class="mb-4"><strong>#<?= (int)$m['id'] ?> — <?= htmlentities($m['name']) ?> (<?= htmlentities($m['topic']) ?>)</strong></p>
      <form method="post" class="d-flex gap-2">
        <input type="hidden" name="csrf" value="<?= htmlentities($csrf) ?>">
        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
        <button class="btn btn-danger">حذف</button>
        <a class="btn btn-secondary" href="messages.php">إلغاء</a>
      </form>
    <?php endif; ?>
  </div>
</div>
</body></html>
