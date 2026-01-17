<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$page_title = "Submit Your Project";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/flash.php';

require_login();

if (is_user_suspended($pdo)) {
    $page_title = "Account Suspended";
    require_once __DIR__ . '/../partials/head.php';
    require_once __DIR__ . '/../partials/header.php';
    ?>
    <div class="max-w-2xl mx-auto py-20 text-center">
        <div class="glass-card p-12 rounded-3xl border border-red-500/20 bg-red-900/10">
            <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-6">OSA Suspended You</h1>
            <p class="text-gray-400 mb-8 leading-relaxed">
                If you believe this is a mistake, please contact us via mail or 
                <a href="contact.php" class="text-cyber-blue hover:underline">create a contact ticket</a> 
                to report the issue and request account reactivation.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="index.php" class="px-8 py-3 bg-white/5 border border-white/10 rounded-xl text-white font-bold hover:bg-white/10 transition">Return Home</a>
            </div>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

// Fetch taxonomies
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$platforms = $pdo->query("SELECT * FROM platforms")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', get_csrf_error_message());
    } else {
        $title = trim($_POST['title'] ?? '');
        $short_desc = trim($_POST['short_description'] ?? '');
        $full_desc = trim($_POST['full_description'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $license = trim($_POST['license'] ?? '');
        $git_url = trim($_POST['git_url'] ?? '');
        $demo_url = trim($_POST['demo_url'] ?? '');
        $docs_url = trim($_POST['docs_url'] ?? '');
        $release_status = $_POST['release_status'] ?? 'stable';
        $p_platforms = $_POST['platforms'] ?? [];

        if (validate_required($title) && validate_required($short_desc) && validate_required($category_id) && validate_url($git_url)) {
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            
            $pdo->beginTransaction();
            try {
                // Handle Logo Upload
                $logo_path = null;
                if (!empty($_FILES['logo']['name'])) {
                    $upload = upload_file($_FILES['logo'], __DIR__ . '/../uploads/logos', ['jpg', 'jpeg', 'png']);
                    if ($upload['success']) $logo_path = $upload['path'];
                    else throw new Exception($upload['error']);
                }

                // Handle Release File Upload
                $release_path = null;
                if (!empty($_FILES['release_file']['name'])) {
                    $upload = upload_file($_FILES['release_file'], __DIR__ . '/../uploads/releases');
                    if ($upload['success']) $release_path = $upload['path'];
                    else throw new Exception($upload['error']);
                }

                $stmt = $pdo->prepare("INSERT INTO projects (developer_id, category_id, title, slug, short_description, full_description, license_type, git_url, demo_url, docs_url, release_status, logo_path, release_file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'], $category_id, $title, $slug, $short_desc, $full_desc, $license, $git_url, $demo_url, $docs_url, $release_status, $logo_path, $release_path
                ]);
                $project_id = $pdo->lastInsertId();

                // Insert Platforms
                $stmt = $pdo->prepare("INSERT INTO project_platforms (project_id, platform_id) VALUES (?, ?)");
                foreach ($p_platforms as $plat_id) {
                    $stmt->execute([$project_id, $plat_id]);
                }

                // Handle Multiple Screenshots
                if (!empty($_FILES['screenshots']['name'][0])) {
                    $stmt = $pdo->prepare("INSERT INTO project_screenshots (project_id, image_path) VALUES (?, ?)");
                    foreach ($_FILES['screenshots']['name'] as $i => $name) {
                        $file_array = [
                            'name' => $_FILES['screenshots']['name'][$i],
                            'type' => $_FILES['screenshots']['type'][$i],
                            'tmp_name' => $_FILES['screenshots']['tmp_name'][$i],
                            'error' => $_FILES['screenshots']['error'][$i],
                            'size' => $_FILES['screenshots']['size'][$i]
                        ];
                        $upload = upload_file($file_array, __DIR__ . '/../uploads/screenshots', ['jpg', 'jpeg', 'png']);
                        if ($upload['success']) $stmt->execute([$project_id, $upload['path']]);
                    }
                }

                $pdo->commit();
                set_flash_message('success', 'Project submitted! It will appear in the directory once approved by an admin.');
                header('Location: dashboard.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                set_flash_message('error', 'Error: ' . $e->getMessage());
            }
        } else {
            set_flash_message('error', 'Please fill in all required fields correctly.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">Submit Your Project</h1>
        <p class="text-gray-400">Share your open-source work with the studio community.</p>
    </div>

    <form action="submit.php" method="POST" enctype="multipart/form-data" class="space-y-8">
        <?php csrf_field(); ?>

        <!-- Basic Info -->
        <section class="glass-card p-8 rounded-3xl border border-white/10">
            <h3 class="text-white font-bold mb-8 text-lg flex items-center">
                <span class="w-8 h-8 rounded-full bg-cyber-blue/20 text-cyber-blue flex items-center justify-center mr-3 text-sm">1</span>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Project Title *</label>
                    <input type="text" name="title" required placeholder="e.g. OpenTerminal X" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Short Description * (Max 150 chars)</label>
                    <input type="text" name="short_description" required maxlength="150" placeholder="A brief one-liner for the project card." class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Category *</label>
                    <select name="category_id" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">License Type *</label>
                    <input type="text" name="license" required placeholder="e.g. MIT, GPL-3.0" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                </div>
            </div>
        </section>

        <!-- Technical Details -->
        <section class="glass-card p-8 rounded-3xl border border-white/10">
            <h3 class="text-white font-bold mb-8 text-lg flex items-center">
                <span class="w-8 h-8 rounded-full bg-cyber-purple/20 text-cyber-purple flex items-center justify-center mr-3 text-sm">2</span>
                Technical Details
            </h3>
            
            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-4">Supported Platforms *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($platforms as $p): ?>
                            <label class="flex items-center space-x-3 text-sm text-gray-300 cursor-pointer group">
                                <input type="checkbox" name="platforms[]" value="<?php echo $p['id']; ?>" class="w-5 h-5 rounded border-white/10 bg-cyber-black checked:bg-cyber-blue focus:ring-cyber-blue transition">
                                <span class="group-hover:text-white"><?php echo htmlspecialchars($p['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Git Repository URL *</label>
                        <input type="url" name="git_url" required placeholder="https://github.com/user/repo" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Demo URL (Optional)</label>
                        <input type="url" name="demo_url" placeholder="https://demo.example.com" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Documentation URL (Optional)</label>
                        <input type="url" name="docs_url" placeholder="https://docs.example.com" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Full Description * (Markdown supported)</label>
                    <textarea name="full_description" rows="8" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition" placeholder="Explain features, usage, and why it belongs in the studio."></textarea>
                </div>
            </div>
        </section>

        <!-- Media & Files -->
        <section class="glass-card p-8 rounded-3xl border border-white/10">
            <h3 class="text-white font-bold mb-8 text-lg flex items-center">
                <span class="w-8 h-8 rounded-full bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center mr-3 text-sm">3</span>
                Media & Assets
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Project Logo (Recommended)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/5 border-dashed rounded-xl hover:border-cyber-blue transition cursor-pointer relative">
                        <div class="space-y-1 text-center font-bold">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <label class="relative cursor-pointer text-cyber-blue">
                                <span>Upload logo</span>
                                <input type="file" name="logo" class="sr-only" accept="image/*" data-preview="logo-preview">
                            </label>
                            <p class="text-xs text-gray-500">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                    <div id="logo-preview" class="mt-4 hidden p-2 border border-white/5 rounded-xl bg-black/20"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Latest Release (Optional)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/5 border-dashed rounded-xl hover:border-cyber-purple transition cursor-pointer relative">
                        <div class="space-y-1 text-center font-bold">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <label class="relative cursor-pointer text-cyber-purple">
                                <span>Upload .zip, .deb, etc.</span>
                                <input type="file" name="release_file" class="sr-only">
                            </label>
                            <p class="text-xs text-gray-500">Up to 2GB</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Project Screenshots (Min 1, Max 5)</label>
                    <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-white/5 border-dashed rounded-xl hover:border-cyber-cyan transition cursor-pointer relative">
                        <div class="space-y-1 text-center font-bold">
                            <label class="relative cursor-pointer text-cyber-cyan">
                                <span>Select multiple images</span>
                                <input type="file" name="screenshots[]" multiple class="sr-only" accept="image/*" data-preview="screenshots-preview">
                            </label>
                            <p class="text-xs text-gray-500">PNG, JPG recommended</p>
                        </div>
                    </div>
                    <div id="screenshots-preview" class="mt-4 hidden grid grid-cols-2 md:grid-cols-5 gap-4 p-4 border border-white/5 rounded-xl bg-black/20"></div>
                </div>
            </div>
        </section>

        <!-- Agreement -->
        <div class="flex items-start space-x-3 p-4">
            <input type="checkbox" required class="mt-1 w-5 h-5 rounded border-white/10 bg-cyber-black checked:bg-cyber-blue focus:ring-cyber-blue">
            <p class="text-sm text-gray-400">
                I confirm this is open-source or will be open-sourced under the selected license. I am the maintainer or have permission to list this project in the Open Source Applications studio.
            </p>
        </div>

        <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-5 rounded-2xl hover:bg-cyber-cyan transition shadow-[0_0_20px_rgba(0,210,255,0.4)] text-lg">
            Submit Project for Review
        </button>
    </form>
</div>

<script>
function handleFileSelect(input, previewId, showName = true) {
    const preview = document.getElementById(previewId);
    const fileNameDisplay = input.parentElement.querySelector('.file-name');
    
    if (input.files && input.files[0]) {
        if (showName && fileNameDisplay) {
            fileNameDisplay.textContent = input.files[0].name;
            fileNameDisplay.classList.remove('hidden');
        }

        if (preview && input.files[0].type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="max-h-32 rounded-lg mx-auto shadow-lg">`;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
}

function handleMultipleSelect(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    preview.classList.remove('hidden');
    
    if (input.files) {
        Array.from(input.files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full aspect-video object-cover rounded-lg border border-white/10">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2">
                            <span class="text-[10px] text-white font-mono break-all">${file.name}</span>
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                const div = document.createElement('div');
                div.className = 'p-3 bg-white/5 border border-white/10 rounded-lg text-xs text-gray-400 truncate';
                div.textContent = file.name;
                preview.appendChild(div);
            }
        });
    }
}

// Initial setup for existing inputs if needed
document.addEventListener('DOMContentLoaded', () => {
    // Add file name placeholders if they don't exist
    document.querySelectorAll('input[type="file"]').forEach(input => {
        const span = document.createElement('span');
        span.className = 'file-name hidden block mt-2 text-xs text-cyber-blue font-mono';
        input.parentElement.appendChild(span);
        
        input.addEventListener('change', function() {
            if (this.multiple) {
                handleMultipleSelect(this, this.dataset.preview);
            } else {
                handleFileSelect(this, this.dataset.preview);
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
