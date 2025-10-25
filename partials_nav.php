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
