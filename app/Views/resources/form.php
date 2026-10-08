<?php $editing = !empty($row); $single = ['products' => 'car', 'customers' => 'customer', 'staff' => 'staff member'][$kind]; ?>
<div class="page-heading"><div><p class="eyebrow"><?= strtoupper($kind) ?></p><h1><?= $editing ? 'Edit' : 'Add' ?> <?= $single ?></h1><p class="subtle"><?= $editing ? 'Update the information below.' : 'Fill out the details below to create a new record.' ?></p></div><a class="back-link" href="<?= site_url($kind) ?>">← Back to <?= $kind ?></a></div>
<section class="panel form-panel">
  <form method="post" action="<?= site_url($kind . ($editing ? '/' . $row['id'] : '')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if ($kind === 'products'): ?>
      <div class="form-section-head"><span class="form-section-icon">◈</span><div><h2>Car details</h2><p>Name, price, and units available for sale.</p></div></div>
      <div class="field-grid"><div class="field full"><label for="name">Car name <b>*</b></label><input id="name" name="name" maxlength="100" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required placeholder="e.g. Apex GT"></div>
      <div class="field"><label for="price">Price (USD) <b>*</b></label><div class="input-prefix"><span>$</span><input id="price" name="price" type="number" step="0.01" min="0" value="<?= esc(old('price', $row['price'] ?? '')) ?>" required placeholder="0.00"></div></div>
      <div class="field"><label for="stock_quantity">Stock quantity <b>*</b></label><input id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" value="<?= esc(old('stock_quantity', $row['stock_quantity'] ?? '0')) ?>" required></div>
      <div class="field full"><label for="image">Car image</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"><small>JPEG, PNG, or WebP · up to 3 MB. <?php if ($editing): ?>Leave empty to keep the current image.<?php endif; ?></small></div></div>
      <?php if ($editing && $row['image']): ?><img class="form-preview" src="<?= site_url($row['image']) ?>" alt="Current image of <?= esc($row['name']) ?>"><?php endif; ?>
    <?php elseif ($kind === 'customers'): ?>
      <div class="form-section-head"><span class="form-section-icon">◎</span><div><h2>Customer details</h2><p>Contact information for the buyer.</p></div></div>
      <div class="field-grid"><div class="field full"><label for="full_name">Full name <b>*</b></label><input id="full_name" name="full_name" maxlength="100" value="<?= esc(old('full_name', $row['full_name'] ?? '')) ?>" required placeholder="Customer's full name"></div>
      <div class="field"><label for="email">Email <b>*</b></label><input id="email" name="email" type="email" maxlength="100" value="<?= esc(old('email', $row['email'] ?? '')) ?>" required placeholder="name@example.com"></div>
      <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" type="tel" maxlength="20" value="<?= esc(old('phone', $row['phone'] ?? '')) ?>" placeholder="Optional"></div></div>
    <?php else: ?>
      <div class="form-section-head"><span class="form-section-icon">♧</span><div><h2>Staff profile</h2><p>Credentials and identity for dashboard access.</p></div></div>
      <div class="field-grid"><div class="field"><label for="full_name">Full name <b>*</b></label><input id="full_name" name="full_name" maxlength="100" value="<?= esc(old('full_name', $row['full_name'] ?? '')) ?>" required></div>
      <div class="field"><label for="username">Username <b>*</b></label><input id="username" name="username" maxlength="50" value="<?= esc(old('username', $row['username'] ?? '')) ?>" required autocomplete="off"></div>
      <div class="field full"><label for="password">Password <?= $editing ? '(leave blank to keep current)' : '<b>*</b>' ?></label><input id="password" name="password" type="password" minlength="10" <?= $editing ? '' : 'required' ?> autocomplete="new-password"><small>Use at least 10 characters.</small></div>
      <div class="field full"><label for="avatar">Avatar</label><input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"><small>JPEG, PNG, or WebP · up to 3 MB.</small></div></div>
      <?php if ($editing && $row['avatar']): ?><img class="form-preview avatar-preview" src="<?= site_url($row['avatar']) ?>" alt="Current avatar of <?= esc($row['full_name']) ?>"><?php endif; ?>
    <?php endif; ?>
    <div class="form-footer"><a class="button button-ghost" href="<?= site_url($kind) ?>">Cancel</a><button class="button button-accent" type="submit"><?= $editing ? 'Save changes' : 'Add ' . $single ?> <span>↗</span></button></div>
  </form>
</section>
