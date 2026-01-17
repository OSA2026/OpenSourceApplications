<?php
$page_title = "About Us";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<section class="max-w-4xl mx-auto">
    <div class="mb-16">
        <h2 class="text-cyber-blue font-mono tracking-widest uppercase text-sm mb-4"># OUR STORY</h2>
        <h1 class="text-5xl font-bold text-white mb-8">About Us</h1>
        <p class="text-xl text-gray-300 leading-relaxed mb-12">
            Open Source Applications is a research-driven software studio working at the intersection of cyber security, system software, and user-focused design.
        </p>
        
        <div class="prose prose-invert max-w-none text-gray-400 leading-loose space-y-6">
            <p>
                Founded on the principles of transparency and freedom, our mission is to provide a platform for high-quality projects that prioritize user privacy and system efficiency. We believe that open-source software is the foundation of a secure digital future.
            </p>
            <p>
                Our studio acts as both an incubator and a curator. We personally review every submission to ensure it meets our standards for code quality, security practices, and licensing. This is not just an app store; it is a curated collection of tools built for the community, by the community.
            </p>
        </div>
    </div>
    
    <div class="mb-20">
        <h2 class="text-cyber-blue font-mono tracking-widest uppercase text-sm mb-8 text-center"># CORE FOUNDERS</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Sish Alam -->
            <div class="glass-card p-8 rounded-3xl border border-white/5 flex flex-col items-center text-center group hover:border-cyber-blue/30 transition duration-500">
                <div class="w-32 h-32 rounded-full border-2 border-cyber-blue/20 p-1 mb-6 group-hover:border-cyber-blue transition duration-500">
                    <img src="https://pbs.twimg.com/profile_images/1974641581279092737/oPTwrYtT_400x400.jpg" alt="Sish Alam" class="w-full h-full rounded-full object-cover grayscale group-hover:grayscale-0 transition duration-500">
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">Sish Alam</h3>
                <a href="https://twitter.com/AlamSish" target="_blank" class="text-cyber-blue text-sm font-mono mb-4 hover:underline">@AlamSish</a>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                    GNU/Linux user!! 🐃❤️🐧| 🇵🇰 🇵🇸<br>
                    Desktop Application Developer 👨💻<br>
                    Game Developer 🎮
                </p>
            </div>

            <!-- Nishchal Acharya -->
            <div class="glass-card p-8 rounded-3xl border border-white/5 flex flex-col items-center text-center group hover:border-cyber-purple/30 transition duration-500">
                <div class="w-32 h-32 rounded-full border-2 border-cyber-purple/20 p-1 mb-6 group-hover:border-cyber-purple transition duration-500">
                    <img src="https://pbs.twimg.com/profile_images/1903396944372011008/1cyF83NE_400x400.jpg" alt="Nishchal Acharya" class="w-full h-full rounded-full object-cover grayscale group-hover:grayscale-0 transition duration-500">
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">Nishchal Acharya</h3>
                <a href="https://twitter.com/AcrNischal" target="_blank" class="text-cyber-purple text-sm font-mono mb-4 hover:underline">@AcrNischal</a>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                    Web & App Developer | Crypto & Blockchain | Linux Explorer 🛡️✨
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-20">
        <div class="glass-card p-8 rounded-2xl border-l-4 border-cyber-blue">
            <h3 class="text-xl font-bold text-white mb-4">Our Mission</h3>
            <p class="text-gray-400">
                To bridge the gap between complex system-level software and user-friendly applications while maintaining absolute transparency.
            </p>
        </div>
        <div class="glass-card p-8 rounded-2xl border-l-4 border-cyber-purple">
            <h3 class="text-xl font-bold text-white mb-4">Our Vision</h3>
            <p class="text-gray-400">
                A world where every device runs on open-source software that the user owns, controls, and understands.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
