<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | " . SITE_NAME : SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            black: '#0a0b0d',
                            gray: '#161b22',
                            blue: '#00d2ff',
                            cyan: '#3aedff',
                            purple: '#7000ff'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    backgroundImage: {
                        'glass-gradient': 'linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 100%)',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(22, 27, 34, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        .text-glow {
            text-shadow: 0 0 10px rgba(58, 237, 255, 0.5);
        }
        .bg-glow {
            filter: blur(100px);
            opacity: 0.2;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
        body {
            background-color: #0d1117;
            color: #c9d1d9;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Gradient Blobs -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 overflow-hidden">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-cyber-blue bg-glow rounded-full"></div>
        <div class="absolute top-1/2 -right-1/4 w-1/2 h-1/2 bg-cyber-purple bg-glow rounded-full"></div>
    </div>
