<?php
$labels = ['products' => ['Inventory', 'car'], 'customers' => ['Customers', 'customer'], 'staff' => ['Staff', 'staff member']];
[$label, $single] = $labels[$kind];

if ($kind === 'products'):
    $categoryFor = static function (string $name): string {
        if (preg_match('/\b(EQE|EQS)\b/i', $name)) return 'electric';
        if (preg_match('/\b(AMG GT|SL)\b/i', $name)) return 'performance';
        if (preg_match('/\b(G-Class|GLA|GLB|GLC|GLE|GLS)\b/i', $name)) return 'suv';
        return 'sedan';
    };
?>
<div class="page-heading inventory-heading"><div><p class="eyebrow">THE COLLECTION / <?= count($rows) ?> IN VIEW</p><h1>Find your next drive<span class="heading-dot">.</span></h1><p class="subtle">Explore the lineup. Spin a car. Close the deal.</p></div><a class="button button-accent" href="<?= site_url('products/new') ?>">＋ Add car <span>↗</span></a></div>

<section class="collection-banner" aria-label="Inventory introduction">
  <div class="collection-orbit orbit-one"></div><div class="collection-orbit orbit-two"></div>
  <div class="collection-banner-copy"><span class="banner-kicker"><span class="online-dot"></span> THE SHOWROOM IS OPEN</span><h2>THE<br><em>COLLECTION</em></h2><p>Take a closer look. Tap 360° to spin each car.</p></div>
  <span class="collection-watermark" aria-hidden="true">MB</span>
  <div class="banner-bottom"><span>01 / MERCEDES-BENZ</span><span>SCROLL TO EXPLORE ↓</span></div>
</section>

<section class="collection-controls" aria-label="Filter cars">
  <div class="filter-chips" role="group" aria-label="Car category">
    <button type="button" class="filter-chip is-active" data-filter="all" aria-pressed="true">All cars <span><?= count($rows) ?></span></button>
    <button type="button" class="filter-chip" data-filter="sedan" aria-pressed="false">Sedans</button>
    <button type="button" class="filter-chip" data-filter="suv" aria-pressed="false">SUVs</button>
    <button type="button" class="filter-chip" data-filter="electric" aria-pressed="false">Electric</button>
    <button type="button" class="filter-chip" data-filter="performance" aria-pressed="false">Performance</button>
  </div>
  <div class="collection-tools">
    <form method="get" action="<?= site_url('products') ?>" class="search-form collection-search"><label class="sr-only" for="search">Search cars</label><input id="search" name="q" type="search" placeholder="Search cars..." value="<?= esc($search) ?>" autocomplete="off"><button type="submit" aria-label="Search">⌕</button></form>
    <label class="sort-label" for="car-sort">Sort <select id="car-sort" aria-label="Sort cars"><option value="default">Newest</option><option value="price-low">Price: low to high</option><option value="price-high">Price: high to low</option><option value="name">Name: A to Z</option></select></label>
  </div>
</section>

<div class="collection-count"><strong id="visible-count"><?= count($rows) ?></strong> cars found <span class="collection-line"></span><span>PRICES ARE DEMO VALUES</span></div>
<?php if (!$rows): ?><div class="panel empty-state">No cars found. <a href="<?= site_url('products/new') ?>">Add a car.</a></div><?php else: ?>
<div class="car-grid" id="car-grid">
  <?php foreach ($rows as $index => $row):
      $category = $categoryFor($row['name']);
      $colors = ['#a7f3ff', '#b9b6ff', '#8be9d3', '#f9a88c', '#dce6ff'];
      $color = $colors[$index % count($colors)];
  ?>
  <article class="car-card" data-name="<?= esc(strtolower($row['name'])) ?>" data-category="<?= esc($category) ?>" data-price="<?= (float) $row['price'] ?>" data-stock="<?= (int) $row['stock_quantity'] ?>" data-order="<?= $index ?>" style="--car-color:<?= $color ?>">
    <div class="car-visual">
      <div class="car-visual-grid"></div>
      <span class="car-category"><?= esc(strtoupper($category)) ?></span>
      <span class="car-index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?> / <?= str_pad((string) count($rows), 2, '0', STR_PAD_LEFT) ?></span>
      <?php if ($row['image']): ?>
        <img class="car-photo" src="<?= site_url($row['image']) ?>" alt="<?= esc($row['name']) ?>" loading="lazy">
      <?php else: ?>
        <div class="car-image-placeholder" role="img" aria-label="Photo not yet uploaded for <?= esc($row['name']) ?>"><span>PHOTO<br>COMING SOON</span></div>
      <?php endif; ?>
      <button type="button" class="spin-trigger" data-car-name="<?= esc($row['name']) ?>" data-car-category="<?= esc($category) ?>" aria-label="Open 360-degree preview of <?= esc($row['name']) ?>"><span class="spin-glyph">⟳</span> 360° VIEW</button>
    </div>
    <div class="car-details"><div class="car-details-top"><div><span class="car-series">MERCEDES-BENZ / <?= esc(strtoupper($category)) ?></span><h2><?= esc($row['name']) ?></h2></div><span class="stock-pill <?= $row['stock_quantity'] == 0 ? 'out' : '' ?>"><?= (int) $row['stock_quantity'] ?> available</span></div>
      <div class="car-details-bottom"><div><small>STARTING AT</small><strong>$<?= number_format((float) $row['price'], 0) ?></strong></div><div class="card-actions"><a href="<?= site_url('products/' . $row['id'] . '/edit') ?>">Edit ↗</a><form method="post" action="<?= site_url('products/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Archive this car?')"><?= csrf_field() ?><button type="submit">Archive</button></form></div></div>
    </div>
  </article>
  <?php endforeach; ?>
