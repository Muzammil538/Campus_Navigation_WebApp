<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();
?>
<header class="bg-white shadow-sm sticky top-0 z-40">
  <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
    <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg -ml-1">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
    <div class="flex-1 text-center">
      <h1 class="text-sm font-semibold"><?= htmlspecialchars($pageTitle ?? 'Campus Navigator') ?></h1>
    </div>
    <div class="w-10"></div>
  </div>
</header>
