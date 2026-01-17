<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, u.username as maintainer, c.name as category_name 
                       FROM projects p 
                       JOIN users u ON p.developer_id = u.id 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ? AND p.status = 'approved'");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: projects.php');
    exit;
}

// Fetch platforms
$platforms_stmt = $pdo->prepare("SELECT pl.name FROM platforms pl 
                                 JOIN project_platforms pp ON pl.id = pp.platform_id 
                                 WHERE pp.project_id = ?");
$platforms_stmt->execute([$id]);
$platforms = $platforms_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch screenshots
$screenshots_stmt = $pdo->prepare("SELECT * FROM project_screenshots WHERE project_id = ?");
$screenshots_stmt->execute([$id]);
$screenshots = $screenshots_stmt->fetchAll();

$page_title = $project['title'];
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="mb-12">
    <a href="projects.php" class="text-sm text-gray-400 hover:text-cyber-blue transition flex items-center mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Directory
    </a>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="flex items-center space-x-6">
            <div class="w-20 h-20 rounded-2xl bg-cyber-black border border-white/10 flex items-center justify-center p-4">
                <?php if ($project['logo_path']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/logos/<?php echo $project['logo_path']; ?>" alt="Logo" class="max-w-full max-h-full object-contain">
                <?php else: ?>
                    <span class="text-cyber-blue font-bold text-3xl"><?php echo substr($project['title'], 0, 1); ?></span>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="text-4xl font-bold text-white mb-2"><?php echo htmlspecialchars($project['title']); ?></h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-cyber-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <?php echo htmlspecialchars($project['maintainer']); ?>
                    </span>
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-cyber-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <?php echo htmlspecialchars($project['category_name']); ?>
                    </span>
                    <span class="bg-white/5 border border-white/10 px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-widest"><?php echo htmlspecialchars($project['license_type']); ?></span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <?php if ($project['demo_url']): ?>
                <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" target="_blank" class="glass-card px-6 py-3 rounded-xl border border-white/10 hover:border-white/30 transition text-sm font-bold">Review Demo</a>
            <?php endif; ?>
            <a href="<?php echo htmlspecialchars($project['git_url']); ?>" target="_blank" class="bg-cyber-blue text-black px-6 py-3 rounded-xl hover:bg-cyber-cyan transition font-bold text-sm shadow-[0_0_15px_rgba(0,210,255,0.4)] flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
                Source Code
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-12">
        <!-- Overview -->
        <section class="glass-card p-8 md:p-12 rounded-3xl border border-white/10">
            <h2 class="text-2xl font-bold text-white mb-6">Overview</h2>
            <div class="prose prose-invert max-w-none text-gray-300 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($project['full_description'])); ?>
            </div>
        </section>

        <!-- Screenshots -->
        <?php if (!empty($screenshots)): ?>
        <section>
            <h2 class="text-2xl font-bold text-white mb-6">Screenshots</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($screenshots as $s): ?>
                    <div class="glass-card p-2 rounded-2xl border border-white/5 overflow-hidden group">
                        <img src="<?php echo BASE_URL; ?>/uploads/screenshots/<?php echo $s['image_path']; ?>" alt="Screenshot" class="rounded-xl w-full aspect-video object-cover transition duration-500 group-hover:scale-105">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Security Section -->
        <section class="glass-card p-8 rounded-3xl border border-red-500/20 bg-red-500/5">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Security Status
                </h2>
                <button class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-2 rounded-lg text-xs font-bold transition">Report Vulnerability</button>
            </div>
            <p class="text-sm text-gray-400">
                No active CVEs reported for this project version. Our studio monitors all approved projects for critical security issues. 
            </p>
        </section>
    </div>

    <!-- Sidebar Info -->
    <aside class="space-y-8">
        <div class="glass-card p-8 rounded-3xl border border-white/5">
            <h3 class="text-white font-bold mb-6">Download & Install</h3>
            <div class="space-y-4">
                <?php if ($project['release_file_path']): ?>
                    <a href="<?php echo BASE_URL; ?>/uploads/releases/<?php echo $project['release_file_path']; ?>" class="w-full bg-cyber-purple text-white font-bold py-4 rounded-xl hover:bg-cyber-purple/80 transition flex items-center justify-center shadow-[0_0_15px_rgba(112,0,255,0.3)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Latest Release
                    </a>
                <?php else: ?>
                    <p class="text-sm text-gray-500 text-center italic">No binaries uploaded yet. Check git repository for build instructions.</p>
                <?php endif; ?>
                
                <?php if ($project['docs_url']): ?>
                    <a href="<?php echo htmlspecialchars($project['docs_url']); ?>" target="_blank" class="w-full bg-white/5 text-gray-300 font-bold py-4 rounded-xl hover:bg-white/10 border border-white/10 transition flex items-center justify-center">
                        View Documentation
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-card p-8 rounded-3xl border border-white/5">
            <h3 class="text-white font-bold mb-6">Compatibility</h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($platforms as $p): ?>
                    <span class="text-xs bg-white/5 text-gray-400 px-3 py-2 rounded-lg border border-white/5"><?php echo htmlspecialchars($p); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="glass-card p-8 rounded-3xl border border-white/5">
            <h3 class="text-white font-bold mb-6">Project Info</h3>
            <dl class="space-y-4">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">License</dt>
                    <dd class="text-white font-mono"><?php echo htmlspecialchars($project['license_type']); ?></dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Status</dt>
                    <dd class="text-green-400"><?php echo ucfirst($project['release_status']); ?></dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-500">Last Updated</dt>
                    <dd class="text-white"><?php echo date('M d, Y', strtotime($project['updated_at'])); ?></dd>
                </div>
            </dl>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
