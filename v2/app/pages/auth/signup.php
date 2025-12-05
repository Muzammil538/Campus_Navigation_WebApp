<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

if (isLoggedIn()) {
    redirect('/app/pages/home/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['full_name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'student';

    if (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } else {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $db->prepare("INSERT INTO users (full_name,email,password,role) VALUES (:n,:e,:p,:r)");
            $ins->execute([
                ':n' => $name,
                ':e' => $email,
                ':p' => $hash,
                ':r' => $role,
            ]);
            redirect('/app/pages/auth/login.php');
        }
    }
}

$pageTitle = 'Sign Up';
include __DIR__ . '/../../includes/head.php';
?>

<div class="min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 space-y-6">
    <div class="flex items-center justify-between">
      <button onclick="history.back()" class="text-slate-400 hover:text-slate-600">
        ←
      </button>
      <div class="text-center flex-1">
        <h1 class="text-xl font-semibold">Create account</h1>
        <p class="text-xs text-slate-500 mt-1">Join Campus Navigator</p>
      </div>
      <div class="w-6"></div>
    </div>

    <?php if ($error): ?>
      <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div class="space-y-1">
        <label class="block text-sm font-medium text-slate-700">Full name</label>
        <input
          type="text" name="full_name" required
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:bg-white"
          placeholder="John Doe"
        />
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-slate-700">Email</label>
        <input
          type="email" name="email" required
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:bg-white"
          placeholder="you@college.edu"
        />
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-slate-700">Role</label>
        <select
          name="role"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:bg-white"
        >
          <option value="student">Student</option>
          <option value="staff">Staff</option>
          <option value="visitor">Visitor</option>
        </select>
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
        Sign up
      </button>
    </form>

    <p class="text-center text-xs text-slate-500">
      Already have an account?
      <a href="../auth/login.php" class="text-brand hover:underline">Sign in</a>
    </p>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
