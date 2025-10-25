<?php require_once __DIR__ . '/includes/i18n.php'; ?>

<?php
require_once __DIR__ . '/includes/data.php';
$events = load_events_all();

// حسابات ديناميكية آمنة
$total = count($events);
$cats = [];
$locs = [];
$dates = [];

foreach ($events as $e) {
  if (!empty($e['category'])) $cats[$e['category']] = true;
  if (!empty($e['location'])) $locs[$e['location']] = true;
  if (!empty($e['event_date'])) {
    $ts = strtotime($e['event_date']);
    if ($ts) $dates[] = $ts;
  }
}
$catCount = count($cats);
$locCount = count($locs);
sort($dates);
$firstDate = $dates ? date('Y-m-d', $dates[0]) : '—';
$lastDate  = $dates ? date('Y-m-d', $dates[count($dates)-1]) : '—';
$today     = strtotime(date('Y-m-d'));
$upcoming  = 0;
foreach ($events as $e) {
  $d = !empty($e['event_date']) ? strtotime($e['event_date']) : 0;
  if ($d && $d >= $today) $upcoming++;
}

// فريق المشروع
$team = [
  ['name' => 'Amer_258506',  'track' => 'C3'],
  ['name' => 'Aya_270191',   'track' => 'C3'],
  ['name' => 'Hasan_322384', 'track' => 'C5'],
  ['name' => 'May_195978',   'track' => 'C3'],
  ['name' => 'Shaden_241560','track' => 'C3'],
];
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>عن الدليل — فعاليات مدينتي</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script>(function(){try{var t=localStorage.getItem('theme')||'light';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>

  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    /* تحسينات خفيفة */
    .hero {
      background: radial-gradient(1200px 300px at 50% -50%, rgba(13,110,253,.15), transparent),
                  linear-gradient(180deg, #f8fafc, #fff);
    }
    .stat-card { border: 1px solid rgba(0,0,0,.06); }
    .avatar {
      width: 56px; height: 56px; border-radius: 999px;
      display: inline-flex; align-items: center; justify-content: center;
      background: #0d6efd10; color: #0d6efd; font-weight: 700;
      border: 1px solid #0d6efd20;
    }
    .value-card { border-left: 4px solid #0d6efd; }
    .step-badge {
      width: 32px; height: 32px; border-radius: 999px; font-weight:700;
      display:inline-flex; align-items:center; justify-content:center;
      background:#0d6efd; color:#fff;
    }
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


<!-- Hero -->
<header class="hero border-bottom py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="display-6 fw-bold mb-3">منصّة مبسّطة لاكتشاف فعاليات مدينتك</h1>
        <p class="lead text-muted">
          نسعى لربط المجتمع بالموسيقى والرياضة والثقافة والأنشطة العائلية—مع بحث ذكي وتصفية سهلة وتفاصيل دقيقة لكل فعالية.
        </p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="events.php" class="btn btn-primary">استكشف الفعاليات</a>
          <a href="contact.php" class="btn btn-outline-secondary">ارسل لنا فعالية</a>
        </div>
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
              <div class="h3 mb-1" data-counter="<?= (int)$upcoming ?>">0</div>
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
              <div class="h6 mb-1"><?= htmlentities($firstDate) ?> → <?= htmlentities($lastDate) ?></div>
              <div class="text-muted small">نطاق التواريخ</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Mission / Vision / Values -->
<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="p-4 bg-white rounded-3 shadow-sm value-card h-100">
          <h3 class="h5 mb-2">رسالتنا</h3>
          <p class="text-muted mb-0">
            توفير دليل موثوق وسهل الاستخدام لفعاليات المدينة، مع تحديثات مستمرة وتصفية ذكيّة تُسهّل الوصول لما يهمّك.
          </p>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="p-4 bg-white rounded-3 shadow-sm value-card h-100" style="border-left-color:#198754">
          <h3 class="h5 mb-2">رؤيتنا</h3>
          <p class="text-muted mb-0">
            أن نصبح منصة المجتمع الأولى لاكتشاف الفعاليات والتواصل بين المنظمين والجمهور، مع تجربة سلسة على جميع الأجهزة.
          </p>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="p-4 bg-white rounded-3 shadow-sm value-card h-100" style="border-left-color:#fd7e14">
          <h3 class="h5 mb-2">قيمنا</h3>
          <ul class="mb-0 text-muted">
            <li>بساطة الواجهة</li>
            <li>دقّة البيانات</li>
            <li>الخصوصية والأمان</li>
            <li>التعلّم والتحسين المستمر</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- How it works -->
<section class="py-5 border-top">
  <div class="container">
    <h2 class="h4 mb-4">كيف يعمل الدليل؟</h2>
    <div class="row g-3">
      <div class="col-md-6 col-lg-3">
        <div class="p-3 rounded-3 bg-light h-100">
          <span class="step-badge me-2">1</span>
          <strong>تصفّح</strong>
          <p class="text-muted small mb-0">استعرض أحدث الفعاليات في الرئيسية.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 rounded-3 bg-light h-100">
          <span class="step-badge me-2">2</span>
          <strong>فلترة وبحث</strong>
          <p class="text-muted small mb-0">حدّد التصنيف/التاريخ أو ابحث بالكلمات.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 rounded-3 bg-light h-100">
          <span class="step-badge me-2">3</span>
          <strong>التفاصيل</strong>
          <p class="text-muted small mb-0">اقرأ الوصف والمكان والوقت بدقة.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 rounded-3 bg-light h-100">
          <span class="step-badge me-2">4</span>
          <strong>أضِف للتقويم</strong>
          <p class="text-muted small mb-0">حمّل ملف .ICS من صفحة الفعالية.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Team -->
<section class="py-5 border-top">
  <div class="container">
    <h2 class="h4 mb-4">فريق المشروع</h2>
    <div class="row g-3">
      <?php foreach ($team as $m):
        $initials = mb_substr($m['name'],0,1);
      ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="p-3 rounded-3 bg-white shadow-sm h-100 d-flex align-items-center gap-3">
          <div class="avatar"><?= htmlentities($initials) ?></div>
          <div>
            <div class="fw-semibold"><?= htmlentities($m['name']) ?></div>
            <div class="text-muted small">المسار: <?= htmlentities($m['track']) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="py-5 border-top">
  <div class="container">
    <h2 class="h4 mb-4">أسئلة شائعة</h2>
    <div class="accordion" id="faq">
      <div class="accordion-item">
        <h2 class="accordion-header" id="q1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">كيف أبحث عن فعالية محددة؟</button>
        </h2>
        <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faq">
          <div class="accordion-body text-muted">
            من صفحة <strong>الفعاليات</strong> استخدم مربع البحث وحدّد التصنيف أو التاريخ. لإرسال الرابط لصديقك، انسخ العنوان بعد تطبيق الفلاتر.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="q2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">كيف أضيف فعالية جديدة؟</button>
        </h2>
        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faq">
          <div class="accordion-body text-muted">
            تواصل معنا عبر صفحة <a href="contact.php">اتصل بنا</a> أو سجّل الدخول كمسؤول لإضافة الفعالية من لوحة التحكم.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="q3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">هل يمكن حفظ الفعالية في التقويم؟</button>
        </h2>
        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faq">
          <div class="accordion-body text-muted">
            نعم، من صفحة تفاصيل الفعالية اضغط <em>أضِف إلى التقويم</em> لتحميل ملف ICS وفتحه في تقويم جهازك.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="q4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">هل يدعم الموقع الهواتف؟</button>
        </h2>
        <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faq">
          <div class="accordion-body text-muted">
            الواجهة مبنية على Bootstrap بتصميم متجاوب بالكامل وتعمل بسلاسة على الجوال والتابلت والكمبيوتر.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 border-top bg-light">
  <div class="container text-center">
    <h2 class="h5 mb-2">هل لديك فعالية تريد نشرها؟</h2>
    <p class="text-muted mb-3">أخبرنا بالتفاصيل وسنراجعها بسرعة.</p>
    <a href="contact.php" class="btn btn-primary">ارسل لنا فعالية</a>
  </div>
</section>

<footer class="bg-dark text-white-50 py-4">
  <div class="container small d-flex flex-column flex-md-row justify-content-between">
    <div>&copy; <?= date("Y") ?> فعاليات مدينتي</div>
    <div>يعتمد على PHP وMySQL وBootstrap وJavaScript</div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// عدادات متحركة بسيطة
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
