<?php
// partials/header.php
require_once __DIR__ . '/../includes/auth.php';
?>
<header class="sticky top-0 z-50 glass-card border-b border-white/10">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
        <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-cyber-blue rounded flex items-center justify-center">
                <span class="text-black font-bold text-xl">O</span>
            </div>
            <span class="text-xl font-bold tracking-tight text-white">OSA <span class="text-cyber-blue">Studio</span></span>
        </a>

        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
            <a href="<?php echo BASE_URL; ?>/public/index.php" class="hover:text-cyber-blue transition">Home</a>
            <a href="<?php echo BASE_URL; ?>/public/projects.php" class="hover:text-cyber-blue transition">Projects</a>
            <a href="<?php echo BASE_URL; ?>/public/services.php" class="hover:text-cyber-blue transition">Services</a>
            <a href="<?php echo BASE_URL; ?>/public/about.php" class="hover:text-cyber-blue transition">About</a>
            <a href="<?php echo BASE_URL; ?>/public/contact.php" class="hover:text-cyber-blue transition">Contact</a>
        </nav>

        <div class="flex items-center space-x-4">
            <?php 
            if (is_logged_in()): 
                // Fresh fetch for avatar
                require_once __DIR__ . '/../includes/db.php';
                $stmt = $pdo->prepare("SELECT dp.avatar_path, u.is_verified FROM users u LEFT JOIN developer_profiles dp ON u.id = dp.user_id WHERE u.id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user_nav_data = $stmt->fetch();
                
                $avatar_url = ($user_nav_data && $user_nav_data['avatar_path']) 
                    ? BASE_URL . '/uploads/avatars/' . $user_nav_data['avatar_path'] 
                    : null;
                $is_verified = $user_nav_data['is_verified'] ?? false;
            ?>
                <div class="flex items-center space-x-3 mr-4 border-r border-white/10 pr-4">
                    <a href="<?php echo BASE_URL; ?>/public/profile.php" class="flex items-center space-x-2 group">
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-white/20 group-hover:border-cyber-blue transition">
                            <?php if ($avatar_url): ?>
                                <img src="<?php echo $avatar_url; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-cyber-blue/10 flex items-center justify-center">
                                    <span class="text-cyber-blue text-xs font-bold"><?php echo substr($_SESSION['username'], 0, 1); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs text-white group-hover:text-cyber-blue transition"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <?php if ($is_verified): ?>
                                <svg class="w-3 h-3 ml-1 text-cyber-blue" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.24.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <a href="<?php echo BASE_URL; ?>/public/dashboard.php" class="text-sm hover:text-cyber-blue transition">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/public/submit.php" class="bg-cyber-blue text-black px-4 py-2 rounded-lg text-sm font-bold hover:bg-cyber-cyan transition shadow-[0_0_15px_rgba(0,210,255,0.4)]">Submit</a>
                <a href="<?php echo BASE_URL; ?>/public/logout.php" class="text-sm text-red-400 hover:text-red-300 transition pl-2">Logout</a>
<?php else: ?>
                <a href="<?php echo BASE_URL; ?>/public/login.php" class="text-sm hover:text-cyber-blue transition">Login</a>
                <a href="<?php echo BASE_URL; ?>/public/signup.php" class="bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2 rounded-lg text-sm font-bold transition">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="flex-grow container mx-auto px-6 py-12">
    <?php require_once __DIR__ . '/../includes/flash.php'; ?>
    <?php display_flash_message(); ?>
