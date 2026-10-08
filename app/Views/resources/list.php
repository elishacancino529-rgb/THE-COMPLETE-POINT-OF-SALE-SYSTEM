<?php
$labels = ['products' => ['Inventory', 'car'], 'customers' => ['Customers', 'customer'], 'staff' => ['Staff', 'staff member']];
[$label, $single] = $labels[$kind];
?>
<div class="page-heading"><div><p class="eyebrow">MANAGEMENT</p><h1><?= $label ?></h1><p class="subtle">Keep your <?= strtolower($label) ?> up to date.</p></div><a class="button button-accent" href="<?= site_url($kind . '/new') ?>">＋ Add <?= $single ?></a></div>
<section class="panel data-panel">
  <div class="list-toolbar"><div><strong><?= count($rows) ?> <?= esc(strtolower($label)) ?></strong><span> in your showroom</span></div><form method="get" action="<?= site_url($kind) ?>" class="search-form"><label class="sr-only" for="search">Search <?= esc(strtolower($label)) ?></label><input id="search" name="q" type="search" placeholder="Search <?= esc(strtolower($label)) ?>" value="<?= esc($search) ?>"><button type="submit" aria-label="Search">⌕</button></form></div>
  <?php if (!$rows): ?><div class="empty-state">No <?= esc(strtolower($label)) ?> found. <a href="<?= site_url($kind . '/new') ?>">Add <?= $single ?>.</a></div><?php else: ?>
  <div class="table-wrap"><table class="data-table"><thead><tr>
    <?php if ($kind === 'products'): ?><th>Car</th><th>Price</th><th>Available</th><th>Added</th><?php endif; ?>
    <?php if ($kind === 'customers'): ?><th>Customer</th><th>Email</th><th>Phone</th><th>Added</th><?php endif; ?>
    <?php if ($kind === 'staff'): ?><th>Staff member</th><th>Username</th><th>Added</th><?php endif; ?>
    <th class="align-right">Actions</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?><tr>
      <?php if ($kind === 'products'): ?>
        <td><div class="entity"><span class="entity-image"><?php if ($row['image']): ?><img src="<?= site_url($row['image']) ?>" alt="<?= esc($row['name']) ?>"><?php else: ?>◈<?php endif; ?></span><strong><?= esc($row['name']) ?></strong></div></td><td class="money">$<?= number_format((float) $row['price'], 2) ?></td><td><span class="stock-pill <?= $row['stock_quantity'] == 0 ? 'out' : '' ?>"><?= (int) $row['stock_quantity'] ?> in stock</span></td><td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
      <?php elseif ($kind === 'customers'): ?>
        <td><div class="entity"><span class="entity-initial"><?= esc(strtoupper(substr($row['full_name'], 0, 1))) ?></span><strong><?= esc($row['full_name']) ?></strong></div></td><td><?= esc($row['email']) ?></td><td><?= esc($row['phone'] ?: '—') ?></td><td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
      <?php else: ?>
        <td><div class="entity"><span class="entity-image small"><?php if ($row['avatar']): ?><img src="<?= site_url($row['avatar']) ?>" alt=""><?php else: ?><?= esc(strtoupper(substr($row['full_name'], 0, 1))) ?><?php endif; ?></span><strong><?= esc($row['full_name']) ?></strong></div></td><td>@<?= esc($row['username']) ?></td><td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
      <?php endif; ?>
      <td class="align-right"><div class="row-actions"><a href="<?= site_url($kind . '/' . $row['id'] . '/edit') ?>">Edit</a><form method="post" action="<?= site_url($kind . '/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Archive this <?= esc($single) ?>?')"><?= csrf_field() ?><button type="submit">Archive</button></form></div></td>
    </tr><?php endforeach; ?></tbody></table></div>
  <?php endif; ?>
</section>
