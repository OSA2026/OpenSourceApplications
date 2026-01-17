<?php
$page_title = "Update Password";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$token = $_GET['token'] ?? '';
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid or expired token.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($password) >= 8 && $password === $confirm) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
            if ($stmt->execute([$hashed, $token])) {
                set_flash_message('success', 'Password updated. You can now login.');
                header('Location: login.php');
                exit;
            }
        } else {
            set_flash_message('error', 'Passwords must match and be at least 8 chars.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full glass-card p-10 rounded-3xl border border-white/10">
        <h1 class="text-2xl font-bold text-white mb-8">Set New Password</h1>

        <?php display_flash_message(); ?>

        <form method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">New Password (Min 8)</label>
                <input type="password" name="password" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition">
                Update Password
            </button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
