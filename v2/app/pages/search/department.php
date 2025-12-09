<?php
chdir(dirname(__DIR__));
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(__DIR__.'/app/pages/search/index.php');

$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM departments WHERE id = :id");
$stmt->execute([':id' => $id]);
$department = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$department) redirect(__DIR__.'/app/pages/search/index.php');

$stmt = $db->prepare("SELECT * FROM labs WHERE department_id = :id ORDER BY name");
$stmt->execute([':id' => $id]);
$labs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Department';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-2">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 text-lg">←</button>
      <div>
        <p class="text-[11px] text-slate-500 uppercase">Department</p>
        <h1 class="text-sm font-semibold"><?= htmlspecialchars($department['name']) ?></h1>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-4">
    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-2">
      <p class="text-xs font-medium text-slate-500">Code</p>
      <p class="text-sm font-semibold"><?= htmlspecialchars($department['code']) ?></p>
      <?php if (!empty($department['description'])): ?>
        <p class="text-xs text-slate-500 mt-1">
          <?= nl2br(htmlspecialchars($department['description'])) ?>
        </p>
      <?php endif; ?>
    </section>

    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-2">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold">Labs / Rooms</h2>
        <span class="text-[11px] text-slate-500"><?= count($labs) ?> items</span>
      </div>

      <?php if (!$labs): ?>
        <p class="text-xs text-slate-500 mt-1">No labs/rooms configured yet.</p>
      <?php else: ?>
        <div class="space-y-2">
          <?php foreach ($labs as $lab): ?>
            <a
              href="../labs/detail.php?id=<?= $lab['id'] ?>"
              class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50"
            >
              <div>
                <p class="text-sm font-semibold"><?= htmlspecialchars($lab['name']) ?></p>
                <p class="text-[11px] text-slate-500">
                  Room: <?= htmlspecialchars($lab['room_no'] ?: '—') ?>
                </p>
              </div>
              <span class="text-xs text-brand">Route ›</span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
