<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$logs = $pdo->query("SELECT al.*, u.username as admin_name, p.title as project_name 
                    FROM admin_logs al 
                    JOIN users u ON al.admin_id = u.id 
                    LEFT JOIN projects p ON al.project_id = p.id 
                    ORDER BY al.created_at DESC LIMIT 100")->fetchAll();

$page_title = "Audit Logs";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-2">Audit Logs</h1>
            <p class="text-gray-500">System-wide trail of administrative actions.</p>
        </div>

        <section class="glass-card rounded-2xl border border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Timestamp</th>
                            <th class="px-6 py-4">Administrator</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">Target Context</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-400 divide-y divide-white/5 font-mono">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center text-gray-600">No logs found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-4 text-xs whitespace-nowrap"><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td class="px-6 py-4 text-cyber-blue"><?php echo htmlspecialchars($log['admin_name']); ?></td>
                                    <td class="px-6 py-4 text-white"><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td class="px-6 py-4 text-xs">
                                        <?php if ($log['project_name']): ?>
                                            <span class="text-cyber-purple">Project:</span> <?php echo htmlspecialchars($log['project_name']); ?>
                                        <?php else: ?>
                                            <span class="text-gray-600">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
