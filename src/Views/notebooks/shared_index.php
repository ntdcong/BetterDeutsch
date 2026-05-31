<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Học qua chia sẻ', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(222.2 84% 4.9%)",
                        card: "hsl(0 0% 100%)",
                        cardForeground: "hsl(222.2 84% 4.9%)",
                        primary: "hsl(222.2 47.4% 11.2%)",
                        primaryForeground: "hsl(210 40% 98%)",
                        secondary: "hsl(210 40% 96.1%)",
                        secondaryForeground: "hsl(222.2 47.4% 11.2%)",
                        muted: "hsl(210 40% 96.1%)",
                        mutedForeground: "hsl(215.4 16.3% 46.9%)",
                        border: "hsl(214.3 31.8% 91.4%)",
                        input: "hsl(214.3 31.8% 91.4%)",
                        ring: "hsl(222.2 84% 4.9%)",
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f8fafc; }
        .hover-card { transition: all 0.2s ease-in-out; }
        .hover-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans text-foreground">
    <!-- Top Bar -->
    <div class="flex items-center justify-between p-4 bg-white/80 backdrop-blur border-b border-border sticky top-0 z-10">
        <div class="flex items-center gap-2">
            <div class="text-lg font-bold text-primary flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary text-primaryForeground flex items-center justify-center font-bold">BD</div>
                BetterDeutsch
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col px-4 py-12 sm:py-20 max-w-4xl w-full mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-foreground mb-4">
                <?= htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-muted-foreground text-base sm:text-lg max-w-2xl mx-auto">
                Bạn đã được chia sẻ một cuốn sổ tay từ vựng trên <strong>BetterDeutsch</strong>. 
                Hãy chọn một phương pháp học bên dưới để bắt đầu trau dồi vốn từ của bạn ngay lập tức!
            </p>
            <div class="mt-4 inline-flex items-center gap-2 bg-secondary/50 text-secondaryForeground px-3 py-1.5 rounded-full text-sm font-medium border border-border">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                <?= (int)($notebook['count'] ?? 0) ?> từ vựng
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full max-w-3xl mx-auto">
            <!-- Flashcard Option -->
            <a href="/shared/flashcard?token=<?= htmlspecialchars($shareToken, ENT_QUOTES, 'UTF-8') ?>" class="hover-card bg-card border-2 border-border rounded-2xl p-8 flex flex-col items-center text-center gap-4 group hover:border-primary/50">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M21 9H3"/><path d="M9 21V9"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-2 group-hover:text-primary transition-colors">Học Flashcard</h3>
                    <p class="text-sm text-muted-foreground">Lật thẻ để ôn tập từ vựng, nghe phát âm và tra cứu động từ chi tiết.</p>
                </div>
            </a>
            
            <!-- Practice Option -->
            <a href="/shared/practice?token=<?= htmlspecialchars($shareToken, ENT_QUOTES, 'UTF-8') ?>" class="hover-card bg-card border-2 border-border rounded-2xl p-8 flex flex-col items-center text-center gap-4 group hover:border-primary/50">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-2 group-hover:text-primary transition-colors">Luyện tập</h3>
                    <p class="text-sm text-muted-foreground">Làm bài tập trắc nghiệm, nối chữ, và luyện viết để ghi nhớ sâu hơn.</p>
                </div>
            </a>
        </div>
    </div>

    <div class="py-6 text-center text-sm text-muted-foreground border-t border-border mt-auto">
        &copy; <?= date('Y') ?> BetterDeutsch. Nền tảng học tiếng Đức miễn phí.
    </div>
</body>
</html>
