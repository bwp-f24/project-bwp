<?php require_once __DIR__ . '/includes/i18n.php'; ?>

<?php
// contact.php — نسخة مطوّرة مع تحقق خادمي + DB/JSON تخزين
session_start();

$SITE_NAME = 'فعاليات مدينتي';

// 1) تحضير CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// 2) إعدادات عامة
$errors = [];
$okMsg  = '';
$old = [
  'name'    => '',
  'email'   => '',
  'topic'   => '',
  'message' => '',
];

// 3) مواضيع جاهزة
$topics = ['اقتراح فعالية','استفسار عام','تعديل بيانات','شراكة/رعاية','أخرى'];

// 4) دالة اتصال DB (اختياري)
$pdo = null;
try {
  require_once __DIR__ . '/db.php'; // إذا غير موجود سيُرمى استثناء ونسقط على JSON
  if (isset($pdo) && $pdo instanceof PDO) {
    // إنشاء الجدول تلقائيًا لو غير موجود
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(120) NOT NULL,
      email VARCHAR(190) NOT NULL,
      topic VARCHAR(60) NOT NULL,
      body TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  } else {
    $pdo = null;
  }
} catch (Throwable $e) {
  $pdo = null; // JSON fallback
}

// 5) مُعدّل الإرسال (3 رسائل / 5 دقائق)
if (!isset($_SESSION['contact_times'])) $_SESSION['contact_times'] = [];

