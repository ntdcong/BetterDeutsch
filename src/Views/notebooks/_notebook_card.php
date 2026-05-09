<?php
$isOwner = (!empty($notebook['user_id']) && $notebook['user_id'] == \App\Core\Auth::id());
?>
<div class="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col transition-all hover:shadow-md relative group">
    
    <?php if ($isOwner): ?>
    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
        <button onclick="editNotebook(<?= $notebook['id'] ?>, '<?= htmlspecialchars(addslashes($notebook['name'])) ?>', '<?= htmlspecialchars(addslashes($notebook['note'] ?? '')) ?>', '<?= $notebook['notebook_group_id'] ?? '' ?>')" class="p-1.5 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-md transition-colors" title="Chỉnh sửa">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
        </button>
        <form action="/notebooks/delete" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sổ tay này cùng toàn bộ từ vựng bên trong? Hành động không thể hoàn tác.')">
            <input type="hidden" name="id" value="<?= $notebook['id'] ?>">
            <button type="submit" class="p-1.5 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-md transition-colors" title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="p-6 flex-1">
        <h3 class="font-semibold text-xl leading-none tracking-tight mb-3 pr-16"><?= htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if ($notebook['note']): ?>
            <p class="text-sm text-muted-foreground line-clamp-2"><?= htmlspecialchars($notebook['note'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <div class="mt-4 flex items-center text-xs text-muted-foreground">
            <?php if ($notebook['is_admin_updated']): ?>
                <span class="bg-primary/10 text-primary px-2 py-1 rounded font-medium mr-2">Hệ thống</span>
            <?php elseif ($notebook['is_public']): ?>
                <span class="bg-secondary text-secondary-foreground px-2 py-1 rounded font-medium mr-2">Công khai</span>
            <?php endif; ?>
            <span><?= date('d/m/Y', strtotime($notebook['created_at'])) ?></span>
        </div>
    </div>
    <div class="p-6 pt-0 mt-auto flex gap-2">
        <a href="/notebooks/flashcard?id=<?= $notebook['id'] ?>" class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            Học Flashcard
        </a>
        <a href="/vocabularies?notebook_id=<?= $notebook['id'] ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" title="Quản lý từ vựng">
            Từ vựng
        </a>
    </div>
</div>
