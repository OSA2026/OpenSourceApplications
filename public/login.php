<?php
$page_title = "Login";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (login_user($user)) {
                set_flash_message('success', 'Welcome back, ' . $user['username'] . '!');
                if ($user['role'] === 'admin') {
                    header('Location: ../admin/index.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                set_flash_message('error', 'Your account has been suspended. Please contact support.');
            }
        } else {
            set_flash_message('error', 'Invalid username/email or password.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center py-12 px-6">
    <div class="max-w-md w-full glass-card p-10 rounded-3xl border border-white/10 relative overflow-hidden">
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-cyber-purple/10 blur-[50px] rounded-full"></div>
        
        <div class="text-center mb-10">
            <a href="index.php" class="inline-flex items-center space-x-2 mb-6">
                <div class="w-8 h-8 bg-cyber-blue rounded flex items-center justify-center">
                    <span class="text-black font-bold text-xl">O</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">OSA <span class="text-cyber-blue">Studio</span></span>
            </a>
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-gray-400">Access your developer dashboard.</p>
        </div>

        <?php display_flash_message(); ?>

        <form action="login.php" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Username or Email</label>
                <input type="text" name="login" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-400">Password</label>
                    <a href="#" class="text-xs text-cyber-blue hover:underline">Forgot?</a>
                </div>
                <input type="password" name="password" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition shadow-[0_0_15px_rgba(0,210,255,0.4)]">
                Login
            </button>
        </form>

        <p class="mt-8 text-center text-gray-400 text-sm">
            Don't have an account? <a href="signup.php" class="text-cyber-blue hover:underline">Sign up here</a>
        </p>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
