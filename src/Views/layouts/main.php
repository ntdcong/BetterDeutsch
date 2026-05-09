<?php
use App\Core\Session;
use App\Core\Auth;
use App\Core\Security;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BetterDeutsch', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        border: "hsl(var(--border))",
                        input: "hsl(var(--input))",
                        ring: "hsl(var(--ring))",
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                        primary: {
                            DEFAULT: "hsl(var(--primary))",
                            foreground: "hsl(var(--primary-foreground))",
                        },
                        secondary: {
                            DEFAULT: "hsl(var(--secondary))",
                            foreground: "hsl(var(--secondary-foreground))",
                        },
                        destructive: {
                            DEFAULT: "hsl(var(--destructive))",
                            foreground: "hsl(var(--destructive-foreground))",
                        },
                        muted: {
                            DEFAULT: "hsl(var(--muted))",
                            foreground: "hsl(var(--muted-foreground))",
                        },
                        accent: {
                            DEFAULT: "hsl(var(--accent))",
                            foreground: "hsl(var(--accent-foreground))",
                        },
                        popover: {
                            DEFAULT: "hsl(var(--popover))",
                            foreground: "hsl(var(--popover-foreground))",
                        },
                        card: {
                            DEFAULT: "hsl(var(--card))",
                            foreground: "hsl(var(--card-foreground))",
                        },
                    },
                    borderRadius: {
                        lg: "var(--radius)",
                        md: "calc(var(--radius) - 2px)",
                        sm: "calc(var(--radius) - 4px)",
                    },
                }
            }
        }
    </script>
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 222.2 47.4% 11.2%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222.2 47.4% 11.2%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96.1%;
            --accent-foreground: 222.2 47.4% 11.2%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 222.2 84% 4.9%;
            --radius: 0.5rem;
        }
        body {
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased">
    <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto flex h-14 max-w-screen-2xl items-center px-4">
            <div class="mr-4 flex">
                <a class="mr-6 flex items-center space-x-2" href="/">
                    <span class="font-bold sm:inline-block">BetterDeutsch</span>
                </a>
                <nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
                    <a class="transition-colors hover:text-foreground/80 text-foreground/60" href="/">Trang chủ</a>
                    <a class="transition-colors hover:text-foreground/80 text-foreground/60" href="/lessons">Bài học</a>
                    <a class="transition-colors hover:text-foreground/80 text-foreground/60" href="/notebooks">Sổ tay</a>
                </nav>
            </div>
            <div class="flex flex-1 items-center justify-end space-x-2">
                <nav class="flex items-center space-x-2">
                    <?php if (Auth::check()): ?>
                        <span class="text-sm font-medium text-muted-foreground mr-2 hidden sm:inline-block">Xin chào!</span>
                        <form method="POST" action="/logout" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                Đăng xuất
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">Đăng nhập</a>
                        <a href="/register" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">Đăng ký</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-1 flex flex-col container mx-auto px-4 max-w-screen-2xl">
        <?php if ($flashError = Session::getFlash('error')): ?>
            <div class="bg-destructive/15 text-destructive border border-destructive/20 p-4 rounded-md mt-4 text-sm font-medium">
                <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <?php if ($flashSuccess = Session::getFlash('success')): ?>
            <div class="bg-green-100 text-green-800 border border-green-200 p-4 rounded-md mt-4 text-sm font-medium">
                <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </main>

    <footer class="py-6 md:px-8 md:py-0 border-t border-border mt-auto">
        <div class="container mx-auto flex flex-col items-center justify-between gap-4 md:h-24 md:flex-row px-4">
            <p class="text-balance text-center text-sm leading-loose text-muted-foreground md:text-left">
                Xây dựng bởi BetterDeutsch. Hệ thống học tiếng Đức thuần PHP.
            </p>
        </div>
    </footer>
</body>
</html>
