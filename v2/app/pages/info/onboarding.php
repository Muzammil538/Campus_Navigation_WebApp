<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();

$_SESSION['onboarding_complete'] = true;
$pageTitle = 'Welcome';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-gradient-to-br from-brand to-brandSoft flex flex-col items-center justify-center px-4 pb-20 text-white">
  <div class="text-center space-y-6 max-w-sm">
    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-6">
      <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    
    <div class="space-y-3">
      <h1 class="text-2xl font-bold">Welcome to Campus Navigator!</h1>
      <p class="text-sm opacity-90 leading-relaxed">
        Search for departments and labs. Get simple block-based routes to navigate campus easily.
      </p>
    </div>
    
    <div class="space-y-2 text-xs opacity-90">
      <p>👉 Search → Department → Lab → Route</p>
      <p>📱 Use bottom navigation to switch screens</p>
    </div>
    
    <a href="../home/" class="block w-full bg-white text-brand font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm">
      Start exploring
    </a>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
