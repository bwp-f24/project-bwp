<?php require_once __DIR__ . '/includes/i18n.php'; ?>

<?php
require_once __DIR__ . '/includes/data.php';
$events = load_events_all();

// ===== حسابات عامة =====
$total = count($events);
$catsCount = []; $locs = []; $titles = [];
$today = strtotime(date('Y-m-d'));
$weekTo = strtotime('+7 days', $today);

foreach ($events as $e) {
  // عدّادات تصنيفات/أماكن/عناوين
  if (!empty($e['category'])) $catsCount[$e['category']] = ($catsCount[$e['category']] ?? 0) + 1;
  if (!empty($e['location'])) $locs[$e['location']] = true;
  if (!empty($e['title']))    $titles[] = $e['title'];
}

// القادمة
$upcomingAll = array_values(array_filter($events, function($e) use ($today){
  $ts = !empty($e['event_date']) ? strtotime($e['event_date']) : 0;
  return $ts && $ts >= $today;
}));
usort($upcomingAll, fn($a,$b)=> strcmp($a['event_date'],$b['event_date']));
$upcomingCount = count($upcomingAll);

// المميّزة (سلايدر) = أقرب 3
$featured = array_slice($upcomingAll, 0, 3);

// هذا الأسبوع (حتى +7 أيام)
$upcomingWeek = array_values(array_filter($upcomingAll, function($e) use ($weekTo){
  $ts = !empty($e['event_date']) ? strtotime($e['event_date']) : 0;
  return $ts && $ts <= $weekTo;
}));
$upcomingWeek = array_slice($upcomingWeek, 0, 6);

// الأحدث (آخر 8 بالتاريخ تنازلي)
$latest = $events;
usort($latest, fn($a,$b)=> strcmp($b['event_date'], $a['event_date']));
$latest = array_slice($latest, 0, 8);

// بيانات إضافية
$catCount = count($catsCount);
$locCount = count($locs);

