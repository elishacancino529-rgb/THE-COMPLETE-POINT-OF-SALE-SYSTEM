const cards = [...document.querySelectorAll('.car-card')];
const grid = document.querySelector('#car-grid');
const count = document.querySelector('#visible-count');
const empty = document.querySelector('#filter-empty');
const search = document.querySelector('#search');
const sort = document.querySelector('#car-sort');
let activeFilter = 'all';

function updateGallery() {
  const query = (search?.value || '').trim().toLowerCase();
  let visible = 0;
  for (const card of cards) {
    const show = (activeFilter === 'all' || card.dataset.category === activeFilter) && card.dataset.name.includes(query);
    card.hidden = !show;
    if (show) visible++;
  }
  if (count) count.textContent = String(visible);
  if (empty) empty.hidden = visible > 0;
}
for (const chip of document.querySelectorAll('.filter-chip')) {
  chip.addEventListener('click', () => {
    activeFilter = chip.dataset.filter;
    for (const other of document.querySelectorAll('.filter-chip')) {
      const selected = other === chip;
      other.classList.toggle('is-active', selected);
      other.setAttribute('aria-pressed', String(selected));
    }
    updateGallery();
  });
}
search?.addEventListener('input', updateGallery);
sort?.addEventListener('change', () => {
  const modes = {
    'price-low': (a, b) => Number(a.dataset.price) - Number(b.dataset.price),
    'price-high': (a, b) => Number(b.dataset.price) - Number(a.dataset.price),
    name: (a, b) => a.dataset.name.localeCompare(b.dataset.name),
    default: (a, b) => Number(a.dataset.order) - Number(b.dataset.order),
  };
  cards.sort(modes[sort.value] || modes.default).forEach(card => grid.appendChild(card));
});

const dialog = document.querySelector('#car-viewer');
const stage = document.querySelector('#viewer-stage');
const title = document.querySelector('#viewer-title');
const credit = document.querySelector('#viewer-credit');
const emptyStage = () => stage.replaceChildren();

function openViewer(button) {
  const id = button.dataset.previewId;
  if (!/^[a-f0-9]{32}$/.test(id || '')) return;
  const modelUrl = `https://sketchfab.com/models/${id}`;
  title.textContent = button.dataset.carName;
  credit.href = modelUrl;
  credit.textContent = `3D model by ${button.dataset.previewCreator} on Sketchfab ↗`;
  emptyStage();
  const frame = document.createElement('iframe');
  frame.title = `${button.dataset.carName} interactive 360-degree 3D model`;
  frame.src = `${modelUrl}/embed?autostart=1&ui_controls=1&ui_hint=0`;
  frame.allow = 'autoplay; fullscreen; xr-spatial-tracking';
  frame.allowFullscreen = true;
  frame.referrerPolicy = 'strict-origin-when-cross-origin';
  stage.appendChild(frame);
  dialog.showModal();
}
for (const button of document.querySelectorAll('.spin-trigger')) {
  button.addEventListener('click', () => openViewer(button));
}
function closeViewer() {
  dialog.close();
  emptyStage();
}
document.querySelector('.viewer-close')?.addEventListener('click', closeViewer);
dialog?.addEventListener('click', event => {
  if (event.target === dialog) closeViewer();
});
dialog?.addEventListener('close', emptyStage);
