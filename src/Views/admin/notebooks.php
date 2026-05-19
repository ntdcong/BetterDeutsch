<?php ob_start(); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Sổ tay chung</h1>
            <p class="text-muted-foreground mt-1">Quản lý các sổ tay hiển thị công khai cho mọi người dùng.</p>
        </div>
        <button onclick="openModal('modal-notebook')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Tạo sổ tay chung
        </button>
    </div>

    <?php if (empty($notebooks)): ?>
        <div class="text-center py-20 border rounded-xl bg-card shadow-sm">
            <h3 class="text-xl font-semibold text-foreground">Chưa có sổ tay chung nào</h3>
        </div>
    <?php else: ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($notebooks as $notebook): ?>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col transition-all hover:shadow-md relative group h-full">
                    <div class="absolute top-3 right-3 flex gap-1 z-10 bg-card/80 backdrop-blur-sm rounded-md p-1 shadow-sm border">
                        <button onclick="editNotebook(<?= $notebook['id'] ?>, '<?= htmlspecialchars(addslashes($notebook['name'])) ?>', '<?= htmlspecialchars(addslashes($notebook['note'] ?? '')) ?>')" class="p-1.5 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-md transition-colors" title="Chỉnh sửa">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                        </button>
                        <form action="/notebooks/delete" method="POST" class="inline m-0" onsubmit="return confirm('Xóa sổ tay này sẽ xóa toàn bộ từ vựng bên trong. Bạn có chắc chắn?')">
                            <input type="hidden" name="id" value="<?= $notebook['id'] ?>">
                            <!-- redirect back to admin -->
                            <input type="hidden" name="redirect" value="/admin/notebooks">
                            <button type="submit" class="p-1.5 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-md transition-colors" title="Xóa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="font-semibold text-xl leading-tight tracking-tight mb-2 pr-12 text-foreground"><?= htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-sm text-muted-foreground line-clamp-3 mb-4 flex-1"><?= $notebook['note'] ? nl2br(htmlspecialchars($notebook['note'], ENT_QUOTES, 'UTF-8')) : '<span class="italic opacity-50">Không có mô tả</span>' ?></p>
                        
                        <div class="mt-auto pt-4 flex items-center text-xs text-muted-foreground border-t border-border/50">
                            <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-md font-medium mr-2 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                <?= (int)($notebook['count'] ?? 0) ?> từ
                            </span>
                            <span class="flex items-center gap-1 ml-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <?= date('d/m/Y', strtotime($notebook['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6 pt-0 mt-auto flex gap-2">
                        <a href="/vocabularies?notebook_id=<?= $notebook['id'] ?>&admin_layout=1" class="flex-1 inline-flex items-center justify-center rounded-md text-[13px] font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                            Quản lý từ vựng
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Tạo / Sửa Sổ tay -->
<div id="modal-notebook" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4 sm:p-6 transition-opacity">
    <div class="bg-card text-card-foreground shadow-xl border rounded-xl w-full max-w-md animate-in fade-in zoom-in-95 relative overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-muted/30">
            <h3 class="font-semibold text-lg tracking-tight" id="modal-notebook-title">Tạo sổ tay chung</h3>
            <button onclick="closeModal('modal-notebook')" class="text-muted-foreground hover:text-foreground hover:bg-muted p-1.5 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <form id="form-notebook" action="/notebooks/create" method="POST" class="space-y-5">
                <input type="hidden" name="id" id="notebook_id">
                <input type="hidden" name="redirect" value="/admin/notebooks">
                <input type="hidden" name="is_public" value="1">
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Tên sổ tay <span class="text-destructive">*</span></label>
                    <input type="text" name="name" id="notebook_name" required placeholder="Nhập tên sổ tay..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-shadow">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Mô tả (Tùy chọn)</label>
                    <textarea name="note" id="notebook_note" placeholder="Mô tả ngắn gọn về sổ tay này..." class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-shadow resize-y"></textarea>
                </div>
                <div class="pt-4 flex gap-3 justify-end border-t mt-6">
                    <button type="button" onclick="closeModal('modal-notebook')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">Hủy</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-6 py-2 shadow-sm">Lưu sổ tay</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    setTimeout(() => {
        const input = document.querySelector(`#${id} input[type="text"]`);
        if(input) input.focus();
    }, 50);
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    if(id === 'modal-notebook') {
        document.getElementById('form-notebook').reset();
        document.getElementById('form-notebook').action = '/notebooks/create';
        document.getElementById('modal-notebook-title').textContent = 'Tạo sổ tay chung';
        document.getElementById('notebook_id').value = '';
    }
}

function editNotebook(id, name, note) {
    document.getElementById('form-notebook').action = '/notebooks/update';
    document.getElementById('modal-notebook-title').textContent = 'Chỉnh sửa sổ tay';
    document.getElementById('notebook_id').value = id;
    document.getElementById('notebook_name').value = name;
    document.getElementById('notebook_note').value = note || '';
    openModal('modal-notebook');
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/admin.php';
