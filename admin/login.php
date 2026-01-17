<?php
$page_title = "Admin Login";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if (is_admin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', get_csrf_error_message());
    } else {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin'");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (login_user($user)) {
                header('Location: index.php');
                exit;
            } else {
                set_flash_message('error', 'Your admin account is suspended.');
            }
        } else {
            set_flash_message('error', 'Invalid admin credentials.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center bg-cyber-black">
    <div class="max-w-md w-full glass-card p-10 rounded-3xl border border-cyber-purple/20">
        <div class="text-center mb-10">
            <div class="w-12 h-12 bg-cyber-purple rounded flex items-center justify-center mx-auto mb-6 shadow-[0_0_20px_rgba(112,0,255,0.4)]">
                <span class="text-white font-bold text-xl">A</span>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2 uppercase tracking-widest">Admin Access</h1>
            <p class="text-gray-500 text-sm italic">Authorized personnel only.</p>
        </div>

        <?php display_flash_message(); ?>

        <form action="login.php" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username</label>
                <input type="text" name="login" required class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-purple transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-purple transition">
            </div>
            <button type="submit" class="w-full bg-cyber-purple text-white font-bold py-4 rounded-xl hover:bg-cyber-purple/80 transition shadow-[0_0_15px_rgba(112,0,255,0.3)]">
                Authenticate
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="../public/index.php" class="text-xs text-gray-600 hover:text-white transition">← Return to Studio</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
