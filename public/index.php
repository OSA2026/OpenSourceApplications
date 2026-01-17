<?php
$page_title = "The Future - Open Source";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 overflow-hidden">
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <h2 class="text-cyber-blue font-mono tracking-widest uppercase text-sm mb-4 animate-pulse"># RESEARCH & DEVELOPMENT</h2>
        <h1 class="text-5xl md:text-7xl font-bold text-white mb-8 leading-tight">
            THE FUTURE — <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-blue to-cyber-purple">Open Source</span>
        </h1>
        <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto leading-relaxed">
            Open Source Applications is a technology studio focused on secure, transparent, and community-driven software for GNU/Linux and modern platforms.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
            <a href="projects.php" class="w-full sm:w-auto bg-cyber-blue text-black font-bold px-8 py-4 rounded-xl hover:bg-cyber-cyan transition shadow-[0_0_20px_rgba(0,210,255,0.3)] flex items-center justify-center">
                Explore Projects
            </a>
            <a href="submit.php" class="w-full sm:w-auto glass-card px-8 py-4 rounded-xl border border-white/10 hover:border-cyber-blue transition flex items-center justify-center">
                Submit Your Project
            </a>
        </div>
    </div>

    <!-- Visual Element -->
    <div class="mt-20 relative max-w-5xl mx-auto">
        <div class="glass-card aspect-video rounded-3xl border border-white/5 overflow-hidden flex items-center justify-center relative">
            <div class="absolute inset-0 bg-gradient-to-br from-cyber-blue/5 to-transparent"></div>
            <!-- Mock UI/Design Element -->
            <div class="text-cyber-blue font-mono text-sm space-y-2 opacity-50">
                <p>> Initializing secure environment...</p>
                <p>> Loading community modules...</p>
                <p>> System state: <span class="text-green-400">OPTIMAL</span></p>
            </div>
            
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 border-2 border-cyber-blue/20 rounded-full animate-ping"></div>
            </div>
        </div>
    </div>
</section>

<!-- Why OSA Section -->
<section class="py-24">
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold text-white mb-4">Why Open Source Applications</h2>
        <div class="w-20 h-1 bg-cyber-blue mx-auto rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Card 1 -->
        <div class="glass-card p-10 rounded-2xl hover:translate-y-[-8px] transition duration-300">
            <div class="w-12 h-12 bg-cyber-blue/10 rounded-lg flex items-center justify-center mb-6 border border-cyber-blue/20 text-cyber-blue">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.016z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Enhanced Security</h3>
            <p class="text-gray-400 leading-relaxed">
                Rigorous review process and security-first development approach for every project under our umbrella.
            </p>
        </div>

        <!-- Card 2 -->
        <div class="glass-card p-10 rounded-2xl hover:translate-y-[-8px] transition duration-300">
            <div class="w-12 h-12 bg-cyber-purple/10 rounded-lg flex items-center justify-center mb-6 border border-cyber-purple/20 text-cyber-purple">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Community Driven</h3>
            <p class="text-gray-400 leading-relaxed">
                Empowering developers and users to shape the future of software through collaboration and open standards.
            </p>
        </div>

        <!-- Card 3 -->
        <div class="glass-card p-10 rounded-2xl hover:translate-y-[-8px] transition duration-300">
            <div class="w-12 h-12 bg-cyber-cyan/10 rounded-lg flex items-center justify-center mb-6 border border-cyber-cyan/20 text-cyber-cyan">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-4">Future Ready</h3>
            <p class="text-gray-400 leading-relaxed">
                Utilizing modern tech stacks and system-level optimizations for performance on Linux and mobile platforms.
            </p>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20">
    <div class="glass-card rounded-3xl p-12 md:p-20 relative overflow-hidden text-center border border-cyber-blue/20">
        <div class="absolute -bottom-1/2 -right-1/4 w-1/2 h-full bg-cyber-blue/10 blur-[120px] rounded-full"></div>
        
        <h2 class="text-4xl font-bold text-white mb-6 relative z-10">Ready to build the future?</h2>
        <p class="text-gray-400 mb-10 max-w-xl mx-auto relative z-10 text-lg">
            Join our network of developers or explore our curated collection of high-quality open-source tools.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 relative z-10">
            <a href="contact.php" class="bg-white text-black font-bold px-8 py-4 rounded-xl hover:bg-gray-200 transition">Contact Us</a>
            <a href="signup.php" class="text-white hover:text-cyber-blue font-bold px-8 py-4 transition">Join as Developer →</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
