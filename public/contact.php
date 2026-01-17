<?php
$page_title = "Contact Us";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validators.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid CSRF token.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (validate_required($name) && validate_email($email) && validate_required($message)) {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                set_flash_message('success', 'Your message has been sent successfully.');
                header('Location: contact.php');
                exit;
            } else {
                set_flash_message('error', 'Something went wrong. Please try again later.');
            }
        } else {
            set_flash_message('error', 'Please fill in all required fields with a valid email.');
        }
    }
}

require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-16 max-w-6xl mx-auto">
    <div>
        <h2 class="text-cyber-blue font-mono tracking-widest uppercase text-sm mb-4"># GET IN TOUCH</h2>
        <h1 class="text-5xl font-bold text-white mb-8">Contact Us</h1>
        <p class="text-xl text-gray-400 mb-8 leading-relaxed">
            Have questions about our projects or want to collaborate? We're always open to hearing from the community.
        </p>
        
        <div class="space-y-6">
            <div class="flex items-start space-x-4">
                <div class="text-cyber-blue mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-bold">Email Us Directly</h4>
                    <p class="text-gray-400">open-source-applications@tutamail.com</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="text-cyber-purple mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-bold">Developer Support</h4>
                    <p class="text-gray-400">Join our GitHub discussions or Discord server.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card p-8 md:p-12 rounded-3xl border border-white/10">
        <form action="contact.php" method="POST" class="space-y-6">
            <?php csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Full Name *</label>
                <input type="text" name="name" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Email Address *</label>
                <input type="email" name="email" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Subject</label>
                <input type="text" name="subject" class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Message *</label>
                <textarea name="message" rows="5" required class="w-full bg-cyber-black border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyber-blue transition"></textarea>
            </div>
            <button type="submit" class="w-full bg-cyber-blue text-black font-bold py-4 rounded-xl hover:bg-cyber-cyan transition shadow-[0_0_15px_rgba(0,210,255,0.4)]">
                Send Message
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
