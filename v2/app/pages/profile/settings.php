<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: " . APP_URL . "/pages/auth/login.php");
    exit;
}

$pageTitle = 'Settings';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen bg-slate-100 flex flex-col pb-20">
  <header class="bg-white shadow-sm">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-2">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600 text-lg">←</button>
      <h1 class="text-sm font-semibold">Settings</h1>
    </div>
  </header>

  <main class="flex-1 max-w-md mx-auto w-full px-4 py-4 space-y-3">
    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-3">
      <h2 class="text-sm font-semibold">Profile</h2>
      <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
        <div class="w-12 h-12 rounded-2xl bg-brandSoft flex items-center justify-center">
          <span class="text-brand font-semibold text-sm">
            <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 2)) ?>
          </span>
        </div>
        <div>
          <p class="font-semibold text-sm"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
          <p class="text-xs text-slate-500"><?= strtoupper($_SESSION['user_role'] ?? 'STUDENT') ?></p>
        </div>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow-sm p-4 space-y-3">
      <h2 class="text-sm font-semibold">Preferences</h2>
      <div class="space-y-3">
        <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100">
          <span class="text-sm">Voice guidance</span>
          <div class="relative w-11 h-6 bg-slate-200 rounded-full">
            <input type="checkbox" class="sr-only peer">
            <div class="w-5 h-5 bg-white rounded-full shadow absolute top-0.5 left-0.5 transition-all duration-200 peer-checked:translate-x-5 peer-checked:bg-brand"></div>
          </div>
        </label>
        <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 cursor-pointer hover:bg-slate-100">
          <span class="text-sm">High contrast</span>
          <div class="relative w-11 h-6 bg-slate-200 rounded-full">
            <input type="checkbox" class="sr-only peer">
            <div class="w-5 h-5 bg-white rounded-full shadow absolute top-0.5 left-0.5 transition-all duration-200 peer-checked:translate-x-5 peer-checked:bg-brand"></div>
          </div>
        </label>
      </div>
    </section>

    <form method="POST" class="bg-white rounded-2xl shadow-sm p-4">
      <button type="submit" name="logout" class="w-full flex items-center justify-center gap-2 text-sm text-rose-600 hover:bg-rose-50 p-3 rounded-xl transition-colors">
        🚪 Logout
      </button>
    </form>
  </main>

<?php include __DIR__ . '/../../includes/bottom-nav.php'; ?>
<script src="../../js/app.js"></script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
