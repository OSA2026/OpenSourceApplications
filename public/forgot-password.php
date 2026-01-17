<?php
$page_title = "Forgot Password";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $email = trim($_POST['email'] ?? '');
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);

            // Simulation: in production, an email would be sent here.
            $reset_link = BASE_URL . "/public/reset-password.php?token=" . $token;
            set_flash_message('info', 'Password reset instructions sent (Simulated). Link: <a href="'.$reset_link.'" class="underline">'.$reset_link.'</a>');
        } else {
            // Security: don't reveal if email exists
            set_flash_message('info', 'If that email exists, instructions have been sent.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full glass-card p-10 rounded-3xl border border-white/10">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-white mb-2">Reset Password</h1>
            <p class="text-gray-400">Enter your email and we'll send a recovery link.</p>
        </div>

        <?php display_flash_message(); ?>

        <form action="forgot-password.php" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition">
                Send Recovery Link
            </button>
        </form>

        <p class="mt-8 text-center text-gray-500 text-sm">
            Remembered? <a href="login.php" class="text-cyber-blue hover:underline">Back to login</a>
        </p>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
