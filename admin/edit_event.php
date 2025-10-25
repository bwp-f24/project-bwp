<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id'=>$id]);
$e = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$e) { header('Location: dashboard.php'); exit; }

$err = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $category = trim($_POST['category'] ?? '');
  $location = trim($_POST['location'] ?? '');
  $event_date = trim($_POST['event_date'] ?? '');
  $imageName = $e['image'];

  // رفع الصورة (اختياري)
if (!empty($_FILES['image']['name'])) {
  $allowed = ['jpg','jpeg','png','gif'];
  $maxSize = 2 * 1024 * 1024; // 2MB
  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $probe = @getimagesize($_FILES['image']['tmp_name']); // NEW: تأكيد أنها صورة

  if ($probe === false) {
    $err = 'الملف المرفوع ليس صورة صالحة';
  } elseif (!in_array($ext, $allowed)) {
    $err = 'الامتدادات المسموحة: JPG, JPEG, PNG, GIF';
  } elseif ($_FILES['image']['size'] > $maxSize) {
    $err = 'الحجم الأقصى المسموح 2MB';
  } else {
    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
    $imageName = $safeBase . '-' . time() . '.' . $ext;
    $target = __DIR__ . '/../assets/img/' . $imageName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $err = 'فشل رفع الصورة';
    }
  }
}


  if (!$err) {
    if ($title && $description && $category && $location && $event_date) {
      $sql = "UPDATE events SET title=:t, description=:d, category=:c, location=:l, event_date=:e, image=:i WHERE id=:id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':t'=>$title, ':d'=>$description, ':c'=>$category,
        ':l'=>$location, ':e'=>$event_date, ':i'=>$imageName, ':id'=>$id
      ]);
      $ok = 'تم التحديث بنجاح';
      // جلب القيم بعد التحديث
      $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
      $stmt->execute([':id'=>$id]);
      $e = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      $err = 'يرجى ملء جميع الحقول المطلوبة';
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>تعديل فعالية</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<div class="container my-4" style="max-width:820px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5">تعديل فعالية #<?= (int)$e['id'] ?></h1>
    <div>
      <a class="btn btn-secondary btn-sm" href="dashboard.php">الرجوع</a>
      <a class="btn btn-outline-dark btn-sm" href="logout.php">خروج</a>
    </div>
  </div>
  <?php if($err):?><div class="alert alert-danger"><?= htmlentities($err) ?></div><?php endif;?>
  <?php if($ok):?><div class="alert alert-success"><?= htmlentities($ok) ?></div><?php endif;?>
  <form method="post" enctype="multipart/form-data" class="card card-body shadow-sm">
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">العنوان*</label>
        <input class="form-control" name="title" value="<?= htmlentities($e['title']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">التصنيف*</label>
        <select class="form-select" name="category" required>
          <?php
            $cats = ['موسيقى','رياضة','ثقافة','عائلي'];
            foreach($cats as $c){ $sel = ($e['category']===$c)?'selected':''; echo "<option $sel>".htmlentities($c)."</option>"; }
          ?>
        </select></div>
      <div class="col-md-3"><label class="form-label">تاريخ الفعالية*</label>
        <input type="date" class="form-control" name="event_date" value="<?= htmlentities($e['event_date']) ?>" required></div>
      <div class="col-md-6"><label class="form-label">المكان*</label>
        <input class="form-control" name="location" value="<?= htmlentities($e['location']) ?>" required></div>
      <div class="col-md-6"><label class="form-label">صورة (اختياري)</label>
        <input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.gif">
        <?php if($e['image']): ?><small class="text-muted">الحالية: <?= htmlentities($e['image']) ?></small><?php endif; ?>
      </div>
      <div class="col-12"><label class="form-label">الوصف*</label>
        <textarea class="form-control" name="description" rows="5" required><?= htmlentities($e['description']) ?></textarea></div>
      <div class="col-12 d-grid"><button class="btn btn-primary">حفظ</button></div>
    </div>
  </form>
</div>
</body></html>
