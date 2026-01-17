<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

require_admin();

// Handle User Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', get_csrf_error_message());
    } else {
        try {
            $user_id = $_POST['user_id'] ?? 0;
            $action = $_POST['action'];

            if ($action === 'suspend') {
                $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ? AND role != 'admin'");
                $stmt->execute([$user_id]);
                set_flash_message('success', 'User suspended.');
            } elseif ($action === 'activate') {
                $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->execute([$user_id]);
                set_flash_message('success', 'User activated.');
            } elseif ($action === 'verify') {
                $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                $stmt->execute([$user_id]);
                set_flash_message('success', 'User verified.');
            }
            
            // Log action
            $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, project_id) VALUES (?, ?, NULL)");
            $stmt->execute([$_SESSION['user_id'], "User Action: $action on user ID $user_id"]);
            
            header('Location: users.php');
            exit;
        } catch (Exception $e) {
            set_flash_message('error', 'Execution Error: ' . $e->getMessage());
        }
    }
}

$stmt = $pdo->query("SELECT u.*, dp.full_name, dp.avatar_path, (SELECT COUNT(*) FROM projects WHERE developer_id = u.id) as project_count 
                    FROM users u 
                    LEFT JOIN developer_profiles dp ON u.id = dp.user_id 
                    WHERE u.role = 'developer' 
                    ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();

$page_title = "User Management";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-2">User Management</h1>
            <p class="text-gray-500">Manage developer accounts and access rights.</p>
        </div>

        <?php display_flash_message(); ?>

        <section class="glass-card rounded-2xl border border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Projects</th>
                            <th class="px-6 py-4">Joined</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-400 divide-y divide-white/5">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-cyber-blue/10 border border-white/10 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            <?php if ($user['avatar_path']): ?>
                                                <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $user['avatar_path']; ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <span class="text-cyber-blue text-xs font-bold"><?php echo substr($user['username'], 0, 1); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full border w-fit <?php echo $user['status'] === 'active' ? 'text-green-500 border-green-500/20' : 'text-red-500 border-red-500/20'; ?>">
                                            <?php echo $user['status']; ?>
                                        </span>
                                        <?php if ($user['is_verified']): ?>
                                            <span class="text-[8px] text-cyber-blue font-bold uppercase tracking-widest flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Verified
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?php echo $user['project_count']; ?></td>
                                <td class="px-6 py-4 text-xs"><?php echo date('M Y', strtotime($user['created_at'])); ?></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <?php if (!$user['is_verified']): ?>
                                            <form method="POST" onsubmit="return confirm('Verify this developer?')">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="action" value="verify" class="text-xs bg-cyber-blue/10 text-cyber-blue hover:bg-cyber-blue hover:text-black px-3 py-1.5 rounded transition font-bold border border-cyber-blue/20">Verify</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['status'] === 'active'): ?>
                                            <form method="POST" onsubmit="return confirm('Suspend this user?')">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="action" value="suspend" class="text-xs bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded transition font-bold border border-red-500/20">Suspend</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST">
                                                <?php csrf_field(); ?>
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="action" value="activate" class="text-xs bg-green-500/10 text-green-400 hover:bg-green-500 hover:text-white px-3 py-1.5 rounded transition font-bold border border-green-500/20">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
