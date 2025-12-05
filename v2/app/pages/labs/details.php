<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/app/pages/search/index.php');

$db = (new Database())->getConnection();

$stmt = $db->prepare("
  SELECT l.*, d.name AS dept_name, d.code AS dept_code, d.id AS dept_id
  FROM labs l
  JOIN departments d ON d.id = l.department_id
  WHERE l.id = :id
");
$stmt->execute([':id' => $id]);
$lab = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lab) redirect('/app/pages/search/index.php');

$pageTitle = 'Lab';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-2">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 text-lg">←</button>
      <div>
        <p class="text-[11px] text-slate-500 uppercase">Lab / Room</p>
        <h1 class="text-sm font-semibold"><?= htmlspecialchars($lab['name']) ?></h1>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-4">
    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-2">
      <p class="text-xs font-medium text-slate-500">Department</p>
      <p class="text-sm font-semibold">
        <?= htmlspecialchars($lab['dept_name']) ?> (<?= htmlspecialchars($lab['dept_code']) ?>)
      </p>

      <div class="mt-3 space-y-1">
        <p class="text-xs font-medium text-slate-500">Room number</p>
        <p class="text-sm"><?= htmlspecialchars($lab['room_no'] ?: '—') ?></p>
      </div>

      <?php if (!empty($lab['description'])): ?>
        <div class="mt-3 space-y-1">
          <p class="text-xs font-medium text-slate-500">Description</p>
          <p class="text-xs text-slate-600"><?= nl2br(htmlspecialchars($lab['description'])) ?></p>
        </div>
      <?php endif; ?>
    </section>

    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-3">
      <h2 class="text-sm font-semibold">Route demo</h2>
      <p class="text-xs text-slate-500">
        This will show a simple block-based path from main gate → department → this lab.
      </p>
      <a
        href="../routes/demo.php?dept_id=<?= $lab['dept_id'] ?>&lab_id=<?= $lab['id'] ?>"
        class="inline-flex w-full items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 transition"
      >
        Show route
      </a>
    </section>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