</div>
<div class="panel empty-state" id="filter-empty" hidden>No cars match this view. Try another category or search.</div>
<?php endif; ?>

<dialog id="car-viewer" class="car-viewer" aria-labelledby="viewer-title">
  <div class="viewer-top"><div><span class="eyebrow">INTERACTIVE 360° SHOWROOM</span><h2 id="viewer-title">Mercedes-Benz</h2></div><button type="button" class="viewer-close" aria-label="Close 360-degree preview">×</button></div>
  <div class="viewer-stage" id="viewer-stage"><div class="viewer-loading">Preparing 3D preview…</div></div>
  <div class="viewer-bottom"><p>Drag to rotate · Scroll to zoom<br><small>Stylized 3D concept preview</small></p><div class="viewer-controls"><div class="paint-options" aria-label="Car color"><button type="button" data-paint="#c9e4ee" class="is-active" style="--paint:#c9e4ee" aria-label="Silver paint" aria-pressed="true"></button><button type="button" data-paint="#202a3b" style="--paint:#202a3b" aria-label="Midnight paint" aria-pressed="false"></button><button type="button" data-paint="#176b88" style="--paint:#176b88" aria-label="Blue paint" aria-pressed="false"></button><button type="button" data-paint="#a62137" style="--paint:#a62137" aria-label="Red paint" aria-pressed="false"></button></div><button type="button" class="viewer-action" id="viewer-reset">Reset view</button><button type="button" class="viewer-action is-active" id="viewer-spin" aria-pressed="true">Auto spin: on</button></div></div>
</dialog>
<?php else: ?>
<div class="page-heading"><div><p class="eyebrow">MANAGEMENT</p><h1><?= $label ?></h1><p class="subtle">Keep your <?= strtolower($label) ?> up to date.</p></div><a class="button button-accent" href="<?= site_url($kind . '/new') ?>">＋ Add <?= $single ?></a></div>
<section class="panel data-panel">
  <div class="list-toolbar"><div><strong><?= count($rows) ?> <?= esc(strtolower($label)) ?></strong><span> in your showroom</span></div><form method="get" action="<?= site_url($kind) ?>" class="search-form"><label class="sr-only" for="search">Search <?= esc(strtolower($label)) ?></label><input id="search" name="q" type="search" placeholder="Search <?= esc(strtolower($label)) ?>" value="<?= esc($search) ?>"><button type="submit" aria-label="Search">⌕</button></form></div>
  <?php if (!$rows): ?><div class="empty-state">No <?= esc(strtolower($label)) ?> found. <a href="<?= site_url($kind . '/new') ?>">Add <?= $single ?>.</a></div><?php else: ?>
  <div class="table-wrap"><table class="data-table"><thead><tr>
    <?php if ($kind === 'customers'): ?><th>Customer</th><th>Email</th><th>Phone</th><th>Added</th><?php endif; ?>
    <?php if ($kind === 'staff'): ?><th>Staff member</th><th>Username</th><th>Added</th><?php endif; ?>
    <th class="align-right">Actions</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?><tr>
      <?php if ($kind === 'customers'): ?>
        <td><div class="entity"><span class="entity-initial"><?= esc(strtoupper(substr($row['full_name'], 0, 1))) ?></span><strong><?= esc($row['full_name']) ?></strong></div></td><td><?= esc($row['email']) ?></td><td><?= esc($row['phone'] ?: '—') ?></td><td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
      <?php else: ?>
        <td><div class="entity"><span class="entity-image small"><?php if ($row['avatar']): ?><img src="<?= site_url($row['avatar']) ?>" alt=""><?php else: ?><?= esc(strtoupper(substr($row['full_name'], 0, 1))) ?><?php endif; ?></span><strong><?= esc($row['full_name']) ?></strong></div></td><td>@<?= esc($row['username']) ?></td><td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
      <?php endif; ?>
      <td class="align-right"><div class="row-actions"><a href="<?= site_url($kind . '/' . $row['id'] . '/edit') ?>">Edit</a><form method="post" action="<?= site_url($kind . '/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Archive this <?= esc($single) ?>?')"><?= csrf_field() ?><button type="submit">Archive</button></form></div></td>
    </tr><?php endforeach; ?></tbody></table></div>
  <?php endif; ?>
</section>
<?php endif; ?>
