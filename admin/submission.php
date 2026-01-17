<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

require_admin();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, u.username, u.email as user_email, c.name as category_name 
                       FROM projects p 
                       JOIN users u ON p.developer_id = u.id 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: submissions.php');
    exit;
}

// Handle Moderation Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $action = $_POST['action'] ?? '';
        $feedback = trim($_POST['feedback'] ?? '');
        $status = 'pending';

        if ($action === 'approve') $status = 'approved';
        if ($action === 'reject') $status = 'rejected';
        if ($action === 'needs_fix') $status = 'needs_fix';

        $stmt = $pdo->prepare("UPDATE projects SET status = ?, admin_feedback = ? WHERE id = ?");
        if ($stmt->execute([$status, $feedback, $id])) {
            
            // Log the action
            $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, project_id) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], "Marked project as " . $status, $id]);

            set_flash_message('success', 'Project ' . $status . ' successfully.');
            header('Location: submissions.php');
            exit;
        }
    }
}

// Fetch screenshots
$screenshots_stmt = $pdo->prepare("SELECT * FROM project_screenshots WHERE project_id = ?");
$screenshots_stmt->execute([$id]);
$screenshots = $screenshots_stmt->fetchAll();

$page_title = "Review: " . $project['title'];
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <a href="submissions.php" class="text-xs text-gray-500 hover:text-white transition flex items-center mb-6 uppercase tracking-widest">
                ← Back to List
            </a>
            <h1 class="text-3xl font-bold text-white mb-2">Review Submission</h1>
            <p class="text-gray-500 italic"><?php echo htmlspecialchars($project['title']); ?></p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-12">
            <!-- Project Details -->
            <div class="xl:col-span-2 space-y-8">
                <section class="glass-card p-8 rounded-2xl border border-white/5">
                    <h3 class="text-white font-bold mb-6">Description</h3>
                    <div class="prose prose-invert max-w-none text-sm text-gray-400 leading-relaxed">
                        <p class="mb-4"><strong>Short:</strong> <?php echo htmlspecialchars($project['short_description']); ?></p>
                        <hr class="border-white/5 my-6">
                        <div class="whitespace-pre-wrap"><?php echo htmlspecialchars($project['full_description']); ?></div>
                    </div>
                </section>

                <section class="glass-card p-8 rounded-2xl border border-white/5">
                    <h3 class="text-white font-bold mb-6">Technical Metadata</h3>
                    <div class="grid grid-cols-2 gap-8 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Git Repository</p>
                            <a href="<?php echo $project['git_url']; ?>" target="_blank" class="text-cyber-blue hover:underline break-all"><?php echo htmlspecialchars($project['git_url']); ?></a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">License</p>
                            <span class="text-white font-mono"><?php echo htmlspecialchars($project['license_type']); ?></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Release Level</p>
                            <span class="text-white"><?php echo ucfirst($project['release_status']); ?></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Developer</p>
                            <span class="text-white"><?php echo htmlspecialchars($project['username']); ?> (<?php echo htmlspecialchars($project['user_email']); ?>)</span>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-white font-bold mb-6">Gallery</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <?php foreach ($screenshots as $s): ?>
                            <a href="<?php echo BASE_URL; ?>/uploads/screenshots/<?php echo $s['image_path']; ?>" target="_blank" class="block glass-card p-1 rounded-lg border border-white/5 overflow-hidden">
                                <img src="<?php echo BASE_URL; ?>/uploads/screenshots/<?php echo $s['image_path']; ?>" class="w-full aspect-video object-cover rounded">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Review Actions -->
            <aside>
                <div class="glass-card p-8 rounded-2xl border border-white/5 sticky top-8">
                    <h3 class="text-white font-bold mb-8">Moderation Actions</h3>
                    
                    <form action="submission.php?id=<?php echo $id; ?>" method="POST" class="space-y-6">
                        <?php csrf_field(); ?>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Verdict</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 bg-green-500/5 border border-green-500/20 rounded-xl cursor-pointer hover:bg-green-500/10 transition group">
                                    <input type="radio" name="action" value="approve" required class="w-5 h-5 text-green-500 bg-black border-white/10 focus:ring-green-500">
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-green-400">Approve & Publish</p>
                                        <p class="text-[10px] text-gray-500">Visible in public directory.</p>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl cursor-pointer hover:bg-yellow-500/10 transition group">
                                    <input type="radio" name="action" value="needs_fix" class="w-5 h-5 text-yellow-500 bg-black border-white/10 focus:ring-yellow-500">
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-yellow-400">Needs Fix</p>
                                        <p class="text-[10px] text-gray-500">Enable developer editing.</p>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 bg-red-500/5 border border-red-500/20 rounded-xl cursor-pointer hover:bg-red-500/10 transition group">
                                    <input type="radio" name="action" value="reject" class="w-5 h-5 text-red-500 bg-black border-white/10 focus:ring-red-500">
                                    <div class="ml-4">
                                        <p class="text-sm font-bold text-red-400">Reject</p>
                                        <p class="text-[10px] text-gray-500">Permanent dismissal.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Admin Feedback (Sent to Dev)</label>
                            <textarea name="feedback" rows="4" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-400 focus:outline-none focus:border-cyber-blue" placeholder="Provide details on your decision..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-white text-black font-bold py-4 rounded-xl hover:bg-gray-200 transition">
                            Submit Verdict
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
