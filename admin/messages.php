<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../db.php';

// تأكد من وجود الجدول
$pdo->exec("CREATE TABLE IF NOT EXISTS messages(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  topic VARCHAR(60) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// فلاتر
$q         = trim($_GET['q'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to   = trim($_GET['date_to'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 10;

$where = [];
$params = [];

if ($q !== '') {
  $where[] = "(name LIKE :q OR email LIKE :q OR topic LIKE :q OR body LIKE :q)";
  $params[':q'] = "%$q%";
}
if ($date_from !== '') {
  $where[] = "created_at >= :df";
  $params[':df'] = $date_from . " 00:00:00";
}
if ($date_to !== '') {
  $where[] = "created_at <= :dt";
  $params[':dt'] = $date_to . " 23:59:59";
}

$wsql = $where ? "WHERE ".implode(" AND ", $where) : "";

// العدد الكلي
$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages $wsql");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// بيانات الصفحة
$offset = ($page-1)*$perPage;
$sql = "SELECT id, name, email, topic, body, created_at
        FROM messages $wsql
        ORDER BY created_at DESC
        LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pages = max(1, (int)ceil($total / $perPage));
?>
<!doctype html>
<html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>الرسائل — لوحة الإدارة</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head><body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <span class="navbar-brand">رسائل “اتصل بنا”</span>
    <div class="d-flex gap-2">
      <a href="dashboard.php" class="btn btn-outline-light btn-sm">الفعاليات</a>
      <a href="export_messages_csv.php" class="btn btn-outline-warning btn-sm">تصدير CSV</a>
      <a href="logout.php" class="btn btn-outline-light btn-sm">خروج</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <form class="row g-2 align-items-end mb-3">
    <div class="col-md-5">
      <label class="form-label">بحث (اسم/بريد/موضوع/نص)</label>
      <input type="text" name="q" class="form-control" value="<?= htmlentities($q) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="date_from" class="form-control" value="<?= htmlentities($date_from) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="date_to" class="form-control" value="<?= htmlentities($date_to) ?>">
    </div>
    <div class="col-md-1 d-grid">
      <button class="btn btn-primary">تطبيق</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th><th>الاسم</th><th>البريد</th><th>الموضوع</th><th>مقتطف</th><th>التاريخ</th><th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlentities($r['name']) ?></td>
            <td><a href="mailto:<?= htmlentities($r['email']) ?>"><?= htmlentities($r['email']) ?></a></td>
            <td><span class="badge bg-secondary"><?= htmlentities($r['topic']) ?></span></td>
            <td class="small text-muted">
              <?= htmlentities(mb_strimwidth($r['body'], 0, 80, '…', 'UTF-8')) ?>
            </td>
            <td class="small text-muted"><?= htmlentities($r['created_at']) ?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="view_message.php?id=<?= (int)$r['id'] ?>">عرض</a>
              <a class="btn btn-sm btn-danger"  href="delete_message.php?id=<?= (int)$r['id'] ?>">حذف</a>
            </td>
          </tr>
        <?php endforeach; if(!$rows): ?>
          <tr><td colspan="7" class="text-center text-muted">لا رسائل</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
  <nav aria-label="pagination">
    <ul class="pagination justify-content-center">
      <?php
        $base = $_GET; unset($base['page']);
        $qs = fn($p)=>'?'.http_build_query($base+['page'=>$p]);
      ?>
      <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="<?= $qs($page-1) ?>">السابق</a></li>
      <?php for($i=1;$i<=$pages;$i++): ?>
        <li class="page-item <?= $page==$i?'active':'' ?>"><a class="page-link" href="<?= $qs($i) ?>"><?= $i ?></a></li>
      <?php endfor; ?>
      <li class="page-item <?= $page>=$pages?'disabled':'' ?>"><a class="page-link" href="<?= $qs($page+1) ?>">التالي</a></li>
    </ul>
  </nav>
  <?php endif; ?>
</div>
</body></html>
