<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · Mercedes-Benz Showroom POS</title>
  <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('style.css') ?>?v=mercedes-1">
</head>
<body class="login-page">
<div class="login-shell">
  <div class="login-art" style="--login-image:url('<?= base_url('mercedes-login.webp') ?>')">
    <a class="brand login-brand" href="<?= site_url('/') ?>" aria-label="Mercedes-Benz showroom home"><span class="brand-mark"><img src="<?= base_url('favicon.svg') ?>" alt=""></span><span>Mercedes-Benz</span></a>
    <div class="login-art-copy"><span class="tiny-label">MERCEDES-BENZ SHOWROOM</span><h1>Every detail.<br>Every drive.</h1><p>Inventory, customers, and sales in one refined workspace.</p></div>
    <div class="login-art-footer">SHOWROOM POINT OF SALE <span>STUDENT PROJECT · <?= date('Y') ?></span></div>
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
      <div class="login-footnote">Authorized staff only · Unofficial student project</div>
    </div>
  </main>
</div>
</body>
</html>
