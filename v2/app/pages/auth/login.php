<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

if (isLoggedIn()) {
    redirect('/app/pages/home/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            redirect('/app/pages/home/index.php');
        } else {
            $error = 'Invalid credentials.';
        }
    }
}

$pageTitle = 'Login';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 space-y-6">
    <div class="text-center space-y-2">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brandSoft text-brand">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="currentColor" stroke-width="2"/>
        </svg>
      </div>
      <h1 class="text-2xl font-semibold">Welcome back</h1>
      <p class="text-sm text-slate-500">Sign in to Campus Navigator</p>
    </div>

    <?php if ($error): ?>
      <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div class="space-y-1">
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input
          type="email" name="email" required
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:bg-white"
          placeholder="you@college.edu"
        />
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-slate-700">Password</label>
        <input
          type="password" name="password" required
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:bg-white"
          placeholder="••••••••"
        />
      </div>

      <button
        type="submit"
        class="w-full inline-flex items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 transition"
      >
        Sign in
      </button>
    </form>

    <p class="text-center text-xs text-slate-500">
      Don’t have an account?
      <a href="../auth/signup.php" class="text-brand hover:underline">Sign up</a>
    </p>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