// أسماء الفريق (للتذييل)
$TEAM = "Amer_258506 (C3) • Aya_270191 (C3) • Hasan_322384 (C5) • May_195978 (C3) • Shaden_241560 (C3)";
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>فعاليات مدينتي — الصفحة الرئيسية</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script>(function(){try{var t=localStorage.getItem('theme')||'light';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>

  <link rel="stylesheet" href="assets/css/styles.css">
  
  <style>
    .hero {
      background: radial-gradient(1200px 300px at 50% -50%, rgba(13,110,253,.15), transparent),
                  linear-gradient(180deg, #f8fafc, #fff);
    }
    .stat-card { border: 1px solid rgba(0,0,0,.06); }
    .cat-chip { border:1px solid rgba(0,0,0,.08); background:#fff; }
  </style>
</head>
<body>

<!-- Navbar -->
<?php // partials_nav.php
require_once __DIR__.'/includes/i18n.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.png" alt="logo" class="me-2" style="height:36px">
      <?= t('brand') ?>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="index.php"><?= t('nav_home') ?></a></li>
        <li class="nav-item"><a class="nav-link" href="events.php"><?= t('nav_events') ?></a></li>
        <li class="nav-item"><a class="nav-link" href="about.php"><?= t('nav_about') ?></a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php"><?= t('nav_contact') ?></a></li>

        <!-- اللغة -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">🌐 <?= t('lang') ?></a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= lang_switch_url('ar') ?>">🇸🇦 <?= t('ar') ?></a></li>
            <li><a class="dropdown-item" href="<?= lang_switch_url('en') ?>">🇬🇧 <?= t('en') ?></a></li>
          </ul>
        </li>

        <!-- الثيم -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">🌓 <?= t('theme') ?></a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><button class="dropdown-item theme-opt" data-theme="light">☀️ <?= t('light') ?></button></li>
            <li><button class="dropdown-item theme-opt" data-theme="dark">🌙 <?= t('dark') ?></button></li>
            <li><button class="dropdown-item theme-opt" data-theme="auto">⚙️ <?= t('auto') ?></button></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


<!-- Hero + Search + Stats -->
<header class="hero border-bottom py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="display-6 fw-bold mb-3">اكتشف ما يحدث في مدينتك اليوم</h1>
        <p class="lead text-muted">موسيقى، رياضة، ثقافة، وأنشطة عائلية — ابحث بسرعة وحدّد ما يلائمك.</p>

        <!-- نموذج بحث سريع يوجّه إلى events.php -->
        <form action="events.php" method="get" class="row g-2 align-items-end">
          <div class="col-md-6">
            <label class="form-label">ابحث بالعنوان/الوصف</label>
            <input list="eventTitles" type="text" class="form-control" name="q" placeholder="مثال: كورال، ماراثون">
            <datalist id="eventTitles">
              <?php
                // اقتراحات عناوين (حتى 50)
                $seen = [];
                foreach ($titles as $t) {
                  $t = trim($t);
                  if ($t !== '' && !isset($seen[$t])) { $seen[$t]=1; echo '<option value="'.htmlentities($t).'">'; }
                  if (count($seen) >= 50) break;
                }
              ?>
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label">التصنيف</label>
            <select class="form-select" name="category">
              <option value="">الكل</option>
              <?php foreach (['موسيقى','رياضة','ثقافة','عائلي'] as $c): ?>
                <option><?= htmlentities($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-primary">بحث</button>
          </div>
        </form>
      </div>

      <div class="col-lg-5">
        <div class="row g-3">
          <div class="col-6">
            <div class="p-3 rounded-3 bg-white shadow-sm stat-card text-center">
              <div class="h3 mb-1" data-counter="<?= (int)$total ?>">0</div>
              <div class="text-muted small">فعالية مُسجّلة</div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3 bg-white shadow-sm stat-card text-center">
              <div class="h3 mb-1" data-counter="<?= (int)$upcomingCount ?>">0</div>
              <div class="text-muted small">قادمة</div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3 bg-white shadow-sm stat-card text-center">
              <div class="h3 mb-1" data-counter="<?= (int)$catCount ?>">0</div>
              <div class="text-muted small">تصنيف</div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3 bg-white shadow-sm stat-card text-center">
              <div class="h3 mb-1" data-counter="<?= (int)$locCount ?>">0</div>
              <div class="text-muted small">مكان</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php // سلايدر مميّز لأقرب 3 فعاليات ?>
    <div class="row mt-4">
      <div class="col-12">
        <div id="hero" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php if ($featured): $i=0; foreach ($featured as $e): 
              $img = !empty($e['image']) ? 'assets/img/'.htmlentities($e['image']) : 'assets/img/placeholder-hero.jpg';
              $active = $i===0 ? 'active' : ''; $i++;
            ?>
            <div class="carousel-item <?= $active ?>">
              <img src="<?= $img ?>" class="d-block w-100 rounded-3 shadow-sm" alt="">
              <div class="carousel-caption d-none d-md-block">
                <h5 class="mb-1"><?= htmlentities($e['title']) ?></h5>
                <p class="small"><?= htmlentities($e['location']) ?> • <?= htmlentities($e['event_date']) ?></p>
                <a href="event.php?id=<?= urlencode($e['id']) ?>" class="btn btn-primary btn-sm">التفاصيل</a>
              </div>
            </div>
            <?php endforeach; else: ?>
              <div class="carousel-item active">
                <img src="assets/img/placeholder-hero.jpg" class="d-block w-100 rounded-3 shadow-sm" alt="">
                <div class="carousel-caption d-none d-md-block">
                  <h5>سيظهر هنا أبرز فعالية حال توفر بيانات قادمة</h5>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#hero" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
          <button class="carousel-control-next" type="button" data-bs-target="#hero" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- التصنيفات الشائعة -->
<section class="py-4">
  <div class="container">
    <h2 class="h5 mb-3">التصنيفات الشائعة</h2>
    <div class="d-flex flex-wrap gap-2">
      <?php
        // ترتيب التصنيفات تنازليًا حسب العدد
        arsort($catsCount);
        if ($catsCount) {
          foreach ($catsCount as $cat=>$cnt) {
            echo '<a class="btn cat-chip btn-sm" href="events.php?category='.urlencode($cat).'">'.
                 htmlentities($cat).' <span class="badge bg-secondary ms-1">'.$cnt.'</span></a>';
          }
        } else {
          echo '<span class="text-muted">لا توجد تصنيفات بعد.</span>';
        }
      ?>
    </div>
  </div>
</section>

<!-- قريبًا هذا الأسبوع -->
<section class="py-5 border-top">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="h5 mb-0">قريبًا هذا الأسبوع</h2>
      <a href="events.php?date_from=<?= date('Y-m-d') ?>&date_to=<?= date('Y-m-d', $weekTo) ?>" class="btn btn-outline-primary btn-sm">عرض المزيد</a>
    </div>
    <div class="row g-3">
      <?php if ($upcomingWeek): foreach ($upcomingWeek as $row):
        $img = !empty($row['image']) ? 'assets/img/'.htmlentities($row['image']) : 'assets/img/placeholder-card.jpg';
      ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <img src="<?= $img ?>" class="card-img-top" alt="">
            <div class="card-body">
              <span class="badge bg-secondary mb-2"><?= htmlentities($row['category']) ?></span>
              <h5 class="card-title"><?= htmlentities($row['title']) ?></h5>
              <p class="card-text small text-muted">📍 <?= htmlentities($row['location']) ?> • 🗓️ <?= htmlentities($row['event_date']) ?></p>
              <div class="d-flex gap-2">
                <a href="event.php?id=<?= urlencode($row['id']) ?>" class="btn btn-primary btn-sm">التفاصيل</a>
                <a href="ics.php?id=<?= urlencode($row['id']) ?>" class="btn btn-outline-secondary btn-sm">أضِف للتقويم</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <div class="col-12"><div class="alert alert-info">لا توجد فعاليات قريبة خلال هذا الأسبوع.</div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- أحدث الفعاليات -->
<section class="py-5 border-top">
  <div class="container">
    <h2 class="h5 mb-4">أحدث الفعاليات</h2>
    <div class="row g-3">
      <?php if (empty($latest)): ?>
        <div class="col-12"><div class="alert alert-warning">لا توجد فعاليات حالياً.</div></div>
      <?php else: foreach ($latest as $row): 
        $img = !empty($row['image']) ? 'assets/img/'.htmlentities($row['image']) : 'assets/img/placeholder-card.jpg';
      ?>
      <div class="col-12 col-md-6 col-lg-3">
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

    <div class="text-center mt-4">
      <a href="events.php" class="btn btn-primary">تصفّح كل الفعاليات</a>
      <a href="contact.php" class="btn btn-outline-secondary ms-2">لديك فعالية؟ أرسلها لنا</a>
    </div>
  </div>
</section>

<footer class="bg-dark text-white-50 py-4">
  <div class="container small d-flex flex-column flex-md-row justify-content-between">
    <div><strong>فريق المشروع:</strong> <?= $TEAM ?></div>
    <div>&copy; <?= date("Y") ?> فعاليات مدينتي</div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// عدادات متحركة بسيطة (نفس أسلوب about)
document.addEventListener('DOMContentLoaded', () => {
  const els = document.querySelectorAll('[data-counter]');
  if (!els.length) return;
  const animate = el => {
    const target = parseInt(el.getAttribute('data-counter') || '0', 10);
    let cur = 0;
    const step = Math.max(1, Math.round(target / 60));
    const tick = () => {
      cur += step;
      if (cur >= target) { el.textContent = target; return; }
      el.textContent = cur;
      requestAnimationFrame(tick);
    };
    tick();
  };
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { animate(e.target); io.unobserve(e.target); } });
  }, {threshold: .4});
  els.forEach(el => io.observe(el));
});
</script>
</body>
</html>
