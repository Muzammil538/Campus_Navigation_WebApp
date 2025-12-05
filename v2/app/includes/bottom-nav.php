<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$pages = [
  'home' => '/app/pages/home/index.php',
  'search' => '/app/pages/search/index.php',
  'routes' => '/app/pages/routes/demo.php',
  'profile' => '/app/pages/profile/settings.php'
];
?>

<nav class="bg-white border-t border-slate-200 px-4 py-2 fixed bottom-0 left-0 right-0 max-w-md mx-auto z-50">
  <div class="flex justify-around items-center text-xs">
    <a href="<?= $pages['home'] ?>" class="flex flex-col items-center p-2 rounded-xl <?= strpos($currentPage, 'home') !== false ? 'text-brand' : 'text-slate-500' ?>">
      <span class="text-lg mb-1">🏠</span>
      <span>Home</span>
    </a>
    <a href="<?= $pages['search'] ?>" class="flex flex-col items-center p-2 rounded-xl <?= strpos($currentPage, 'search') !== false ? 'text-brand' : 'text-slate-500' ?>">
      <span class="text-lg mb-1">🔍</span>
      <span>Search</span>
    </a>
    <a href="<?= $pages['routes'] ?>" class="flex flex-col items-center p-2 rounded-xl <?= strpos($currentPage, 'routes') !== false ? 'text-brand' : 'text-slate-500' ?>">
      <span class="text-lg mb-1">➝</span>
      <span>Route</span>
    </a>
    <a href="<?= $pages['profile'] ?>" class="flex flex-col items-center p-2 rounded-xl <?= strpos($currentPage, 'profile') !== false ? 'text-brand' : 'text-slate-500' ?>">
      <span class="text-lg mb-1">⚙️</span>
      <span>Profile</span>
    </a>
  </div>
</nav>

<style>
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}
</style>
