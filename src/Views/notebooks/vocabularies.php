<?php ob_start(); 
$isOwner = (!empty($notebook['user_id']) && $notebook['user_id'] == \App\Core\Auth::id());
?>
<div class="py-12 w-full max-w-6xl mx-auto px-4">
    <div class="mb-8">
        <a href="/notebooks" class="text-sm font-medium text-muted-foreground hover:text-foreground inline-flex items-center mb-4 transition-colors">
            ← Trở lại Danh sách sổ tay
        </a>
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Từ vựng: <?= htmlspecialchars($notebook['name']) ?></h2>
                <p class="text-muted-foreground mt-1">Quản lý các từ vựng trong bộ này.</p>
            </div>
            <div class="flex gap-2">
                <a href="/notebooks/flashcard?id=<?= $notebook['id'] ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Học Flashcard
                </a>
                <?php if ($isOwner): ?>
                <button onclick="openVocabModal()" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    Thêm từ mới
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="mb-6 flex gap-2 max-w-sm">
        <form action="/vocabularies" method="GET" class="flex-1 flex gap-2">
            <input type="hidden" name="notebook_id" value="<?= $notebook['id'] ?>">
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Tìm kiếm từ vựng..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-secondary text-secondary-foreground hover:bg-secondary/80 h-10 px-4 py-2 shrink-0">Tìm</button>
        </form>
    </div>

    <!-- Table -->
    <div class="border rounded-xl bg-card overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-muted/50 text-muted-foreground text-xs uppercase font-medium">
                    <tr>
                        <th class="px-4 py-3">Từ vựng</th>
                        <th class="px-4 py-3">Giống/Loại</th>
                        <th class="px-4 py-3">Số nhiều</th>
                        <th class="px-4 py-3">Nghĩa tiếng Việt</th>
                        <th class="px-4 py-3">Ghi chú</th>
                        <?php if ($isOwner): ?>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php if (empty($vocabularies)): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Không tìm thấy từ vựng nào.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($vocabularies as $v): ?>
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-4 py-3 font-medium"><?= htmlspecialchars($v['word']) ?></td>
                            <td class="px-4 py-3">
                                <?php if ($v['article']): ?>
                                    <span class="font-semibold <?php
                                        if($v['article'] === 'der') echo 'text-blue-600 dark:text-blue-400';
                                        elseif($v['article'] === 'die') echo 'text-red-500 dark:text-red-400';
                                        elseif($v['article'] === 'das') echo 'text-green-600 dark:text-green-400';
                                    ?>"><?= $v['article'] ?></span>
                                <?php endif; ?>
                                <span class="text-xs bg-secondary text-secondary-foreground px-2 py-0.5 rounded ml-1"><?= $v['word_type'] ?></span>
                            </td>
                            <td class="px-4 py-3"><?= htmlspecialchars($v['plural_form'] ?? '') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($v['translation_vn']) ?></td>
                            <td class="px-4 py-3 text-muted-foreground"><?= htmlspecialchars($v['note'] ?? '') ?></td>
                            
                            <?php if ($isOwner): ?>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick='editVocab(<?= json_encode($v, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' class="text-muted-foreground hover:text-primary transition-colors p-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></button>
                                    <form action="/vocabularies/delete" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa từ này?')">
                                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                        <input type="hidden" name="notebook_id" value="<?= $notebook['id'] ?>">
                                        <button type="submit" class="text-muted-foreground hover:text-destructive transition-colors p-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="mt-6 flex justify-center gap-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?notebook_id=<?= $notebook['id'] ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $i ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 w-9 border <?= $i === $page ? 'border-primary bg-primary text-primary-foreground' : 'border-input bg-background hover:bg-accent hover:text-accent-foreground' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>

<!-- Modal: Tạo / Sửa Từ vựng -->
<div id="modal-vocab" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-card text-card-foreground shadow-lg border rounded-xl w-full max-w-lg animate-in fade-in zoom-in-95 p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeVocabModal()" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <h3 class="font-semibold text-xl tracking-tight mb-4" id="modal-vocab-title">Thêm từ vựng</h3>
        <form id="form-vocab" action="/vocabularies/create" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="vocab_id">
            <input type="hidden" name="notebook_id" value="<?= $notebook['id'] ?>">
            
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
                    <label class="text-sm font-medium leading-none">Giống (Dành cho Danh từ)</label>
                    <select name="article" id="vocab_article" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring">
                        <option value="">-- Không có --</option>
                        <option value="der">Der (Giống đực)</option>
                        <option value="die">Die (Giống cái)</option>
                        <option value="das">Das (Giống trung)</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Số nhiều</label>
                    <input type="text" name="plural_form" id="vocab_plural_form" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Giới từ đi kèm</label>
                    <input type="text" name="preposition" id="vocab_preposition" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <div class="space-y-2 col-span-2">
                    <label class="text-sm font-medium leading-none">Ghi chú (Ví dụ, đồng nghĩa...)</label>
                    <textarea name="note" id="vocab_note" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
                </div>
            </div>
            <div class="pt-4 flex gap-2 justify-end">
                <button type="button" onclick="closeVocabModal()" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">Hủy</button>
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">Lưu từ vựng</button>
            </div>
        </form>
    </div>
</div>

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
function editVocab(v) {
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
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
