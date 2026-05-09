<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Học Flashcard', ENT_QUOTES, 'UTF-8') ?></title>
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
        body { background-color: #f8fafc; overflow: hidden; touch-action: none; }
        .perspective-1000 { perspective: 1000px; }
        .transform-style-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }
        .rotate-y-180 { transform: rotateY(180deg); }
        
        /* Card Stack Container */
        .card-stack {
            position: absolute;
            inset: 0;
            width: 100%;
            max-width: 36rem;
            margin: 0 auto;
        }
        
        .card-wrapper {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            transform-origin: center bottom;
        }

        .card-inner {
            position: relative;
            width: 100%; height: 100%;
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .is-flipped .card-inner { transform: rotateY(180deg); }
        
        .card-front, .card-back {
            position: absolute;
            inset: 0;
            backface-visibility: hidden;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .card-back { transform: rotateY(180deg); overflow-y: auto; }
        
        /* Modal */
        #verb-modal { transition: opacity 0.3s ease; }
        #verb-modal-content { transition: transform 0.3s ease, opacity 0.3s ease; }
        
        .bg-der-light { background-color: #eff6ff; border: 2px solid #93c5fd; }
        .bg-die-light { background-color: #fef2f2; border: 2px solid #fca5a5; }
        .bg-das-light { background-color: #f0fdf4; border: 2px solid #86efac; }
        
        /* Custom scrollbar for card-back */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-screen flex flex-col font-sans text-foreground select-none" id="flashcard-app" data-notebook-id="<?= $notebook['id'] ?>">
    <!-- Top Bar -->
    <div class="flex items-center justify-between p-3 sm:p-4 bg-white/50 backdrop-blur border-b border-border z-10 shrink-0 gap-2">
        <a href="/notebooks" class="text-sm font-medium text-muted-foreground hover:text-foreground inline-flex items-center transition-colors shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
            <span class="hidden sm:inline">Sổ tay</span>
        </a>
        
        <div class="flex-1 text-center text-base sm:text-lg font-extrabold tracking-tight text-foreground truncate px-2">
            <?= htmlspecialchars($notebook['name'] ?? 'Học Flashcard', ENT_QUOTES, 'UTF-8') ?>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button id="toggle-autoplay-btn" class="text-xs font-medium text-muted-foreground hover:text-foreground border border-border rounded-lg px-2 py-1.5 inline-flex items-center transition-colors whitespace-nowrap" title="Bật/Tắt tự động đọc">
                Phát âm: Thủ công
            </button>
            <button onclick="openVocabModal()" class="inline-flex items-center justify-center rounded-lg text-xs sm:text-sm font-medium bg-primary text-primaryForeground hover:bg-primary/90 h-8 sm:h-9 px-2 sm:px-4 shadow-sm transition-all active:scale-95 text-white">
                <span class="sm:hidden">+</span><span class="hidden sm:inline">Thêm từ mới</span>
            </button>
        </div>
    </div>
 
    <!-- Main Card Area -->
    <div class="flex-1 flex flex-col px-4 pt-3 pb-4 sm:px-6 sm:pt-4 sm:pb-5 overflow-hidden perspective-1000">
        <!-- Progress & Shuffle -->
        <div class="w-full max-w-2xl mx-auto flex items-center justify-between mb-3 shrink-0 gap-4">
            <div class="flex items-center gap-3 flex-1">
                <span class="text-sm font-bold text-muted-foreground w-10 text-right" id="progress-text">0/0</span>
                <div class="h-2.5 flex-1 bg-secondary rounded-full overflow-hidden border border-border/50">
                    <div class="h-full bg-primary transition-all duration-300 ease-out" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>
            <button id="btn-shuffle" class="text-xs sm:text-sm font-medium text-muted-foreground hover:text-foreground transition-colors flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 rounded-lg hover:bg-secondary flex-shrink-0 border border-transparent hover:border-border/50" title="Trộn từ"> 
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18h1.4c1.3 0 2.5-.6 3.3-1.7l6.1-8.6c.7-1.1 2-1.7 3.3-1.7H22"/><path d="m18 2 4 4-4 4"/><path d="M2 6h1.9c1.5 0 2.9.9 3.6 2.2"/><path d="M22 18h-5.9c-1.3 0-2.6-.7-3.3-1.8l-.5-.8"/><path d="m18 14 4 4-4 4"/></svg>
                <span>Trộn từ</span>
            </button> 
        </div>

        <!-- Card Stack: fills space but capped to prevent overflow -->
        <div class="flex-1 w-full max-w-2xl mx-auto relative min-h-0 max-h-[52vh] sm:max-h-[58vh] md:max-h-[62vh]">
            <div class="card-stack" id="card-stack">
                <div class="absolute inset-0 flex items-center justify-center p-12 border-2 border-dashed border-border rounded-2xl bg-white">
                    <div class="text-center text-muted-foreground font-medium animate-pulse" id="loading-indicator">Đang tải dữ liệu...</div>
                </div>
            </div>
        </div>

        <br/>
        <br/>
        <!-- Controls: right below the card, no gap -->
        <div class="w-full max-w-2xl mx-auto flex items-center justify-center gap-3 pt-3 shrink-0">
            <button id="btn-prev" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border-2 border-border bg-white hover:bg-secondary hover:text-foreground h-11 flex-1 disabled:opacity-50 shadow-sm transition-all active:scale-95 text-muted-foreground">
                TRƯỚC
            </button>
            <button id="btn-flip" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border-2 border-primary bg-primary hover:bg-primary/90 h-12 flex-1 shadow-sm transition-all active:scale-95 text-white">
                LẬT THẺ
            </button>
            <button id="btn-next" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border-2 border-border bg-white hover:bg-secondary hover:text-foreground h-11 flex-1 disabled:opacity-50 shadow-sm transition-all active:scale-95 text-muted-foreground">
                SAU
            </button>
        </div>
    </div>

    <!-- Verb Modal -->
    <div id="verb-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 opacity-0 pointer-events-none px-4">
        <div id="verb-modal-content" class="bg-card w-full max-w-lg rounded-xl shadow-2xl scale-95 opacity-0 overflow-hidden flex flex-col max-h-[85vh]">
            <div class="flex justify-between items-center p-4 border-b border-border bg-muted/30">
                <h3 class="font-bold text-lg text-foreground" id="verb-title">Tra cứu động từ</h3>
                <button id="btn-close-modal" class="text-muted-foreground hover:text-foreground text-3xl leading-none px-2">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar" id="verb-body">
                Loading...
            </div>
        </div>
    </div>

    <!-- Modal: Tạo / Sửa Từ vựng -->
    <div id="modal-vocab" class="fixed inset-0 z-[60] bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-card text-card-foreground shadow-lg border rounded-xl w-full max-w-lg animate-in fade-in zoom-in-95 p-6 relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeVocabModal()" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <h3 class="font-semibold text-xl tracking-tight mb-4" id="modal-vocab-title">Thêm từ vựng</h3>
            <form id="form-vocab" action="/vocabularies/create" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="vocab_id">
                <input type="hidden" name="notebook_id" value="<?= $notebook['id'] ?>">
                <input type="hidden" name="redirect" value="/notebooks/flashcard?id=<?= $notebook['id'] ?>">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2 col-span-2 sm:col-span-1">
                        <label class="text-sm font-medium leading-none">Từ vựng (Tiếng Đức) *</label>
                        <input type="text" name="word" id="vocab_word" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                    <div class="space-y-2 col-span-2 sm:col-span-1">
                        <label class="text-sm font-medium leading-none">Nghĩa tiếng Việt *</label>
                        <input type="text" name="translation_vn" id="vocab_translation_vn" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Loại từ</label>
                        <select name="word_type" id="vocab_word_type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring">
                            <option value="none">-- Chọn loại từ --</option>
                            <option value="noun">Danh từ (Noun)</option>
                            <option value="verb">Động từ (Verb)</option>
                            <option value="adj">Tính từ (Adj)</option>
                            <option value="adv">Trạng từ (Adv)</option>
                            <option value="prep">Giới từ (Prep)</option>
                            <option value="pron">Đại từ (Pron)</option>
                            <option value="conj">Liên từ (Conj)</option>
                            <option value="article">Mạo từ (Article)</option>
                            <option value="phrase">Cụm từ (Phrase)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Giống</label>
                        <select name="article" id="vocab_article" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring">
                            <option value="">-- Không có --</option>
                            <option value="der">Der</option>
                            <option value="die">Die</option>
                            <option value="das">Das</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Số nhiều</label>
                        <input type="text" name="plural_form" id="vocab_plural_form" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Giới từ</label>
                        <input type="text" name="preposition" id="vocab_preposition" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                    <div class="space-y-2 col-span-2">
                        <label class="text-sm font-medium leading-none">Ghi chú</label>
                        <textarea name="note" id="vocab_note" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
                    </div>
                </div>
                <div class="pt-4 flex gap-2 justify-end">
                    <button type="button" onclick="closeVocabModal()" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">Hủy</button>
                    <button type="submit" class="inline-flex text-white items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">Lưu từ vựng</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/assets/js/flashcard.js?v=<?= time() ?>"></script>
    <script>
    function openVocabModal() {
        document.getElementById('modal-vocab').classList.remove('hidden');
    }
    function closeVocabModal() {
        document.getElementById('modal-vocab').classList.add('hidden');
        document.getElementById('form-vocab').reset();
        document.getElementById('form-vocab').action = '/vocabularies/create';
        document.getElementById('modal-vocab-title').textContent = 'Thêm từ vựng mới';
        document.getElementById('vocab_id').value = '';
    }
    // Listen for custom event 'editVocab' from flashcard.js
    window.addEventListener('editVocab', (e) => {
        const v = e.detail;
        document.getElementById('form-vocab').action = '/vocabularies/update';
        document.getElementById('modal-vocab-title').textContent = 'Chỉnh sửa từ vựng';
        
        document.getElementById('vocab_id').value = v.id;
        document.getElementById('vocab_word').value = v.word;
        document.getElementById('vocab_translation_vn').value = v.translation_vn;
        document.getElementById('vocab_word_type').value = v.word_type || 'none';
        document.getElementById('vocab_article').value = v.article || '';
        document.getElementById('vocab_plural_form').value = v.plural_form || '';
        document.getElementById('vocab_preposition').value = v.preposition || '';
        document.getElementById('vocab_note').value = v.note || '';
        
        openVocabModal();
    });
    </script>
</body>
</html>
