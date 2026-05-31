<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Luyện tập', ENT_QUOTES, 'UTF-8') ?></title>
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
                        der: "#3b82f6", // blue-500
                        die: "#ef4444", // red-500
                        das: "#22c55e", // green-500
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f8fafc; }
        .mode-card {
            transition: all 0.2s ease-in-out;
        }
        .mode-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }
        .match-card {
            transition: all 0.2s;
            cursor: pointer;
            user-select: none;
        }
        .match-card.selected {
            border-color: hsl(222.2 47.4% 11.2%);
            background-color: hsl(210 40% 96.1%);
        }
        .match-card.matched {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .match-card.error {
            border-color: #ef4444;
            background-color: #fef2f2;
            animation: shake 0.4s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans text-foreground" id="practice-app" data-notebook-id="<?= $notebook['id'] ?>" data-is-shared="<?= !empty($isSharedView) ? '1' : '0' ?>" data-share-token="<?= htmlspecialchars($shareToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <!-- Top Bar -->
    <div class="flex items-center justify-between p-4 bg-white/80 backdrop-blur border-b border-border sticky top-0 z-10">
        <div class="flex items-center gap-2">
            <?php if (empty($isSharedView)): ?>
            <a href="/notebooks" class="text-sm font-medium text-muted-foreground hover:text-foreground inline-flex items-center transition-colors" id="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                <span class="hidden sm:inline">Trở lại</span>
            </a>
            <?php else: ?>
            <div class="text-sm font-medium text-primary inline-flex items-center" id="btn-back-shared">
                BetterDeutsch
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center text-lg font-extrabold tracking-tight text-foreground truncate px-4 flex-1">
            <span id="header-title">Luyện tập: <?= htmlspecialchars($notebook['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="w-[80px]"></div> <!-- Spacer for centering -->
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col px-4 py-8 max-w-4xl w-full mx-auto" id="main-container">
        <!-- Loading State -->
        <div id="loading-state" class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
            <p class="text-muted-foreground">Đang tải dữ liệu từ vựng...</p>
        </div>

        <!-- Mode Selection Menu (Hidden initially) -->
        <div id="menu-state" class="hidden flex-col items-center w-full">
            <h2 class="text-2xl font-bold mb-2">Chọn chế độ luyện tập</h2>
            <p class="text-muted-foreground mb-8 text-center">Hãy chọn một bài tập để củng cố kiến thức của bạn</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                <!-- Mode 1 -->
                <button onclick="startMode('quiz_meaning')" class="mode-card bg-card border border-border rounded-xl p-6 flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg">Trắc nghiệm nghĩa</h3>
                    <p class="text-sm text-muted-foreground">Chọn nghĩa đúng của từ vựng tiếng Đức được cho.</p>
                </button>
                
                <!-- Mode 2 -->
                <button onclick="startMode('quiz_article')" class="mode-card bg-card border border-border rounded-xl p-6 flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <h3 class="font-bold text-lg">Đoán giống danh từ</h3>
                    <p class="text-sm text-muted-foreground">Chọn giống (der, die, das) chính xác cho các danh từ.</p>
                </button>
                
                <!-- Mode 3 -->
                <button onclick="startMode('matching')" class="mode-card bg-card border border-border rounded-xl p-6 flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8"/><path d="m3 21 8-8"/><path d="m21 21-8-8"/></svg>
                    </div>
                    <h3 class="font-bold text-lg">Nối từ vựng</h3>
                    <p class="text-sm text-muted-foreground">Ghép cặp từ tiếng Đức với nghĩa tiếng Việt tương ứng.</p>
                </button>
                
                <!-- Mode 4 -->
                <button onclick="startMode('writing')" class="mode-card bg-card border border-border rounded-xl p-6 flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg">Luyện viết từ</h3>
                    <p class="text-sm text-muted-foreground">Gõ lại từ vựng tiếng Đức dựa trên nghĩa tiếng Việt.</p>
                </button>
            </div>
        </div>

        <!-- Quiz State -->
        <div id="quiz-state" class="hidden flex-col items-center w-full max-w-2xl mx-auto">
            <div class="w-full flex items-center justify-between mb-4">
                <span class="text-sm font-bold text-muted-foreground" id="quiz-progress-text">Câu 1/10</span>
                <div class="h-2 flex-1 mx-4 bg-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-primary transition-all duration-300" id="quiz-progress-bar" style="width: 0%"></div>
                </div>
                <span class="text-sm font-bold text-green-600" id="quiz-score-text">0 điểm</span>
            </div>
            
            <div class="w-full bg-card border border-border rounded-2xl p-8 shadow-sm flex flex-col items-center min-h-[300px] justify-center relative" id="quiz-card">
                <h3 class="text-3xl font-extrabold text-center mb-2" id="quiz-question">Câu hỏi</h3>
                <p class="text-muted-foreground text-center mb-8" id="quiz-hint">Gợi ý</p>
                
                <!-- Quiz Meaning / Article Options -->
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-3" id="quiz-options">
                    <!-- Options will be injected here -->
                </div>
                
                <!-- Quiz Writing Input -->
                <div class="w-full hidden flex-col items-center gap-4" id="quiz-writing-container">
                    <input type="text" id="quiz-writing-input" class="w-full text-center text-xl font-bold p-4 rounded-xl border-2 border-input focus:border-primary focus:outline-none focus:ring-0 transition-colors" placeholder="Nhập từ tiếng Đức..." autocomplete="off" spellcheck="false">
                    <button id="btn-submit-writing" class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-colors">Kiểm tra</button>
                </div>
            </div>
            
            <!-- Result Feedback -->
            <div id="quiz-feedback" class="mt-4 w-full p-4 rounded-xl font-bold text-center hidden">
                <!-- Feedback message -->
            </div>
            
            <button id="btn-next-quiz" class="mt-6 px-8 py-3 text-white bg-primary text-primary-foreground font-bold rounded-xl hover:bg-primary/90 transition-colors hidden">
                Tiếp tục
            </button>
        </div>

        <!-- Matching State -->
        <div id="matching-state" class="hidden flex-col items-center w-full">
            <div class="w-full flex items-center justify-between mb-4 max-w-3xl mx-auto">
                <p class="text-muted-foreground font-medium">Tìm và chọn các cặp từ vựng - nghĩa tương ứng.</p>
                <span class="text-sm font-bold bg-primary/10 text-primary px-3 py-1 rounded-full" id="matching-timer">00:00</span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full max-w-4xl" id="matching-grid">
                <!-- Matching cards will be injected here -->
            </div>
        </div>

        <!-- Summary State -->
        <div id="summary-state" class="hidden flex-col items-center justify-center w-full py-12">
            <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h2 class="text-3xl font-extrabold mb-2">Hoàn thành!</h2>
            <p class="text-muted-foreground mb-8 text-center max-w-md" id="summary-message">Bạn đã hoàn thành bài luyện tập xuất sắc.</p>
            
            <div class="grid grid-cols-2 gap-4 w-full max-w-sm mb-8">
                <div class="bg-card border border-border p-4 rounded-xl text-center">
                    <p class="text-muted-foreground text-sm font-medium">Đúng</p>
                    <p class="text-2xl font-bold text-green-600" id="summary-correct">0</p>
                </div>
                <div class="bg-card border border-border p-4 rounded-xl text-center">
                    <p class="text-muted-foreground text-sm font-medium">Sai</p>
                    <p class="text-2xl font-bold text-red-500" id="summary-incorrect">0</p>
                </div>
            </div>
            
            <button onclick="showMenu()" class="px-8 py-3 text-white bg-primary font-bold rounded-xl hover:bg-primary/90 transition-colors">
                Luyện tập lại
            </button>
        </div>

    </div>
 
    <script src="/assets/js/practice.js?v=<?= time() ?>"></script>
</body>
</html>
