import * as THREE from 'three';
import { OrbitControls } from './OrbitControls.js';

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
const spinButton = document.querySelector('#viewer-spin');
let scene, camera, renderer, controls, model, paintMaterial, animationId, resizeObserver;
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function addBox(group, width, height, depth, color, x, y, z, options = {}) {
  const material = color instanceof THREE.Material ? color : new THREE.MeshStandardMaterial({ color, metalness: options.metalness ?? .35, roughness: options.roughness ?? .45 });
  const mesh = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), material);
  mesh.position.set(x, y, z);
  mesh.castShadow = true;
  mesh.receiveShadow = true;
  group.add(mesh);
  return mesh;
}
function addShape(group, points, depth, material, z = 0, bevel = .045) {
  const shape = new THREE.Shape();
  points.forEach(([x, y], i) => i ? shape.lineTo(x, y) : shape.moveTo(x, y));
  shape.closePath();
  const geometry = new THREE.ExtrudeGeometry(shape, { depth, bevelEnabled: bevel > 0, bevelThickness: bevel, bevelSize: bevel, bevelSegments: 2, steps: 1 });
  geometry.translate(0, 0, z - depth / 2);
  const mesh = new THREE.Mesh(geometry, material);
  mesh.castShadow = true;
  mesh.receiveShadow = true;
  group.add(mesh);
  return mesh;
}
function addWheel(group, x, z) {
  const rubber = new THREE.MeshStandardMaterial({ color: 0x0b1019, roughness: .88 });
  const rim = new THREE.MeshStandardMaterial({ color: 0xa7b9c6, metalness: .9, roughness: .22 });
  const spoke = new THREE.MeshStandardMaterial({ color: 0x708b9c, metalness: .8, roughness: .25 });
  const tire = new THREE.Mesh(new THREE.CylinderGeometry(.47, .47, .24, 36), rubber);
  tire.rotation.x = Math.PI / 2;
  tire.position.set(x, .51, z);
  tire.castShadow = true;
  group.add(tire);
  const side = z > 0 ? z + .15 : z - .15;
  const disk = new THREE.Mesh(new THREE.CylinderGeometry(.31, .31, .025, 36), rim);
  disk.rotation.x = Math.PI / 2;
  disk.position.set(x, .51, side);
  group.add(disk);
  for (let i = 0; i < 5; i++) {
    const spokeMesh = addBox(group, .075, .38, .032, spoke, x, .51, side + (z > 0 ? .02 : -.02));
    spokeMesh.rotation.z = (i / 5) * Math.PI;
  }
  const hub = new THREE.Mesh(new THREE.CylinderGeometry(.08, .08, .04, 24), spoke);
  hub.rotation.x = Math.PI / 2;
  hub.position.set(x, .51, side + (z > 0 ? .05 : -.05));
  group.add(hub);
}
function makeCar(category, name) {
  const group = new THREE.Group();
  const paint = new THREE.MeshPhysicalMaterial({ color: 0xc9e4ee, metalness: .72, roughness: .22, clearcoat: 1, clearcoatRoughness: .08 });
  const glass = new THREE.MeshPhysicalMaterial({ color: 0x0b2134, metalness: .35, roughness: .09, transmission: .12, transparent: true, opacity: .92 });
  const trim = new THREE.MeshStandardMaterial({ color: 0x142232, metalness: .7, roughness: .3 });
  const chrome = new THREE.MeshStandardMaterial({ color: 0xd0e8f0, metalness: .9, roughness: .17 });
  const lamp = new THREE.MeshBasicMaterial({ color: 0xe2f7ff });
  const rearLamp = new THREE.MeshBasicMaterial({ color: 0xff455f });
  const isSuv = category === 'suv';
  const isSport = category === 'performance';
  const electric = category === 'electric';
  const roof = isSuv ? 2.25 : isSport ? 1.62 : 1.9;
  const length = isSport ? 5.9 : isSuv ? 5.5 : 5.7;
  const half = length / 2;
  const wheelX = isSport ? 1.89 : 1.73;
  const lower = isSport ? .76 : .83;

  // A simple original concept model with three body profiles, not a manufacturer scan.
  addBox(group, length - .15, .56, 2.28, paint, 0, lower, 0);
  const roofPoints = isSuv
    ? [[-1.75, 1.16], [-1.34, roof], [1.23, roof], [1.87, 1.18]]
    : isSport
      ? [[-1.67, 1.12], [-.92, roof], [.72, roof], [1.58, 1.12]]
      : [[-1.67, 1.15], [-1.02, roof], [.99, roof], [1.66, 1.15]];
  addShape(group, roofPoints, 2.12, paint, 0, .08);
  const sideGlass = isSuv
    ? [[-1.51, 1.35], [-1.2, roof - .16], [1.09, roof - .16], [1.62, 1.35]]
    : isSport
      ? [[-1.37, 1.3], [-.8, roof - .12], [.63, roof - .12], [1.25, 1.3]]
      : [[-1.4, 1.31], [-.9, roof - .13], [.85, roof - .13], [1.36, 1.31]];
  for (const side of [-1, 1]) {
    addShape(group, sideGlass, .023, glass, side * 1.102, 0);
    addBox(group, .055, roof - 1.21, .035, chrome, .08, (roof + 1.22) / 2, side * 1.13);
    addBox(group, length - .35, .045, .07, chrome, 0, 1.24, side * 1.17);
    addBox(group, .33, .11, .22, paint, .63, 1.33, side * 1.29);
    addBox(group, .57, .06, .17, trim, .73, 1.27, side * 1.27);
    addWheel(group, -wheelX, side * 1.16);
    addWheel(group, wheelX, side * 1.16);
    addBox(group, .44, .13, .035, chrome, .16, 1.02, side * 1.17);
  }
  addBox(group, .09, .42, 1.42, trim, half - .01, .88, 0);
  addBox(group, .1, .27, electric ? 1.48 : 1.18, electric ? trim : chrome, half + .045, .97, 0);
  addBox(group, .08, .14, 1.76, trim, half + .01, .57, 0);
  for (const side of [-1, 1]) {
    addBox(group, .08, .15, .48, lamp, half + .05, 1.14, side * .82);
    addBox(group, .08, .13, .56, rearLamp, -half - .045, 1.09, side * .79);
  }
  if (!electric) {
    const badge = new THREE.Group();
    const ring = new THREE.Mesh(new THREE.TorusGeometry(.19, .017, 6, 24), chrome);
    ring.rotation.y = Math.PI / 2;
    badge.add(ring);
    for (let i = 0; i < 3; i++) {
      const angle = i * Math.PI * 2 / 3 + Math.PI / 2;
      const spokeMesh = new THREE.Mesh(new THREE.BoxGeometry(.015, .014, .21), chrome);
      spokeMesh.rotation.x = angle;
      spokeMesh.position.set(0, Math.cos(angle) * .065, Math.sin(angle) * .065);
      badge.add(spokeMesh);
    }
    badge.position.set(half + .105, .97, 0);
    group.add(badge);
  }
  if (isSport) addBox(group, .55, .055, 1.75, trim, -half + .13, 1.16, 0);
  if (electric) addBox(group, .05, .025, 1.75, new THREE.MeshBasicMaterial({ color: 0x5deeff }), half + .095, 1.3, 0);
  group.userData.name = name;
  return { group, paint };
}
function initialize() {
  scene = new THREE.Scene();
  scene.background = new THREE.Color(0x09111e);
  scene.fog = new THREE.Fog(0x09111e, 13, 24);
  camera = new THREE.PerspectiveCamera(40, 1, .1, 50);
  camera.position.set(6.7, 3.2, 6.7);
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false, powerPreference: 'high-performance' });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  stage.replaceChildren(renderer.domElement);
  controls = new OrbitControls(camera, renderer.domElement);
  controls.target.set(0, 1.05, 0);
  controls.enableDamping = true;
  controls.enablePan = false;
  controls.minDistance = 5;
  controls.maxDistance = 13;
  controls.minPolarAngle = .35;
  controls.maxPolarAngle = 1.54;
  controls.autoRotate = !reducedMotion;
  controls.autoRotateSpeed = 1.15;
  controls.update();
  scene.add(new THREE.AmbientLight(0xc9e8ff, 1.5));
  const key = new THREE.DirectionalLight(0xffffff, 4.1);
  key.position.set(4, 8, 5);
  key.castShadow = true;
  key.shadow.mapSize.set(1024, 1024);
  key.shadow.camera.left = -8; key.shadow.camera.right = 8;
  key.shadow.camera.top = 8; key.shadow.camera.bottom = -8;
  scene.add(key);
  const rim = new THREE.PointLight(0x57d9ff, 100);
  rim.position.set(-3, 2.3, -4);
  scene.add(rim);
  const violet = new THREE.PointLight(0x8478ff, 55);
  violet.position.set(3, 1.7, -4);
  scene.add(violet);
  const ground = new THREE.Mesh(new THREE.PlaneGeometry(200, 200), new THREE.MeshStandardMaterial({ color: 0x0c1726, metalness: .2, roughness: .7 }));
  ground.rotation.x = -Math.PI / 2;
  ground.position.y = .02;
  ground.receiveShadow = true;
  scene.add(ground);
  const halo = new THREE.Mesh(new THREE.RingGeometry(3.5, 3.53, 80), new THREE.MeshBasicMaterial({ color: 0x2b87aa, transparent: true, opacity: .6, side: THREE.DoubleSide }));
  halo.rotation.x = -Math.PI / 2;
  halo.position.y = .032;
  scene.add(halo);
  resizeObserver = new ResizeObserver(resize);
  resizeObserver.observe(stage);
  resize();
}
function resize() {
  if (!renderer || !stage) return;
  const width = Math.max(stage.clientWidth, 1);
  const height = Math.max(stage.clientHeight, 1);
  camera.aspect = width / height;
  camera.updateProjectionMatrix();
  renderer.setSize(width, height, false);
}
function animate() {
  animationId = requestAnimationFrame(animate);
  controls.update();
  renderer.render(scene, camera);
}
function destroyModel() {
  if (!model) return;
  scene.remove(model);
  model.traverse(object => {
    if (object.geometry) object.geometry.dispose();
    if (object.material) {
      for (const material of [object.material].flat()) material.dispose();
    }
  });
  model = null;
}
function openViewer(button) {
  title.textContent = button.dataset.carName;
  dialog.showModal();
  try {
    if (!renderer) initialize();
    destroyModel();
    const result = makeCar(button.dataset.carCategory, button.dataset.carName);
    model = result.group;
    paintMaterial = result.paint;
    scene.add(model);
    camera.position.set(6.7, 3.2, 6.7);
    controls.target.set(0, 1.05, 0);
    controls.autoRotate = !reducedMotion;
    controls.update();
    spinButton.classList.toggle('is-active', controls.autoRotate);
    spinButton.setAttribute('aria-pressed', String(controls.autoRotate));
    spinButton.textContent = 'Auto spin: ' + (controls.autoRotate ? 'on' : 'off');
    const firstPaint = document.querySelector('.paint-options button');
    firstPaint?.click();
    resize();
    if (!animationId) animate();
  } catch (error) {
    console.error('3D preview failed:', error);
    stage.innerHTML = '<div class="viewer-loading">3D preview is unavailable on this device.</div>';
    renderer = null;
  }
}
for (const button of document.querySelectorAll('.spin-trigger')) {
  button.addEventListener('click', () => openViewer(button));
}
document.querySelector('.viewer-close')?.addEventListener('click', () => dialog.close());
dialog?.addEventListener('click', event => {
  if (event.target === dialog) dialog.close();
});
dialog?.addEventListener('close', () => {
  if (animationId) cancelAnimationFrame(animationId);
  animationId = null;
});
document.querySelector('#viewer-reset')?.addEventListener('click', () => {
  if (!controls) return;
  camera.position.set(6.7, 3.2, 6.7);
  controls.target.set(0, 1.05, 0);
  controls.update();
});
spinButton?.addEventListener('click', () => {
  if (!controls) return;
  controls.autoRotate = !controls.autoRotate;
  spinButton.classList.toggle('is-active', controls.autoRotate);
  spinButton.setAttribute('aria-pressed', String(controls.autoRotate));
  spinButton.textContent = 'Auto spin: ' + (controls.autoRotate ? 'on' : 'off');
});
for (const button of document.querySelectorAll('.paint-options button')) {
  button.addEventListener('click', () => {
    if (paintMaterial) paintMaterial.color.set(button.dataset.paint);
    for (const other of document.querySelectorAll('.paint-options button')) {
      const selected = button === other;
      other.classList.toggle('is-active', selected);
      other.setAttribute('aria-pressed', String(selected));
    }
  });
}

