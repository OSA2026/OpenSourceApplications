<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

require_admin();

// Handle Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $type = $_POST['type'] ?? '';
        $action = $_POST['action'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if ($type === 'category') {
            if ($action === 'add' && $name) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                $stmt->execute([$name, $slug]);
                set_flash_message('success', 'Category added.');
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? 0;
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', 'Category deleted.');
            }
        } elseif ($type === 'platform') {
            if ($action === 'add' && $name) {
                $stmt = $pdo->prepare("INSERT INTO platforms (name) VALUES (?)");
                $stmt->execute([$name]);
                set_flash_message('success', 'Platform added.');
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? 0;
                $stmt = $pdo->prepare("DELETE FROM platforms WHERE id = ?");
                $stmt->execute([$id]);
                set_flash_message('success', 'Platform deleted.');
            }
        }
        header('Location: taxonomies.php');
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$platforms = $pdo->query("SELECT * FROM platforms ORDER BY name ASC")->fetchAll();

$page_title = "Taxonomy Management";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-2">Taxonomies</h1>
            <p class="text-gray-500">Manage categories and platforms tags.</p>
        </div>

        <?php display_flash_message(); ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Categories -->
            <section class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Categories</h2>
                </div>
                
                <form method="POST" class="flex space-x-2">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="type" value="category">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="name" placeholder="New category name" required class="flex-grow bg-cyber-black border border-white/10 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-cyber-blue">
                    <button type="submit" class="bg-cyber-blue text-black font-bold px-4 py-2 rounded-lg text-sm hover:bg-cyber-cyan transition">Add</button>
                </form>

                <div class="glass-card rounded-2xl border border-white/5 overflow-hidden">
                    <table class="w-full text-left">
                        <tbody class="text-sm text-gray-400 divide-y divide-white/5">
                            <?php foreach ($categories as $cat): ?>
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-4 text-white font-medium"><?php echo htmlspecialchars($cat['name']); ?></td>
                                    <td class="px-6 py-4 text-xs italic text-gray-600"><?php echo $cat['slug']; ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" onsubmit="return confirm('Delete category? Ensure no projects are using it.')" class="inline">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="type" value="category">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-400 p-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Platforms -->
            <section class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Platforms</h2>
                </div>

                <form method="POST" class="flex space-x-2">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="type" value="platform">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="name" placeholder="New platform (e.g. Ubuntu Touch)" required class="flex-grow bg-cyber-black border border-white/10 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-cyber-purple">
                    <button type="submit" class="bg-cyber-purple text-white font-bold px-4 py-2 rounded-lg text-sm hover:bg-cyber-purple/80 transition">Add</button>
                </form>

                <div class="glass-card rounded-2xl border border-white/5 overflow-hidden">
                    <table class="w-full text-left">
                        <tbody class="text-sm text-gray-400 divide-y divide-white/5">
                            <?php foreach ($platforms as $p): ?>
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-4 text-white font-medium"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" onsubmit="return confirm('Delete platform?')" class="inline">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="type" value="platform">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-400 p-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
