<?php require_once __DIR__ . '/includes/i18n.php'; ?>

<?php
require_once __DIR__ . '/includes/data.php';
$filters = [
  'category'  => $_GET['category']  ?? '',
  'q'         => $_GET['q']         ?? '',
  'date_from' => $_GET['date_from'] ?? '',
  'date_to'   => $_GET['date_to']   ?? ''
];
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$perPage = 9;

$total = 0;
$events = load_events_page($filters, $page, $perPage, $total);
$pages = max(1, (int)ceil($total / $perPage));
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الفعاليات — فعاليات مدينتي</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script>(function(){try{var t=localStorage.getItem('theme')||'light';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>

  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/partials_nav.php'; /* اختياري: أو انسخ نافبار index.php */ ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.png" alt="الشعار" class="me-2" style="height:36px"> فعاليات مدينتي
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="events.php">الفعاليات</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">عن الدليل</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">اتصل بنا</a></li>
      </ul>
    </div>
  </div>
</nav>

<header class="bg-light py-4 border-bottom">
  <div class="container">
    <form class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">التصنيف</label>
        <select name="category" class="form-select">
          <option value="">الكل</option>
          <?php
          $cats = ["موسيقى","رياضة","ثقافة","عائلي"];
          foreach ($cats as $c) {
            $sel = ($filters['category']===$c)?'selected':'';
            echo "<option $sel>".htmlentities($c)."</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">بحث</label>
        <input type="text" class="form-control" name="q" value="<?= htmlentities($filters['q']) ?>" placeholder="عنوان/وصف">
      </div>
      <div class="col-md-2">
        <label class="form-label">من تاريخ</label>
        <input type="date" class="form-control" name="date_from" value="<?= htmlentities($filters['date_from']) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" class="form-control" name="date_to" value="<?= htmlentities($filters['date_to']) ?>">
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary">تطبيق</button>
      </div>
    </form>
  </div>
</header>

<main class="py-4">
  <div class="container">
    <div class="row g-3">
      <?php if (empty($events)): ?>
        <div class="col-12"><div class="alert alert-warning">لا نتائج مطابقة.</div></div>
      <?php else: foreach ($events as $row): 
        $img = !empty($row['image']) ? 'assets/img/'.htmlentities($row['image']) : 'assets/img/placeholder-card.jpg'; ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <img src="<?= $img ?>" class="card-img-top" alt="">
            <div class="card-body">
              <span class="badge bg-secondary mb-2"><?= htmlentities($row['category']) ?></span>
              <h5 class="card-title"><?= htmlentities($row['title']) ?></h5>
              <p class="card-text small text-muted"><?= htmlentities($row['location']) ?> • <?= htmlentities($row['event_date']) ?></p>
              <a href="event.php?id=<?= urlencode($row['id']) ?>" class="btn btn-outline-primary btn-sm">التفاصيل</a>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</main>
<?php if ($pages > 1): ?>
<nav aria-label="pagination" class="mt-4">
  <ul class="pagination justify-content-center flex-wrap">
    <?php
      // بناء رابط مع الحفاظ على فلاتر GET الأخرى
      $base = $_GET; unset($base['page']);
      $qs = function($p) use ($base){ return '?'.http_build_query($base + ['page'=>$p]); };
    ?>
    <li class="page-item <?= $page<=1?'disabled':'' ?>">
      <a class="page-link" href="<?= $qs($page-1) ?>">السابق</a>
    </li>
    <?php for ($i=1;$i<=$pages;$i++): ?>
      <li class="page-item <?= $i===$page?'active':'' ?>">
        <a class="page-link" href="<?= $qs($i) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$pages?'disabled':'' ?>">
      <a class="page-link" href="<?= $qs($page+1) ?>">التالي</a>
    </li>
  </ul>
</nav>
<?php endif; ?>


<footer class="bg-dark text-white-50 py-4 mt-5">
  <div class="container small">&copy; <?= date("Y") ?> فعاليات مدينتي</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
