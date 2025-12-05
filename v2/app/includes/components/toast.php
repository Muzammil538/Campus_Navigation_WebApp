<?php if (isset($_SESSION['toast'])): ?>
<div class="fixed top-4 right-4 z-50 bg-white shadow-2xl rounded-2xl px-4 py-3 border max-w-sm w-full animate-slide-in">
  <div class="flex items-center justify-between">
    <span class="text-sm font-medium"><?= htmlspecialchars($_SESSION['toast']) ?></span>
    <button onclick="this.parentElement.parentElement.remove()" class="text-slate-400 hover:text-slate-600">
      ✕
    </button>
  </div>
</div>
<?php unset($_SESSION['toast']); ?>
<?php endif; ?>
<style>
@keyframes slide-in { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
