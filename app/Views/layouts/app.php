<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0b1217">
  <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
  <title><?= esc($title) ?> · Mercedes-Benz Showroom POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('style.css') ?>?v=showroom-3">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <a class="brand" href="<?= site_url('/') ?>" aria-label="Mercedes-Benz showroom home"><span class="brand-mark"><img src="<?= base_url('favicon.svg') ?>" alt=""></span><span>Mercedes-Benz</span></a>
      <div class="sidebar-caption">DRIVE MODE / ON</div>
      <nav class="main-nav" aria-label="Main navigation">
        <a class="nav-link <?= $section === 'overview' ? 'active' : '' ?>" href="<?= site_url('/') ?>"><span class="nav-icon">▦</span> Overview</a>
        <a class="nav-link <?= $section === 'products' ? 'active' : '' ?>" href="<?= site_url('products') ?>"><span class="nav-icon">◈</span> Inventory</a>
        <a class="nav-link <?= $section === 'sales' ? 'active' : '' ?>" href="<?= site_url('sales') ?>"><span class="nav-icon">↗</span> Sales history</a>
        <a class="nav-link <?= $section === 'customers' ? 'active' : '' ?>" href="<?= site_url('customers') ?>"><span class="nav-icon">◎</span> Customers</a>
        <a class="nav-link <?= $section === 'staff' ? 'active' : '' ?>" href="<?= site_url('staff') ?>"><span class="nav-icon">♧</span> Staff</a>
      </nav>
      <div class="sidebar-bottom"><span class="online-dot"></span> System ready <span class="version">LIVE SHOWROOM</span></div>
    </aside>
    <main class="main-content">
      <header class="topbar">
        <div class="breadcrumb">MERCEDES-BENZ <span>/</span> <?= esc($title) ?></div>
        <div class="topbar-actions">
          <a class="button button-compact button-accent" href="<?= site_url('sales/new') ?>"><span>＋</span> New sale</a>
          <div class="user-chip"><span class="user-avatar"><?= esc(strtoupper(substr((string) session('user_name'), 0, 1))) ?></span><span><?= esc(session('user_name')) ?></span></div>
          <form method="post" action="<?= site_url('logout') ?>" class="logout-form"><?= csrf_field() ?><button class="logout-button" type="submit" title="Sign out">Sign out <span>↗</span></button></form>
        </div>
      </header>
      <div class="page-content">
        <?php if (session('success')): ?><div class="notice success" role="status"><?= esc(session('success')) ?></div><?php endif; ?>
        <?php if (session('error')): ?><div class="notice error" role="alert"><?= esc(session('error')) ?></div><?php endif; ?>
        <?php if (session('errors')): ?><div class="notice error" role="alert"><strong>Please check the form:</strong><ul><?php foreach (session('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <?= $content ?>
      </div>
    </main>
  </div>
  <?php if ($section === 'products' && $title === 'Inventory'): ?><script type="module" src="<?= base_url('viewer.js') ?>?v=3"></script><?php endif; ?>
</body>
</html>
