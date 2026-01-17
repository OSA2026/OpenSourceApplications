<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

// Fetch overall stats
$project_stats = $pdo->query("SELECT status, COUNT(*) as count FROM projects GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'developer'")->fetchColumn();
$recent_submissions = $pdo->query("SELECT p.*, u.username FROM projects p JOIN users u ON p.developer_id = u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
$recent_contacts = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$page_title = "Admin Dashboard";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-2">System Overview</h1>
            <p class="text-gray-500">Monitor studio activity and moderate submissions.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="glass-card p-6 rounded-2xl border border-white/5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Pending Review</p>
                <p class="text-4xl font-bold text-yellow-500"><?php echo $project_stats['pending'] ?? 0; ?></p>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Live Projects</p>
                <p class="text-4xl font-bold text-cyber-blue"><?php echo $project_stats['approved'] ?? 0; ?></p>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Total Developers</p>
                <p class="text-4xl font-bold text-white"><?php echo $total_users; ?></p>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Active Messages</p>
                <p class="text-4xl font-bold text-cyber-purple"><?php echo count($recent_contacts); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Recent Submissions -->
            <section class="glass-card rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-white font-bold">Recent Submissions</h3>
                    <a href="submissions.php" class="text-xs text-cyber-blue hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 text-[10px] text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Developer</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-400 divide-y divide-white/5">
                            <?php foreach ($recent_submissions as $p): ?>
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 font-medium text-white"><?php echo htmlspecialchars($p['title']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($p['username']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full border <?php echo $p['status'] === 'pending' ? 'text-yellow-500 border-yellow-500/20' : 'text-gray-500 border-white/10'; ?>">
                                            <?php echo $p['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs"><?php echo date('M d', strtotime($p['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Contact Messages -->
            <section class="glass-card rounded-2xl border border-white/5 overflow-hidden">
                <div class="p-6 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-white font-bold">Inbound Inquiries</h3>
                    <a href="contacts.php" class="text-xs text-cyber-purple hover:underline">View All</a>
                </div>
                <div class="p-6 space-y-4">
                    <?php if (empty($recent_contacts)): ?>
                        <p class="text-gray-500 text-sm">No new messages.</p>
                    <?php else: ?>
                        <?php foreach ($recent_contacts as $c): ?>
                            <div class="p-4 bg-white/5 rounded-xl border border-white/5">
                                <div class="flex justify-between mb-1">
                                    <span class="text-white font-bold text-xs"><?php echo htmlspecialchars($c['name']); ?></span>
                                    <span class="text-[10px] text-gray-500"><?php echo date('M d, H:i', strtotime($c['created_at'])); ?></span>
                                </div>
                                <p class="text-xs text-gray-400 line-clamp-1"><?php echo htmlspecialchars($c['message']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
