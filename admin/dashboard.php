<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';
$stmt = $pdo->query("SELECT id, title, category, location, event_date, image FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>لوحة التحكم — الفعاليات</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <span class="navbar-brand">لوحة الإدارة</span>
    <div>
      <a href="add_event.php" class="btn btn-success btn-sm">+ إضافة فعالية</a>
      <a href="messages.php" class="btn btn-outline-info btn-sm">الرسائل</a>

    <a href="export_csv.php" class="btn btn-outline-warning btn-sm">تصدير CSV</a>
<a href="logout.php" class="btn btn-outline-light btn-sm">تسجيل الخروج</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-dark">
        <tr><th>#</th><th>العنوان</th><th>التصنيف</th><th>المكان</th><th>التاريخ</th><th>الصورة</th><th>إجراءات</th></tr>
      </thead>
      <tbody>
        <?php foreach ($events as $e): ?>
        <tr>
          <td><?= (int)$e['id'] ?></td>
          <td><?= htmlentities($e['title']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlentities($e['category']) ?></span></td>
          <td><?= htmlentities($e['location']) ?></td>
          <td><?= htmlentities($e['event_date']) ?></td>
          <td><?= htmlentities($e['image']) ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="edit_event.php?id=<?= (int)$e['id'] ?>">تعديل</a>
            <a class="btn btn-sm btn-danger" href="delete_event.php?id=<?= (int)$e['id'] ?>">حذف</a>
          </td>
        </tr>
        <?php endforeach; if (!$events): ?>
          <tr><td colspan="7" class="text-center text-muted">لا توجد فعاليات</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
