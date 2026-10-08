<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · VELOS POS</title>
  <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('style.css') ?>?v=login-layout-2">
</head>
<body class="login-page">
<div class="login-shell">
  <div class="login-art" style="background-image:linear-gradient(90deg,rgba(10,5,7,.18),rgba(10,5,7,.1)),url('<?= base_url('hero-car.webp') ?>')">
    <a class="brand login-brand" href="<?= site_url('/') ?>"><span class="brand-mark">✦</span><span>VELOS<span class="brand-dot">.</span></span></a>
    <div class="login-art-copy"><span class="tiny-label">AUTOMOTIVE RETAIL SYSTEM</span><h1>Every sale.<br>Full throttle.</h1><p>Inventory, people, and performance in one place.</p></div>
    <div class="login-art-footer">PRECISION IN EVERY TRANSACTION <span>© <?= date('Y') ?> VELOS</span></div>
  </div>
  <main class="login-panel">
    <div class="login-card">
      <div class="login-kicker"><span class="online-dot"></span> STAFF ACCESS</div>
      <h2>Welcome back.</h2>
      <p class="muted">Sign in to manage your showroom.</p>
      <?php if (session('error')): ?><div class="notice error" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
      <form method="post" action="<?= site_url('login') ?>" class="stack-form">
        <?= csrf_field() ?>
        <label for="username">Username</label><input id="username" name="username" type="text" autocomplete="username" value="<?= esc(old('username')) ?>" required autofocus>
        <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit" class="button button-accent button-full">Sign in <span>↗</span></button>
      </form>
      <div class="login-footnote">Authorized staff only · Secure session</div>
    </div>
  </main>
</div>
</body>
</html>
