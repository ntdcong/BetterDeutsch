<?php
use App\Core\Session;
use App\Core\Auth;
use App\Core\Security;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$useHeader = in_array($path, ['/', '/login', '/register']);
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
        /* Mobile sidebar drawer */
        #mobile-drawer {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        #mobile-drawer.open {
            transform: translateX(0);
        }
        #drawer-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #drawer-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="min-h-screen flex font-sans antialiased <?php echo $useHeader ? 'flex-col' : ''; ?>">
    <?php if ($useHeader): ?>
        <?php if (!isset($hideHeaderFooter) || !$hideHeaderFooter): ?>
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
        <?php endif; ?>

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

        <?php if (!isset($hideHeaderFooter) || !$hideHeaderFooter): ?>
        <footer class="py-6 md:px-8 md:py-0 border-t border-border mt-auto">
            <div class="container mx-auto flex flex-col items-center justify-between gap-4 md:h-24 md:flex-row px-4">
                <p class="text-balance text-center text-sm leading-loose text-muted-foreground md:text-left">
                    Made with ❤️ by Duy Công.
                </p>
            </div>
        </footer>
        <?php endif; ?>
    <?php else: ?>
        <!-- Sidebar layout -->
        <?php if (!isset($hideHeaderFooter) || !$hideHeaderFooter): ?>
        <aside class="w-64 border-r border-border bg-card hidden md:flex flex-col sticky top-0 h-screen shrink-0">
            <div class="h-14 flex items-center px-6 border-b border-border shrink-0">
                <a class="flex items-center space-x-2" href="/">
                    <span class="font-bold">BetterDeutsch</span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= $path === '/' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Trang chủ
                </a>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= str_starts_with($path, '/lessons') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/lessons">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    Bài học
                </a>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= str_starts_with($path, '/notebooks') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/notebooks">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Sổ tay
                </a>
            </nav>
            <div class="p-4 mt-auto border-t border-border shrink-0">
                <?php if (Auth::check()): ?>
                    <div class="flex flex-col gap-2">
                        <form method="POST" action="/logout" class="w-full">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                            <button type="submit" class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-2">
                        <a href="/login" class="flex items-center justify-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground transition-colors border border-input">Đăng nhập</a>
                        <a href="/register" class="flex items-center justify-center gap-3 rounded-lg px-3 py-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors">Đăng ký</a>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
        <?php endif; ?>

        <!-- Mobile Drawer Overlay -->
        <?php if (!isset($hideHeaderFooter) || !$hideHeaderFooter): ?>
        <div id="drawer-overlay" class="fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeDrawer()"></div>
        <!-- Mobile Sidebar Drawer -->
        <aside id="mobile-drawer" class="fixed top-0 left-0 z-50 h-screen w-64 bg-card border-r border-border flex flex-col md:hidden">
            <div class="h-14 flex items-center justify-between px-6 border-b border-border shrink-0">
                <a class="flex items-center space-x-2" href="/">
                    <span class="font-bold">BetterDeutsch</span>
                </a>
                <button onclick="closeDrawer()" class="text-muted-foreground hover:text-foreground p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= $path === '/' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Trang chủ
                </a>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= str_starts_with($path, '/lessons') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/lessons">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    Bài học
                </a>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= str_starts_with($path, '/notebooks') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground' ?>" href="/notebooks">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Sổ tay
                </a>
            </nav>
            <div class="p-4 border-t border-border shrink-0">
                <?php if (Auth::check()): ?>
                    <form method="POST" action="/logout" class="w-full">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                        <button type="submit" class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            Đăng xuất
                        </button>
                    </form>
                <?php else: ?>
                    <div class="flex flex-col gap-2">
                        <a href="/login" class="flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground transition-colors border border-input">Đăng nhập</a>
                        <a href="/register" class="flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors">Đăng ký</a>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
        <?php endif; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Top Bar with Hamburger -->
            <?php if (!isset($hideHeaderFooter) || !$hideHeaderFooter): ?>
            <header class="md:hidden sticky top-0 z-30 w-full border-b border-border bg-background/95 backdrop-blur flex h-14 items-center px-4 shrink-0 gap-3">
                <button onclick="openDrawer()" class="text-muted-foreground hover:text-foreground p-1.5 rounded-md hover:bg-accent transition-colors shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
                <a class="flex items-center" href="/">
                    <span class="font-bold">BetterDeutsch</span>
                </a>
                <span class="ml-auto text-sm text-muted-foreground truncate max-w-[40%] text-right"><?= htmlspecialchars(ucfirst(trim(str_replace('/', ' ', $path), '/')), ENT_QUOTES, 'UTF-8') ?: 'Trang chủ' ?></span>
            </header>
            <?php endif; ?>

            <main class="flex-1 flex flex-col p-4 md:p-6 lg:p-8">
                <div class="mx-auto w-full flex-1 flex flex-col">
                    <?php if ($flashError = Session::getFlash('error')): ?>
                        <div class="bg-destructive/15 text-destructive border border-destructive/20 p-4 rounded-md mb-4 text-sm font-medium">
                            <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($flashSuccess = Session::getFlash('success')): ?>
                        <div class="bg-green-100 text-green-800 border border-green-200 p-4 rounded-md mb-4 text-sm font-medium">
                            <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    <?php endif; ?>

    <?php if (!$useHeader): ?>
    <script>
    function openDrawer() {
        document.getElementById('mobile-drawer').classList.add('open');
        document.getElementById('drawer-overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        document.getElementById('mobile-drawer').classList.remove('open');
        document.getElementById('drawer-overlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    </script>
    <?php endif; ?>
</body>
</html>
