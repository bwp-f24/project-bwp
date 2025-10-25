<?php
require_once __DIR__.'/../db.php';
$username = 'admin';
$newPass  = 'admin'; // كلمة المرور الجديدة
$hash = password_hash($newPass, PASSWORD_BCRYPT);

// تأمين وجود المستخدم admin مرة واحدة
$pdo->exec("INSERT IGNORE INTO users (username,password) VALUES ('admin','admin')");

// استبدال كلمة المرور بالتجزئة
$stmt = $pdo->prepare("UPDATE users SET password = :p WHERE username = :u");
$stmt->execute([':p'=>$hash, ':u'=>$username]);

echo "تم تعيين كلمة مرور جديدة (مجزّأة) للمستخدم admin.";
