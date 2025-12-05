<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
requireLogin();

$db = (new Database())->getConnection();

$deptId = (int)($_GET['dept_id'] ?? 0);
$labId  = (int)($_GET['lab_id'] ?? 0);

$department = null;
$lab        = null;

if ($deptId) {
    $sd = $db->prepare("SELECT * FROM departments WHERE id = :id");
    $sd->execute([':id' => $deptId]);
    $department = $sd->fetch(PDO::FETCH_ASSOC);
}
if ($labId) {
    $sl = $db->prepare("SELECT * FROM labs WHERE id = :id");
    $sl->execute([':id' => $labId]);
    $lab = $sl->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = 'Route Demo';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-2">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 text-lg">←</button>
      <div>
        <p class="text-[11px] text-slate-500 uppercase">Route demo</p>
        <h1 class="text-sm font-semibold">
          <?= $department ? htmlspecialchars($department['name']) : 'Sample route' ?>
        </h1>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-4">
    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-2">
      <p class="text-xs text-slate-500">
        Block-based map (no Google Maps). Blue: path; colored blocks: buildings.
      </p>
    </section>

    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-3">
      <!-- CUSTOM BLOCK MAP -->
      <div class="relative w-full aspect-[3/4] bg-slate-50 rounded-xl border border-slate-200 overflow-hidden p-3">
        <div class="grid grid-rows-5 grid-cols-3 gap-1 h-full">
          <!-- Row 1 -->
          <div class="row-span-1 col-span-3 flex items-center justify-center text-[10px] text-slate-500">
            Main Gate
          </div>

          <!-- Row 2: simple vertical path down center -->
          <div></div>
          <div class="bg-blue-200 rounded-full mx-auto w-1 h-full"></div>
          <div></div>

          <!-- Row 3: Department block in center -->
          <div></div>
          <div class="bg-emerald-100 rounded-md flex items-center justify-center text-[10px] text-emerald-700 font-semibold">
            <?= $department ? htmlspecialchars($department['code']) : 'DEPT' ?>
          </div>
          <div></div>

          <!-- Row 4: path from dept to lab (right) -->
          <div></div>
          <div class="flex items-center justify-center">
            <div class="h-1 w-full bg-blue-200 rounded-full"></div>
          </div>
          <div class="bg-orange-100 rounded-md flex items-center justify-center text-[10px] text-orange-700 font-semibold">
            <?= $lab ? htmlspecialchars($lab['room_no'] ?: 'LAB') : 'LAB' ?>
          </div>

          <!-- Row 5: label -->
          <div class="row-span-1 col-span-3 flex flex-col items-center justify-center text-[10px] text-slate-500">
            <span>Route: Gate → Dept → Lab</span>
            <?php if ($lab): ?>
              <span class="mt-1 text-[10px] text-slate-600">
                Destination: <?= htmlspecialchars($lab['name']) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <p class="text-[11px] text-slate-500">
        This is a simplified visual for demo. You can later replace the static layout with 
        dynamic JS-based blocks/routes.
      </p>
    </section>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
