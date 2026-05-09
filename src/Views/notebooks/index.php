<?php ob_start(); 
$groupedNotebooks = [];
$ungroupedNotebooks = [];

foreach ($notebooks as $nb) {
    if ($nb['notebook_group_id']) {
        $groupedNotebooks[$nb['group_name']][] = $nb;
    } else {
        $ungroupedNotebooks[] = $nb;
    }
}
?>
<div class="py-12 w-full max-w-6xl mx-auto px-4">
    <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Sổ tay từ vựng</h2>
            <p class="text-muted-foreground mt-1">Danh sách các bài học và bộ từ vựng của bạn.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('modal-group')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                Quản lý Nhóm
            </button>
            <button onclick="openModal('modal-notebook')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                Tạo sổ tay mới
            </button>
        </div>
    </div>

    <?php if (empty($notebooks)): ?>
        <div class="text-center py-12 border rounded-xl bg-card">
            <h3 class="text-lg font-medium">Chưa có sổ tay nào</h3>
            <p class="text-muted-foreground mt-1">Hãy tạo một sổ tay mới để bắt đầu học.</p>
        </div>
    <?php else: ?>

        <?php foreach ($groupedNotebooks as $groupName => $nbs): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-foreground/90">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($nbs as $notebook): ?>
                        <?php include __DIR__ . '/_notebook_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($ungroupedNotebooks)): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-foreground/90">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="9" x2="9" y1="21" y2="9"/></svg>
                    Sổ tay tự do
                </h3>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($ungroupedNotebooks as $notebook): ?>
                        <?php include __DIR__ . '/_notebook_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Modal: Tạo / Sửa Sổ tay -->
<div id="modal-notebook" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-card text-card-foreground shadow-lg border rounded-xl w-full max-w-md animate-in fade-in zoom-in-95 p-6 relative">
        <button onclick="closeModal('modal-notebook')" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <h3 class="font-semibold text-xl tracking-tight mb-4" id="modal-notebook-title">Tạo sổ tay mới</h3>
        <form id="form-notebook" action="/notebooks/create" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="notebook_id">
            <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Tên sổ tay</label>
                <input type="text" name="name" id="notebook_name" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
            </div>
            <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Nhóm (Tùy chọn)</label>
                <select name="notebook_group_id" id="notebook_group_id" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <option value="">-- Không có nhóm --</option>
                    <?php foreach ($groups as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-medium leading-none">Ghi chú (Tùy chọn)</label>
                <textarea name="note" id="notebook_note" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"></textarea>
            </div>
            <div class="pt-2 flex gap-2 justify-end">
                <button type="button" onclick="closeModal('modal-notebook')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">Hủy</button>
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">Lưu sổ tay</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Quản lý nhóm -->
<div id="modal-group" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-card text-card-foreground shadow-lg border rounded-xl w-full max-w-md animate-in fade-in zoom-in-95 p-6 relative">
        <button onclick="closeModal('modal-group')" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
        <h3 class="font-semibold text-xl tracking-tight mb-4">Quản lý Nhóm</h3>
        
        <!-- List Groups -->
        <div class="mb-6 space-y-2 max-h-48 overflow-y-auto">
            <?php if(empty($groups)): ?>
                <p class="text-sm text-muted-foreground">Chưa có nhóm nào.</p>
            <?php endif; ?>
            <?php foreach ($groups as $g): ?>
                <div class="flex items-center justify-between p-2 border rounded-md group-item">
                    <span class="text-sm font-medium group-name-display"><?= htmlspecialchars($g['name']) ?></span>
                    <form action="/notebook-groups/update" method="POST" class="hidden flex-1 group-edit-form gap-2 mr-2">
                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($g['name']) ?>" class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-8 px-2 shrink-0">Lưu</button>
                        <button type="button" class="inline-flex items-center justify-center rounded-md text-xs font-medium hover:bg-accent hover:text-accent-foreground h-8 px-2 shrink-0 btn-cancel-edit">Hủy</button>
                    </form>
                    <div class="flex items-center gap-1 group-actions">
                        <button type="button" class="text-muted-foreground hover:bg-accent p-1.5 rounded-md transition-colors btn-edit-group" title="Sửa tên nhóm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                        </button>
                        <form action="/notebook-groups/delete" method="POST" class="inline" onsubmit="return confirm('Xóa nhóm này? Các sổ tay bên trong sẽ không bị xóa.')">
                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                            <button type="submit" class="text-destructive hover:bg-destructive/10 p-1.5 rounded-md transition-colors" title="Xóa nhóm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h4 class="text-sm font-medium mb-2">Tạo nhóm mới</h4>
        <form action="/notebook-groups/create" method="POST" class="flex gap-2">
            <input type="text" name="name" required placeholder="Tên nhóm..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 shrink-0">Thêm</button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    if(id === 'modal-notebook') {
        document.getElementById('form-notebook').reset();
        document.getElementById('form-notebook').action = '/notebooks/create';
        document.getElementById('modal-notebook-title').textContent = 'Tạo sổ tay mới';
        document.getElementById('notebook_id').value = '';
    }
}
function editNotebook(id, name, note, groupId) {
    document.getElementById('form-notebook').action = '/notebooks/update';
    document.getElementById('modal-notebook-title').textContent = 'Chỉnh sửa sổ tay';
    document.getElementById('notebook_id').value = id;
    document.getElementById('notebook_name').value = name;
    document.getElementById('notebook_note').value = note || '';
    document.getElementById('notebook_group_id').value = groupId || '';
    openModal('modal-notebook');
}

document.querySelectorAll('.btn-edit-group').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.group-item');
        item.querySelector('.group-name-display').classList.add('hidden');
        item.querySelector('.group-actions').classList.add('hidden');
        item.querySelector('.group-edit-form').classList.remove('hidden');
        item.querySelector('.group-edit-form').classList.add('flex');
    });
});
document.querySelectorAll('.btn-cancel-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.group-item');
        item.querySelector('.group-name-display').classList.remove('hidden');
        item.querySelector('.group-actions').classList.remove('hidden');
        item.querySelector('.group-edit-form').classList.add('hidden');
        item.querySelector('.group-edit-form').classList.remove('flex');
    });
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
