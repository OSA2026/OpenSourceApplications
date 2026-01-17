<?php
$page_title = "What We Build";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<section class="mb-20">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-cyber-blue font-mono tracking-widest uppercase text-sm mb-4"># CAPABILITIES</h2>
        <h1 class="text-5xl font-bold text-white mb-6">What We Build</h1>
        <p class="text-xl text-gray-400">
            Specializing in diverse software domains with a unified focus on open technology.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Service 1 -->
        <div class="glass-card p-12 rounded-3xl border border-white/5 flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-cyber-blue/10 rounded-2xl flex items-center justify-center mb-8 border border-cyber-blue/30 text-cyber-blue group-hover:scale-110 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Mobile App Development</h3>
            <p class="text-gray-400 leading-relaxed">
                Native applications for Linux-based mobile systems like Ubuntu Touch, focused on performance and privacy.
            </p>
        </div>

        <!-- Service 2 -->
        <div class="glass-card p-12 rounded-3xl border border-white/5 flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-cyber-purple/10 rounded-2xl flex items-center justify-center mb-8 border border-cyber-purple/30 text-cyber-purple group-hover:scale-110 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Desktop Applications</h3>
            <p class="text-gray-400 leading-relaxed">
                Powerful, system-integrated tools for GNU/Linux, Windows, and macOS built with modern frameworks.
            </p>
        </div>

        <!-- Service 3 -->
        <div class="glass-card p-12 rounded-3xl border border-white/5 flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-cyber-cyan/10 rounded-2xl flex items-center justify-center mb-8 border border-cyber-cyan/30 text-cyber-cyan group-hover:scale-110 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Firmware & Drivers</h3>
            <p class="text-gray-400 leading-relaxed">
                Low-level software development ensuring hardware compatibility and security at the deepest levels.
            </p>
        </div>

        <!-- Service 4 -->
        <div class="glass-card p-12 rounded-3xl border border-white/5 flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-green-500/10 rounded-2xl flex items-center justify-center mb-8 border border-green-500/30 text-green-400 group-hover:scale-110 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">Game Development</h3>
            <p class="text-gray-400 leading-relaxed">
                Open-source game engines and interactive experiences designed for cross-platform performance.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
