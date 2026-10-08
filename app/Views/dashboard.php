<div class="page-heading"><div><p class="eyebrow">GOOD TO SEE YOU, <?= esc(strtoupper((string) session('user_name'))) ?></p><h1>Your showroom<span class="heading-dot">.</span></h1><p class="subtle">Everything in motion, all in one place.</p></div><span class="today-date"><?= date('l, F j, Y') ?></span></div>
<section class="hero" style="--hero-image:url('<?= base_url('mercedes-s-class.webp') ?>')">
  <div class="hero-content"><span class="hero-tag"><span class="online-dot"></span> MERCEDES-BENZ SHOWROOM</span><h2>FULL<br>THROTTLE<span class="heading-dot">.</span></h2><p>Fresh arrivals. Fast moves. Your next sale starts here.</p><div class="hero-buttons"><a href="<?= site_url('products') ?>" class="button button-light">Explore cars <span>↗</span></a><a href="<?= site_url('sales/new') ?>" class="button button-outline">Record a sale <span>↗</span></a></div></div>
  <span class="hero-index">THE DRIVE STARTS HERE / 001</span>
</section>
<section class="stats-grid" aria-label="Business metrics">
  <div class="stat-card"><div class="stat-top"><span>Cars in inventory</span><span class="stat-symbol">◈</span></div><strong><?= number_format($cars) ?></strong><a href="<?= site_url('products') ?>">View inventory ↗</a></div>
  <div class="stat-card"><div class="stat-top"><span>Sales completed</span><span class="stat-symbol">↗</span></div><strong><?= number_format($salesCount) ?></strong><a href="<?= site_url('sales') ?>">View history ↗</a></div>
  <div class="stat-card"><div class="stat-top"><span>Total revenue</span><span class="stat-symbol">＄</span></div><strong>$<?= number_format((float) $revenue, 2) ?></strong><span class="stat-caption">All recorded transactions</span></div>
  <div class="stat-card"><div class="stat-top"><span>Customers</span><span class="stat-symbol">◎</span></div><strong><?= number_format($customers) ?></strong><a href="<?= site_url('customers') ?>">View customers ↗</a></div>
</section>
<div class="dashboard-grid">
  <section class="panel"><div class="panel-head"><div><p class="eyebrow">ACTIVITY</p><h2>Recent sales</h2></div><a class="text-link" href="<?= site_url('sales') ?>">View all ↗</a></div>
    <?php if (!$recent): ?><div class="empty-state">No sales yet. <a href="<?= site_url('sales/new') ?>">Record your first sale.</a></div><?php else: ?>
    <div class="activity-list"><?php foreach ($recent as $sale): ?><div class="activity-row"><span class="activity-icon">↗</span><div><strong><?= esc($sale['product_name']) ?></strong><small><?= esc($sale['customer_name'] ?: 'Walk-in customer') ?> · <?= date('M j, Y', strtotime($sale['created_at'])) ?></small></div><b>$<?= number_format((float) $sale['total_price'], 2) ?></b></div><?php endforeach; ?></div>
    <?php endif; ?>
  </section>
  <section class="panel"><div class="panel-head"><div><p class="eyebrow">ATTENTION</p><h2>Low stock</h2></div><a class="text-link" href="<?= site_url('products') ?>">Inventory ↗</a></div>
    <?php if (!$lowStock): ?><div class="empty-state">Everything is well stocked.</div><?php else: ?>
    <div class="low-list"><?php foreach ($lowStock as $car): ?><div class="low-row"><span class="low-dot"></span><strong><?= esc($car['name']) ?></strong><span class="stock-pill <?= $car['stock_quantity'] == 0 ? 'out' : '' ?>"><?= (int) $car['stock_quantity'] ?> left</span></div><?php endforeach; ?></div>
    <?php endif; ?>
  </section>
</div>
