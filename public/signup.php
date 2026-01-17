<?php
$page_title = "Sign Up";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/flash.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', get_csrf_error_message());
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (validate_required($username) && validate_email($email) && strlen($password) >= 8) {
            if ($password !== $confirm_password) {
                set_flash_message('error', 'Passwords do not match.');
            } else {
                // Check if user exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    set_flash_message('error', 'Username or Email already taken.');
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->beginTransaction();
                    try {
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'developer')");
                        $stmt->execute([$username, $email, $hashed_password]);
                        $user_id = $pdo->lastInsertId();

                        $stmt = $pdo->prepare("INSERT INTO developer_profiles (user_id) VALUES (?)");
                        $stmt->execute([$user_id]);

                        $pdo->commit();
                        set_flash_message('success', 'Registration successful! You can now login.');
                        header('Location: login.php');
                        exit;
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        set_flash_message('error', 'An error occurred. Please try again.');
                    }
                }
            }
        } else {
            set_flash_message('error', 'Please fill all fields correctly. Password must be at least 8 characters.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center py-12 px-6">
    <div class="max-w-md w-full glass-card p-10 rounded-3xl border border-white/10 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-cyber-blue/10 blur-[50px] rounded-full"></div>
        
        <div class="text-center mb-10">
            <a href="index.php" class="inline-flex items-center space-x-2 mb-6">
                <div class="w-8 h-8 bg-cyber-blue rounded flex items-center justify-center">
                    <span class="text-black font-bold text-xl">O</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">OSA <span class="text-cyber-blue">Studio</span></span>
            </a>
            <h1 class="text-3xl font-bold text-white mb-2">Join the Studio</h1>
            <p class="text-gray-400">Create a developer account to submit projects.</p>
        </div>

        <?php display_flash_message(); ?>

        <form action="signup.php" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Username</label>
                <input type="text" name="username" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition" placeholder="Min 8 characters">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition shadow-[0_0_15px_rgba(0,210,255,0.4)]">
                Create Account
            </button>
        </form>

        <p class="mt-8 text-center text-gray-400 text-sm">
            Already have an account? <a href="login.php" class="text-cyber-blue hover:underline">Login here</a>
        </p>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
