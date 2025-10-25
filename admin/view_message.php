<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id=:id");
$stmt->execute([':id'=>$id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>عرض رسالة</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<div class="container my-4" style="max-width:820px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">عرض رسالة</h1>
    <div>
      <a class="btn btn-secondary btn-sm" href="messages.php">الرجوع</a>
      <a class="btn btn-outline-dark btn-sm" href="logout.php">خروج</a>
    </div>
  </div>

  <?php if(!$m): ?>
    <div class="alert alert-danger">الرسالة غير موجودة.</div>
  <?php else: ?>
    <div class="card card-body shadow-sm">
      <div class="row g-3">
        <div class="col-md-6"><strong>الاسم:</strong> <?= htmlentities($m['name']) ?></div>
        <div class="col-md-6"><strong>البريد:</strong> <a href="mailto:<?= htmlentities($m['email']) ?>"><?= htmlentities($m['email']) ?></a></div>
        <div class="col-md-6"><strong>الموضوع:</strong> <span class="badge bg-secondary"><?= htmlentities($m['topic']) ?></span></div>
        <div class="col-md-6"><strong>التاريخ:</strong> <span class="text-muted small"><?= htmlentities($m['created_at']) ?></span></div>
        <div class="col-12"><hr><strong>نص الرسالة:</strong><p class="mt-2"><?= nl2br(htmlentities($m['body'])) ?></p></div>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-danger" href="delete_message.php?id=<?= (int)$m['id'] ?>">حذف</a>
      </div>
    </div>
  <?php endif; ?>
</div>
</body></html>
