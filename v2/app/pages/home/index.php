<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth-guard.php';

$pageTitle = 'Campus Home';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col pb-20">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto flex items-center justify-between px-4 py-3">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-brandSoft flex items-center justify-center text-brand">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <div>
          <p class="text-xs text-slate-500">Hi,</p>
          <p class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Student') ?></p>
        </div>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-4">
    <section class="bg-white rounded-2xl shadow-sm p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold">Campus Map</h2>
        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Live demo</span>
      </div>

      <!-- DYNAMIC BLOCK MAP -->
      <div id="blockMap" class="relative w-full aspect-[3/4] bg-slate-50 rounded-xl border border-slate-200 overflow-hidden p-2">
        <div class="grid grid-cols-3 gap-1 h-full">
          <div data-block="gate" class="bg-slate-200 rounded-md flex items-center justify-center text-[10px] text-slate-700 font-semibold row-span-2">Gate</div>
          <div data-block="cse" class="bg-blue-100 rounded-md flex items-center justify-center text-[10px] text-blue-700 font-semibold">CSE</div>
          <div data-block="ece" class="bg-emerald-100 rounded-md flex items-center justify-center text-[10px] text-emerald-700 font-semibold">ECE</div>
          <div data-block="library" class="bg-orange-100 rounded-md flex items-center justify-center text-[10px] text-orange-700 font-semibold">Lib</div>
          <div data-block="lab1" class="bg-sky-100 rounded-md flex items-center justify-center text-[10px] text-sky-700 font-semibold">Lab A</div>
          <div data-block="admin" class="bg-lime-100 rounded-md flex items-center justify-center text-[10px] text-lime-700 font-semibold">Admin</div>
          <div data-block="path1" class="bg-slate-200 rounded-md flex items-center justify-center text-[10px] text-slate-700 font-semibold">Path</div>
          <div data-block="hostel" class="bg-rose-100 rounded-md flex items-center justify-center text-[10px] text-rose-700 font-semibold">Hostel</div>
        </div>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-3">
      <h3 class="text-sm font-semibold">Quick start</h3>
      <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="../search/" class="flex flex-col items-center rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
          <span class="text-lg mb-1">🔍</span>
          <span>Search depts</span>
        </a>
        <a href="../routes/demo.php" class="flex flex-col items-center rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
          <span class="text-lg mb-1">➝</span>
          <span>Route demo</span>
        </a>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/../../includes/bottom-nav.php'; ?>
<script src="../../js/map-blocks.js"></script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
