<?php
$page_title = "Explore Projects";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Filtering and Sorting
$platform_id = $_GET['platform'] ?? '';
$category_id = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

$query = "SELECT p.*, u.username as maintainer, c.name as category_name 
          FROM projects p 
          JOIN users u ON p.developer_id = u.id 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.status = 'approved'";

$params = [];

if ($platform_id) {
    $query .= " AND p.id IN (SELECT project_id FROM project_platforms WHERE platform_id = ?)";
    $params[] = $platform_id;
}

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $query .= " AND (p.title LIKE ? OR p.short_description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($sort) {
    case 'trending':
        $query .= " ORDER BY p.updated_at DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Fetch taxonomies for filters
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$platforms = $pdo->query("SELECT * FROM platforms")->fetchAll();

require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="mb-12">
    <h1 class="text-4xl font-bold text-white mb-4">Project Directory</h1>
    <p class="text-gray-400">High-quality, reviewed open-source applications.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Sidebar Filters -->
    <aside class="lg:col-span-1 space-y-8">
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-white font-bold mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-cyber-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </h3>
            
            <form action="projects.php" method="GET" class="space-y-6">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Project name..." class="w-full bg-cyber-black border border-white/10 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-cyber-blue">
                </div>

                <!-- Platform -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Platform</label>
                    <select name="platform" class="w-full bg-cyber-black border border-white/10 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-cyber-blue">
                        <option value="">All Platforms</option>
                        <?php foreach ($platforms as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $platform_id == $p['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Category</label>
                    <div class="space-y-2">
                        <label class="flex items-center text-sm text-gray-400 cursor-pointer hover:text-white">
                            <input type="radio" name="category" value="" <?php echo empty($category_id) ? 'checked' : ''; ?> class="mr-3">
                            All Categories
                        </label>
                        <?php foreach ($categories as $c): ?>
                            <label class="flex items-center text-sm text-gray-400 cursor-pointer hover:text-white">
                                <input type="radio" name="category" value="<?php echo $c['id']; ?>" <?php echo $category_id == $c['id'] ? 'selected' : ''; ?> class="mr-3">
                                <?php echo htmlspecialchars($c['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="w-full bg-white/5 hover:bg-white/10 text-white text-sm font-bold py-3 rounded-lg border border-white/10 transition">
                    Apply Filters
                </button>
            </form>
        </div>
    </aside>

    <!-- Project Grid -->
    <div class="lg:col-span-3">
        <!-- Sort & Stats -->
        <div class="flex justify-between items-center mb-8 bg-white/5 p-4 rounded-xl border border-white/5">
            <span class="text-sm text-gray-400"><?php echo count($projects); ?> projects found</span>
            <form action="projects.php" method="GET" class="flex items-center space-x-3">
                <input type="hidden" name="platform" value="<?php echo htmlspecialchars($platform_id); ?>">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_id); ?>">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <label class="text-sm text-gray-400">Sort by:</label>
                <select name="sort" onchange="this.form.submit()" class="bg-transparent border-none text-sm text-white font-bold focus:outline-none cursor-pointer">
                    <option value="latest" <?php echo $sort == 'latest' ? 'selected' : ''; ?>>Latest</option>
                    <option value="trending" <?php echo $sort == 'trending' ? 'selected' : ''; ?>>Trending</option>
                </select>
            </form>
        </div>

        <?php if (empty($projects)): ?>
            <div class="glass-card p-20 rounded-3xl text-center border border-dashed border-white/10">
                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No projects found</h3>
                <p class="text-gray-400">Try adjusting your filters or search keywords.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($projects as $project): ?>
                    <a href="project.php?id=<?php echo $project['id']; ?>" class="glass-card p-6 rounded-2xl border border-white/5 hover:border-cyber-blue/50 transition group flex flex-col h-full">
                        <div class="flex items-start justify-between mb-6">
                            <div class="w-12 h-12 rounded-xl bg-cyber-black border border-white/10 overflow-hidden flex items-center justify-center p-2">
                                <?php if ($project['logo_path']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/logos/<?php echo $project['logo_path']; ?>" alt="Logo" class="max-w-full max-h-full object-contain">
                                <?php else: ?>
                                    <span class="text-cyber-blue font-bold text-xl"><?php echo substr($project['title'], 0, 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($project['is_verified']): ?>
                                <span class="bg-cyber-blue/10 text-cyber-blue text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-widest border border-cyber-blue/20">Verified</span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyber-blue transition"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="text-sm text-gray-400 mb-6 flex-grow line-clamp-2"><?php echo htmlspecialchars($project['short_description']); ?></p>
                        
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="text-[10px] bg-white/5 text-gray-400 px-2 py-1 rounded border border-white/5"><?php echo htmlspecialchars($project['license_type']); ?></span>
                            <span class="text-[10px] bg-white/5 text-gray-400 px-2 py-1 rounded border border-white/5"><?php echo htmlspecialchars($project['category_name']); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between pt-6 border-t border-white/5">
                            <div class="text-xs text-gray-500">
                                by <span class="text-gray-300 font-medium"><?php echo htmlspecialchars($project['maintainer']); ?></span>
                            </div>
                            <div class="text-[10px] text-gray-600 uppercase tracking-widest">
                                Updated <?php echo date('M Y', strtotime($project['updated_at'])); ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
