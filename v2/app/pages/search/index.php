<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
requireLogin();

$db = (new Database())->getConnection();

$q = trim($_GET['q'] ?? '');
$departments = [];

if ($q !== '') {
    $stmt = $db->prepare("SELECT * FROM departments WHERE name LIKE :q OR code LIKE :q ORDER BY name");
    $stmt->execute([':q' => "%{$q}%"]);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->query("SELECT * FROM departments ORDER BY name");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = 'Search';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-2">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 text-lg">←</button>
      <form method="GET" class="flex-1 flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2">
        <span class="text-slate-400 text-sm">🔍</span>
        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($q) ?>"
          placeholder="Search department..."
          class="flex-1 bg-transparent border-none outline-none text-sm"
        />
      </form>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-3">
    <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
      Departments
    </h2>

    <?php if (!$departments): ?>
      <p class="text-sm text-slate-500 mt-2">No departments found.</p>
    <?php else: ?>
      <div class="space-y-2">
        <?php foreach ($departments as $d): ?>
          <a
            href="./department.php?id=<?= $d['id'] ?>"
            class="flex items-center justify-between bg-white rounded-xl px-3 py-2 shadow-sm hover:bg-slate-50"
          >
            <div>
              <p class="text-sm font-semibold"><?= htmlspecialchars($d['name']) ?></p>
              <p class="text-[11px] text-slate-500"><?= htmlspecialchars($d['code']) ?></p>
            </div>
            <span class="text-xs text-slate-400">›</span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
