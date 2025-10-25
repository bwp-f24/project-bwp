<?php
session_start();
require_once __DIR__ . '/../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? trim($_POST['password']) : '';

  if ($username === '' || $password === '') {
    $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
  } else {
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      $dbPass = $user['password'];
      $ok = password_verify($password, $dbPass) || ($password === $dbPass); // يدعم التجزئة أو النص الصريح
      if ($ok) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$user['id'];
        $_SESSION['admin_name'] = $user['username'];
        header('Location: dashboard.php');
        exit;
      }
    }
    $error = 'بيانات الدخول غير صحيحة';
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تسجيل الدخول — لوحة الإدارة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container" style="max-width:420px;">
    <h1 class="h4 text-center my-4">لوحة إدارة الفعاليات</h1>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlentities($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" class="card card-body shadow-sm">
      <div class="mb-3">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">دخول</button>
    </form>
  </div>
</body>
</html>
