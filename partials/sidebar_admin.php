<?php
// partials/sidebar_admin.php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
?>
<aside class="w-64 glass-card border-r border-white/10 h-screen sticky top-0 flex flex-col">
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center space-x-2">
            <div class="w-6 h-6 bg-cyber-purple rounded flex items-center justify-center">
                <span class="text-white font-bold text-sm">A</span>
            </div>
            <span class="font-bold text-white tracking-widest text-xs uppercase">OSA ADMIN</span>
        </div>
    </div>
    
    <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Dashboard</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/submissions.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Submissions</span>
            <?php
            // Count pending
            require_once __DIR__ . '/../includes/db.php';
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects WHERE status = 'pending'");
            $pending_count = $stmt->fetch()['count'];
            if ($pending_count > 0):
            ?>
                <span class="ml-auto bg-cyber-blue text-black text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Users</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/taxonomies.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Taxonomies</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/contacts.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Contact Messages</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/logs.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/5 transition text-sm">
            <span>Audit Logs</span>
        </a>
    </nav>
    
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center space-x-3 px-4 py-3 text-xs text-gray-500">
            <span>Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-red-900/20 text-red-400 transition text-sm">
            <span>Log Out</span>
        </a>
    </div>
</aside>
