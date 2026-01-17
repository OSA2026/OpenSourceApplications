<?php
$page_title = "Developer Dashboard";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/flash.php';

require_login();
if (is_user_suspended($pdo)) {
    header('Location: submit.php');
    exit;
}

// Fetch developer's profile (for avatar, verification, and name)
$stmt = $pdo->prepare("SELECT dp.avatar_path, dp.full_name, u.is_verified FROM users u LEFT JOIN developer_profiles dp ON u.id = dp.user_id WHERE u.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$dev_profile = $stmt->fetch();

// Fetch developer's projects
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM projects p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.developer_id = ? 
                       ORDER BY p.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();

require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
    <div>
        <h1 class="text-4xl font-bold text-white mb-2">Developer Dashboard</h1>
        <p class="text-gray-400">Manage your submissions and track review status.</p>
    </div>
    <a href="submit.php" class="bg-cyber-blue text-black font-bold px-8 py-4 rounded-xl hover:bg-cyber-cyan transition shadow-[0_0_15px_rgba(0,210,255,0.4)] flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Submission
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Profile Stats -->
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-card p-6 rounded-2xl border border-white/5 text-center">
            <div class="w-20 h-20 bg-cyber-blue/10 rounded-full mx-auto mb-4 flex items-center justify-center border border-cyber-blue/20 overflow-hidden">
                <?php if ($dev_profile && $dev_profile['avatar_path']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $dev_profile['avatar_path']; ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-cyber-blue text-3xl font-bold"><?php echo substr($_SESSION['username'], 0, 1); ?></span>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-center mb-1">
                <h3 class="text-white font-bold text-lg truncate"><?php echo htmlspecialchars($dev_profile['full_name'] ?: $_SESSION['username']); ?></h3>
                <?php if ($dev_profile && $dev_profile['is_verified']): ?>
                    <svg class="w-4 h-4 ml-1.5 text-cyber-blue flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?php endif; ?>
            </div>
            <?php if ($dev_profile['full_name']): ?>
                <p class="text-[10px] text-gray-500 font-mono mb-4 uppercase tracking-tighter">@<?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <?php endif; ?>
            <p class="text-xs text-cyber-blue font-bold uppercase tracking-widest mb-6 border-b border-white/5 pb-4">Verified Developer</p>
            
            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/5">
                <div>
                    <span class="block text-2xl font-bold text-white"><?php echo count($projects); ?></span>
                    <span class="text-[10px] text-gray-500 uppercase">Projects</span>
                </div>
                <div>
                    <?php
                        $approved_count = count(array_filter($projects, fn($p) => $p['status'] === 'approved'));
                    ?>
                    <span class="block text-2xl font-bold text-cyber-blue"><?php echo $approved_count; ?></span>
                    <span class="text-[10px] text-gray-500 uppercase">Live</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    <div class="lg:col-span-3">
        <?php if (empty($projects)): ?>
            <div class="glass-card p-20 rounded-3xl text-center border border-dashed border-white/10">
                <p class="text-gray-400 mb-8">You haven't submitted any projects yet.</p>
                <a href="submit.php" class="text-cyber-blue hover:underline">Start your first submission →</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($projects as $p): ?>
                    <div class="glass-card p-6 rounded-2xl border border-white/5 flex flex-col md:flex-row md:items-center justify-between group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-cyber-black rounded-lg border border-white/10 flex items-center justify-center p-2">
                                <?php if ($p['logo_path']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/logos/<?php echo $p['logo_path']; ?>" class="max-w-full max-h-full object-contain">
                                <?php else: ?>
                                    <span class="text-cyber-blue font-bold"><?php echo substr($p['title'], 0, 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="text-white font-bold group-hover:text-cyber-blue transition"><?php echo htmlspecialchars($p['title']); ?></h3>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($p['category_name']); ?> • <?php echo date('M d, Y', strtotime($p['created_at'])); ?></p>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0 flex items-center space-x-6">
                            <!-- Status Badge -->
                            <?php
                                $status_class = 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
                                if ($p['status'] === 'approved') $status_class = 'bg-green-500/10 text-green-400 border-green-500/20';
                                if ($p['status'] === 'rejected') $status_class = 'bg-red-500/10 text-red-400 border-red-500/20';
                                if ($p['status'] === 'needs_fix') $status_class = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                            ?>
                            <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border <?php echo $status_class; ?>">
                                <?php echo str_replace('_', ' ', $p['status']); ?>
                            </span>

                            <div class="flex items-center space-x-2">
                                <?php if ($p['status'] === 'approved'): ?>
                                    <a href="project.php?id=<?php echo $p['id']; ?>" class="p-2 text-gray-500 hover:text-white transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ($p['status'] === 'pending' || $p['status'] === 'needs_fix'): ?>
                                    <a href="#" class="p-2 text-gray-500 hover:text-cyber-blue transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($p['admin_feedback']): ?>
                        <div class="ml-4 mt-2 p-4 bg-cyber-blue/5 border border-cyber-blue/10 rounded-xl text-xs text-gray-400">
                            <strong class="text-cyber-blue">Admin Feedback:</strong> <?php echo htmlspecialchars($p['admin_feedback']); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
