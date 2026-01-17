<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Message deleted.');
        header('Location: contacts.php');
        exit;
    }
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

$page_title = "Inbound Messages";
require_once __DIR__ . '/../partials/head.php';
?>

<div class="flex min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar_admin.php'; ?>

    <main class="flex-grow p-8 md:p-12 overflow-x-hidden">
        <div class="mb-12">
            <h1 class="text-3xl font-bold text-white mb-2">Contact Messages</h1>
            <p class="text-gray-500">View and moderate inquiries from the community.</p>
        </div>

        <?php display_flash_message(); ?>

        <div class="space-y-6">
            <?php if (empty($messages)): ?>
                <div class="glass-card p-20 rounded-3xl text-center border border-dashed border-white/10">
                    <p class="text-gray-500">No messages found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="glass-card p-8 rounded-2xl border border-white/5 relative group">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white"><?php echo htmlspecialchars($msg['subject'] ?: 'No Subject'); ?></h3>
                                <div class="text-xs text-cyber-blue font-mono mt-1">
                                    <?php echo htmlspecialchars($msg['name']); ?> &lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                                <form method="POST" onsubmit="return confirm('Delete this message?')" class="opacity-0 group-hover:opacity-100 transition">
                                    <?php csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="bg-black/30 p-6 rounded-xl border border-white/5 text-sm text-gray-400 leading-relaxed whitespace-pre-wrap">
                            <?php echo htmlspecialchars($msg['message']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
