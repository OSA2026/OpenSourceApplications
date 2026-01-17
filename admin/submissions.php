<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$status_filter = $_GET['status'] ?? 'pending';
$params = [];

$query = "SELECT p.*, u.username, c.name as category_name 
          FROM projects p 
          JOIN users u ON p.developer_id = u.id 
          JOIN categories c ON p.category_id = c.id";

if ($status_filter) {
    $query .= " WHERE p.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$page_title = "Moderation Queue";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Moderation Queue</h1>
                <p class="text-gray-500">Review and verify project submissions.</p>
            </div>
            
            <div class="flex bg-white/5 p-1 rounded-xl border border-white/5">
                <a href="?status=pending" class="px-4 py-2 rounded-lg text-xs font-bold transition <?php echo $status_filter === 'pending' ? 'bg-cyber-blue text-black' : 'text-gray-400 hover:text-white'; ?>">Pending</a>
                <a href="?status=needs_fix" class="px-4 py-2 rounded-lg text-xs font-bold transition <?php echo $status_filter === 'needs_fix' ? 'bg-cyber-blue text-black' : 'text-gray-400 hover:text-white'; ?>">Needs Fix</a>
                <a href="?status=approved" class="px-4 py-2 rounded-lg text-xs font-bold transition <?php echo $status_filter === 'approved' ? 'bg-cyber-blue text-black' : 'text-gray-400 hover:text-white'; ?>">Approved</a>
                <a href="?status=rejected" class="px-4 py-2 rounded-lg text-xs font-bold transition <?php echo $status_filter === 'rejected' ? 'bg-cyber-blue text-black' : 'text-gray-400 hover:text-white'; ?>">Rejected</a>
            </div>
        </div>

        <section class="glass-card rounded-2xl border border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] text-gray-500 uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Project</th>
                            <th class="px-6 py-4">Developer</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Submitted At</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-400 divide-y divide-white/5">
                        <?php if (empty($projects)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-gray-600 italic">No submissions found for this status.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($projects as $p): ?>
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-cyber-black rounded border border-white/10 flex items-center justify-center p-1">
                                                <?php if ($p['logo_path']): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/logos/<?php echo $p['logo_path']; ?>" class="max-w-full max-h-full object-contain">
                                                <?php else: ?>
                                                    <span class="text-cyber-blue text-xs font-bold"><?php echo substr($p['title'], 0, 1); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="font-medium text-white"><?php echo htmlspecialchars($p['title']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($p['username']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($p['category_name']); ?></td>
                                    <td class="px-6 py-4 text-xs"><?php echo date('M d, Y H:i', strtotime($p['created_at'])); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="submission.php?id=<?php echo $p['id']; ?>" class="bg-white/5 hover:bg-cyber-blue hover:text-black border border-white/10 px-4 py-2 rounded-lg text-xs font-bold transition">Review</a>
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