// 6) معالجة POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  if (!isset($_POST['csrf']) || !hash_equals($csrf, (string)$_POST['csrf'])) {
    $errors[] = 'طلب غير صالح (CSRF). يرجى تحديث الصفحة والمحاولة مجددًا.';
  }

  // Honeypot (حقل مخفي يجب أن يبقى فارغ)
  if (!empty($_POST['website'])) {
    $errors[] = 'تم رصد نشاط غير معتاد.';
  }

  // جلب القيم
  $old['name']    = trim($_POST['name']    ?? '');
  $old['email']   = trim($_POST['email']   ?? '');
  $old['topic']   = trim($_POST['topic']   ?? '');
  $old['message'] = trim($_POST['message'] ?? '');

  // تحقق القيم
  if ($old['name'] === '' || mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 80) {
    $errors[] = 'الاسم يجب أن يكون بين 2 و 80 حرفًا.';
  }
  if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 190) {
    $errors[] = 'الرجاء إدخال بريد إلكتروني صالح.';
  }
  if ($old['topic'] === '' || !in_array($old['topic'], $topics, true)) {
    $errors[] = 'الرجاء اختيار موضوع الرسالة.';
  }
  if ($old['message'] === '' || mb_strlen($old['message']) < 10 || mb_strlen($old['message']) > 2000) {
    $errors[] = 'نص الرسالة يجب أن يكون بين 10 و 2000 حرف.';
  }

  // Rate limit
  $now = time();
  $_SESSION['contact_times'] = array_values(array_filter($_SESSION['contact_times'], fn($t)=> $now - $t < 300)); // 5 دقائق
  if (count($_SESSION['contact_times']) >= 3) {
    $errors[] = 'لقد تجاوزت الحد المسموح به. يرجى المحاولة لاحقًا.';
  }

  // حفظ
  if (!$errors) {
    try {
      if ($pdo) {
        $stmt = $pdo->prepare("INSERT INTO messages (name,email,topic,body) VALUES (:n,:e,:t,:b)");
        $stmt->execute([
          ':n' => $old['name'],
          ':e' => $old['email'],
          ':t' => $old['topic'],
          ':b' => $old['message'],
        ]);
      } else {
        // JSON fallback
        $file = __DIR__ . '/assets/data/contact_messages.json';
        if (!is_dir(dirname($file))) { @mkdir(dirname($file), 0775, true); }
        $all = [];
        if (file_exists($file)) {
          $json = file_get_contents($file);
          $all = json_decode($json, true);
          if (!is_array($all)) $all = [];
        }
        $all[] = [
          'name' => $old['name'],
          'email'=> $old['email'],
          'topic'=> $old['topic'],
          'body' => $old['message'],
          'created_at' => date('c'),
        ];
        file_put_contents($file, json_encode($all, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
      }

      // (اختياري) إرسال بريد للمدير إن كان mail() مفعّلًا — معطّل افتراضيًا:
      /*
      @mb_internal_encoding('UTF-8');
      $to = 'admin@example.com';
      $subject = "رسالة جديدة من نموذج التواصل - $SITE_NAME";
      $body = "الاسم: {$old['name']}\nالبريد: {$old['email']}\nالموضوع: {$old['topic']}\n\n{$old['message']}";
      @mail($to, $subject, $body, "Content-Type: text/plain; charset=UTF-8");
      */

      $_SESSION['contact_times'][] = $now;
      $okMsg = 'تم استلام رسالتك بنجاح. سنعاود التواصل قريبًا. شكرًا لك!';

      // إعادة ضبط الحقول بعد النجاح
      $old = ['name'=>'','email'=>'','topic'=>'','message'=>''];
    } catch (Throwable $e) {
      $errors[] = 'حدث خطأ أثناء الحفظ. يرجى المحاولة لاحقًا.';
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>اتصل بنا — <?= htmlspecialchars($SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script>(function(){try{var t=localStorage.getItem('theme')||'light';if(t==='auto'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-bs-theme',t);}catch(e){}})();</script>

  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    .hero { background: linear-gradient(180deg,#f8fafc,#fff); }
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


<header class="hero py-5 border-bottom">
  <div class="container">
    <h1 class="h3 mb-2">يسعدنا تواصلك</h1>
    <p class="text-muted mb-0">املأ النموذج أدناه—نراجع الطلبات بسرعة ونعود إليك خلال أقرب وقت.</p>
  </div>
</header>

<main class="py-5">
  <div class="container" style="max-width:820px;">
    <?php if ($okMsg): ?>
      <div class="alert alert-success"><?= htmlentities($okMsg, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $er): ?>
            <li><?= htmlentities($er, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" class="card card-body shadow-sm" novalidate>
      <input type="hidden" name="csrf" value="<?= htmlentities($csrf) ?>">
      <!-- Honeypot -->
      <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">الاسم الكامل *</label>
          <input type="text" class="form-control" name="name" required minlength="2" maxlength="80"
                 value="<?= htmlentities($old['name'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">البريد الإلكتروني *</label>
          <input type="email" class="form-control" name="email" required maxlength="190"
                 value="<?= htmlentities($old['email'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">الموضوع *</label>
          <select class="form-select" name="topic" required>
            <option value="">اختر...</option>
            <?php foreach ($topics as $t):
              $sel = ($old['topic'] === $t) ? 'selected' : ''; ?>
              <option <?= $sel ?>><?= htmlentities($t, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">نص الرسالة *</label>
          <textarea class="form-control" name="message" rows="6" required minlength="10" maxlength="2000"><?= htmlentities($old['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
          <div class="form-text">الحد الأقصى 2000 حرف. يُرجى كتابة تفاصيل واضحة.</div>
        </div>
        <div class="col-12 d-flex justify-content-between align-items-center">
          <small class="text-muted">* حقول مطلوبة</small>
          <button class="btn btn-primary">إرسال الرسالة</button>
        </div>
      </div>
    </form>

    <!-- معلومات تواصل ثابتة -->
    <div class="row g-3 mt-4">
      <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100">
          <div class="fw-semibold mb-1">البريد</div>
          <div class="text-muted small">info@example.com</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100">
          <div class="fw-semibold mb-1">الهاتف</div>
          <div class="text-muted small">(+000) 123 456 789</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-3 bg-light rounded-3 h-100">
          <div class="fw-semibold mb-1">العنوان</div>
          <div class="text-muted small">ساحة المدينة، المركز الثقافي</div>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="bg-dark text-white-50 py-4 mt-5">
  <div class="container small d-flex flex-column flex-md-row justify-content-between">
    <div>&copy; <?= date('Y') ?> <?= htmlentities($SITE_NAME) ?></div>
    <div>يحفظ الرسائل في <?= $pdo ? 'قاعدة البيانات' : 'JSON (fallback)' ?></div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
