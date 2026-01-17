<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/upload.php';

$username_query = $_GET['u'] ?? null;
$mode = $_GET['mode'] ?? 'view';
$is_own_profile = false;
$user_id = null;

if ($username_query) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username_query]);
    $u = $stmt->fetch();
    if (!$u) {
        die("User not found.");
    }
    $user_id = $u['id'];
    $is_own_profile = (is_logged_in() && $_SESSION['user_id'] == $user_id);
} else {
    require_login();
    if (is_user_suspended($pdo)) {
        header('Location: submit.php'); // Redirect to submit where the message is shown, or show it here too
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $is_own_profile = true;
    
    if (is_admin()) {
        $msg = "We deceted you are Admin So, Sorry, the previlleges cannot give you to modify contnet from site if you are Sish or Nishchal Trying then just update from DB.";
        die("<div style='background:#050505; color:#00d2ff; height:100vh; display:flex; align-items:center; justify-content:center; flex-direction:column; text-align:center; font-family:monospace; padding:20px;'>
                <h1 style='font-size:3rem; margin-bottom:20px; color: #fff; text-shadow: 0 0 20px rgba(0,210,255,0.5);'>ACCESS RESTRICTED</h1>
                <p style='font-size:1.2rem; max-width:800px; line-height:1.6; margin: 0 auto;'>$msg</p>
                <p style='margin-top:30px; color: #555; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px;'>NOTE: Normal user or Developers have access of this site</p>
                <a href='index.php' style='margin-top:40px; color:#00d2ff; text-decoration:none; border:1px solid #00d2ff; padding:12px 30px; border-radius:12px; font-weight:bold; transition: 0.3s; background: rgba(0,210,255,0.05);'>Return to Base</a>
             </div>");
    }
}

// Fetch Profile Data
$stmt = $pdo->prepare("SELECT u.username, u.email, u.is_verified, dp.* FROM users u JOIN developer_profiles dp ON u.id = dp.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

if (!$profile) {
    die("Profile not initialized.");
}

// Handle Update (Only if own profile and mode is edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_own_profile || $mode !== 'edit') {
        die("Unauthorized action.");
    }
    
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', get_csrf_error_message());
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $github_url = trim($_POST['github_url'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $skills = trim($_POST['skills'] ?? '');

        if ($github_url && !validate_url($github_url)) {
            set_flash_message('error', 'Invalid GitHub URL.');
        } else {
            $avatar_path = $profile['avatar_path'];
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = upload_file($_FILES['avatar'], __DIR__ . '/../uploads/avatars', ['jpg', 'jpeg', 'png']);
                if ($upload['success']) {
                    $avatar_path = $upload['path'];
                } else {
                    set_flash_message('error', $upload['error']);
                }
            }

            $stmt = $pdo->prepare("UPDATE developer_profiles SET full_name = ?, github_url = ?, bio = ?, skills = ?, avatar_path = ? WHERE user_id = ?");
            if ($stmt->execute([$full_name, $github_url, $bio, $skills, $avatar_path, $user_id])) {
                set_flash_message('success', 'Profile updated successfully.');
                header('Location: profile.php?u=' . urlencode($profile['username']));
                exit;
            }
        }
    }
}

$page_title = $is_own_profile ? "My Profile" : $profile['username'] . "'s Profile";
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center">
                <h1 class="text-4xl font-bold text-white"><?php echo htmlspecialchars($profile['username']); ?></h1>
                <?php if ($profile['is_verified']): ?>
                    <svg class="w-8 h-8 ml-3 text-cyber-blue flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?php endif; ?>
            </div>
            <p class="text-gray-400">Developer Profile</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <button onclick="copyProfileLink()" class="bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 rounded-xl text-sm font-bold text-white transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                Copy Link
            </button>
            <?php if ($is_own_profile): ?>
                <?php if ($mode === 'edit'): ?>
                    <a href="profile.php?u=<?php echo urlencode($profile['username']); ?>" class="text-sm text-gray-400 hover:text-white transition">Cancel</a>
                <?php else: ?>
                    <a href="profile.php?mode=edit" class="bg-cyber-blue text-black px-6 py-2 rounded-xl text-sm font-bold hover:bg-cyber-cyan transition">Edit Profile</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php display_flash_message(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Sidebar -->
        <aside class="lg:col-span-1 space-y-8">
            <div class="glass-card p-8 rounded-3xl border border-white/5 text-center">
                <div class="relative inline-block mb-6 group">
                    <div class="w-32 h-32 bg-cyber-blue/10 rounded-full flex items-center justify-center border border-cyber-blue/20 overflow-hidden">
                        <?php if ($profile['avatar_path']): ?>
                            <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $profile['avatar_path']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="text-cyber-blue text-5xl font-bold"><?php echo substr($profile['username'], 0, 1); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center justify-center mb-2">
                    <h3 class="text-white font-bold text-xl"><?php echo htmlspecialchars($profile['full_name'] ?: $profile['username']); ?></h3>
                    <?php if ($profile['is_verified']): ?>
                        <svg class="w-5 h-5 ml-2 text-cyber-blue flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 uppercase tracking-widest">Verified Developer</p>
                
                <?php if ($profile['github_url']): ?>
                    <a href="<?php echo htmlspecialchars($profile['github_url']); ?>" target="_blank" class="mt-6 inline-flex items-center text-sm text-cyber-blue hover:underline">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        GitHub Profile
                    </a>
                <?php endif; ?>
            </div>

            <div class="glass-card p-8 rounded-3xl border border-white/5">
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-widest">Skills</h4>
                <div class="flex flex-wrap gap-2">
                    <?php 
                    $skill_list = explode(',', $profile['skills']);
                    foreach ($skill_list as $s): 
                        if (trim($s)):
                    ?>
                        <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-lg text-xs text-gray-400"><?php echo htmlspecialchars(trim($s)); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <?php if ($mode === 'edit' && $is_own_profile): ?>
                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <?php csrf_field(); ?>
                    <div class="glass-card p-8 rounded-3xl border border-white/10 space-y-6">
                        <h3 class="text-white font-bold text-lg mb-6 border-b border-white/5 pb-4">Edit Profile Information</h3>
                        
                        <!-- Avatar Upload -->
                        <div class="flex flex-col items-center mb-8 bg-black/20 p-6 rounded-2xl border border-white/5">
                            <div class="relative group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                                <div id="avatar-preview" class="w-24 h-24 bg-cyber-blue/10 rounded-full flex items-center justify-center border-2 border-dashed border-cyber-blue/30 overflow-hidden hover:border-cyber-blue transition">
                                    <?php if ($profile['avatar_path']): ?>
                                        <img src="<?php echo BASE_URL; ?>/uploads/avatars/<?php echo $profile['avatar_path']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-cyber-blue text-2xl font-bold"><?php echo substr($profile['username'], 0, 1); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 rounded-full transition">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                            </div>
                            <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                            <p class="text-xs text-gray-500 mt-4">Click to change profile picture (Max 5MB)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Display Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">GitHub Profile URL</label>
                            <input type="url" name="github_url" value="<?php echo htmlspecialchars($profile['github_url'] ?? ''); ?>" placeholder="https://github.com/username" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Technical Bio</label>
                            <textarea name="bio" rows="6" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Skills (Comma separated)</label>
                            <input type="text" name="skills" value="<?php echo htmlspecialchars($profile['skills'] ?? ''); ?>" placeholder="C++, Rust, Linux Kernel, GTK" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
                        </div>
                        
                        <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="space-y-8">
                    <section class="glass-card p-8 rounded-3xl border border-white/5 text-left">
                        <h3 class="text-white font-bold text-lg mb-4 text-left">About the Developer</h3>
                        <div class="text-gray-400 leading-relaxed text-left">
                            <?php echo $profile['bio'] ? htmlspecialchars(trim($profile['bio'])) : '<span class="italic text-gray-600">No bio provided.</span>'; ?>
                        </div>
                    </section>

                    <?php
                    // Fetch Recent Projects by this developer
                    $stmt = $pdo->prepare("SELECT * FROM projects WHERE developer_id = ? AND status = 'approved' ORDER BY created_at DESC LIMIT 6");
                    $stmt->execute([$user_id]);
                    $projects = $stmt->fetchAll();
                    ?>
                    
                    <section>
                        <h3 class="text-white font-bold text-lg mb-6">Published Projects</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php if (empty($projects)): ?>
                                <p class="text-gray-600 italic">No approved projects yet.</p>
                            <?php else: ?>
                                <?php foreach ($projects as $p): ?>
                                    <a href="project.php?id=<?php echo $p['id']; ?>" class="glass-card p-6 rounded-2xl border border-white/5 hover:border-cyber-blue/50 transition group">
                                        <h4 class="text-white font-bold group-hover:text-cyber-blue transition"><?php echo htmlspecialchars($p['title']); ?></h4>
                                        <p class="text-xs text-gray-500 mt-2 line-clamp-2"><?php echo htmlspecialchars($p['short_description']); ?></p>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyProfileLink() {
    const url = window.location.origin + window.location.pathname + '?u=<?php echo urlencode($profile['username']); ?>';
    navigator.clipboard.writeText(url).then(() => {
        alert('Profile link copied to clipboard!');
    });
}

function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
