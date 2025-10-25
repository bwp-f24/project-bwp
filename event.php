<?php require_once __DIR__ . '/includes/i18n.php'; ?>

<?php
require_once __DIR__ . '/includes/data.php';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$event = $id ? find_event_by_id($id) : null;
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title><?= $event ? htmlentities($event['title']) : 'تفاصيل الفعالية' ?> — فعاليات مدينتي</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script>(function(){try{var t=localStorage.getItem('theme')||'light';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>

  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
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


<main class="py-5">
  <div class="container">
    <?php if (!$event): ?>
      <div class="alert alert-danger">تعذّر العثور على الفعالية المطلوبة.</div>
      <a href="events.php" class="btn btn-secondary btn-sm">عودة للقائمة</a>
    <?php else: 
      $img = !empty($event['image']) ? 'assets/img/'.htmlentities($event['image']) : 'assets/img/placeholder-hero.jpg';
    ?>
    <div class="row g-4">
      <div class="col-lg-7">
        <img src="<?= $img ?>" class="img-fluid rounded-3 shadow-sm" alt="">
      </div>
      <div class="col-lg-5">
        <h1 class="h3 mb-3"><?= htmlentities($event['title']) ?></h1>
        <p class="text-muted mb-1"><span class="badge bg-secondary"><?= htmlentities($event['category']) ?></span></p>
        <p class="mb-2">📍 <?= htmlentities($event['location']) ?></p>
        <p class="mb-4">🗓️ <?= htmlentities($event['event_date']) ?></p>
        <p><?= nl2br(htmlentities($event['description'])) ?></p>
        <div class="d-flex gap-2 mt-4">
          <a class="btn btn-primary btn-sm" href="events.php">كل الفعاليات</a>
          <a class="btn btn-outline-secondary btn-sm" href="ics.php?id=<?= urlencode($event['id']) ?>">أضِف إلى التقويم</a>

        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</main>

<footer class="bg-dark text-white-50 py-4 mt-5">
  <div class="container small">&copy; <?= date("Y") ?> فعاليات مدينتي</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
