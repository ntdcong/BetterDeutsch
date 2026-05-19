<?php
$isOwner = (!empty($notebook['user_id']) && $notebook['user_id'] == \App\Core\Auth::id()) || \App\Core\Auth::isAdmin();
?>
<div class="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col transition-all hover:shadow-md hover:border-primary/20 relative group h-full">
    
    <?php if ($isOwner): ?>
    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1 z-10 bg-card/80 backdrop-blur-sm rounded-md p-1 shadow-sm border">
        <button onclick="editNotebook(<?= $notebook['id'] ?>, '<?= htmlspecialchars(addslashes($notebook['name'])) ?>', '<?= htmlspecialchars(addslashes($notebook['note'] ?? '')) ?>', <?= $notebook['notebook_group_id'] ?: 'null' ?>)" class="p-1.5 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-md transition-colors" title="Chỉnh sửa">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
        </button>
        <form action="/notebooks/delete" method="POST" class="inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sổ tay này cùng toàn bộ từ vựng bên trong? Hành động không thể hoàn tác.')">
            <input type="hidden" name="id" value="<?= $notebook['id'] ?>">
            <button type="submit" class="p-1.5 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-md transition-colors" title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="p-6 flex-1 flex flex-col">
        <h3 class="font-semibold text-xl leading-tight tracking-tight mb-2 pr-12 text-foreground"><?= htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if ($notebook['note']): ?>
            <p class="text-sm text-muted-foreground line-clamp-3 mb-4 flex-1"><?= nl2br(htmlspecialchars($notebook['note'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php else: ?>
            <p class="text-sm text-muted-foreground/50 italic mb-4 flex-1">Không có mô tả</p>
        <?php endif; ?>
        
        <div class="mt-auto pt-4 flex items-center text-xs text-muted-foreground border-t border-border/50">
            <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-md font-medium mr-2 flex items-center gap-1" title="Tổng số từ vựng">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                <?= (int)($notebook['count'] ?? 0) ?> từ
            </span>

            <?php if ($notebook['is_admin_updated']): ?>
                <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-md font-medium mr-2 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="-10 -258 596 532" class="w-3 h-3 fill-current"><path d="M208-24c53 0 96-43 96-96s-43-96-96-96-96 43-96 96 43 96 96 96zm0-224c71 0 128 57 128 128S279 8 208 8 80-49 80-120s57-128 128-128zM176 88C97 88 32 153 32 232v16c0 9-7 16-16 16s-16-7-16-16v-16C0 135 79 56 176 56h64c97 0 176 79 176 176v16c0 9-7 16-16 16s-16-7-16-16v-16c0-79-64-144-144-144h-64zM343-7c7-9 13-17 19-27 11 7 24 10 38 10 44 0 80-36 80-80s-36-80-80-80c-9 0-18 2-26 5-4-11-8-20-14-30 13-4 26-7 40-7 62 0 112 50 112 112S462 8 400 8c-21 0-40-6-57-15zm82 113c-8-12-18-24-28-34h3c97 0 176 79 176 176 0 9-7 16-16 16s-16-7-16-16c0-71-51-130-119-142z"/></svg>                    Chung
                </span>
            <?php elseif ($notebook['is_public']): ?>
                <span class="bg-secondary text-secondary-foreground px-2 py-0.5 rounded-md font-medium mr-2 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                    Công khai
                </span>
            <?php endif; ?>
            <span class="flex items-center gap-1 ml-auto" title="Ngày tạo">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <?= date('d/m/Y', strtotime($notebook['created_at'])) ?>
            </span>
        </div>
    </div>
    
    <div class="p-6 pt-0 mt-auto flex flex-wrap gap-2">
        <a href="/notebooks/flashcard?id=<?= $notebook['id'] ?>" class="flex-[1_1_auto] inline-flex items-center justify-center rounded-md text-[13px] font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-2 shadow-sm whitespace-nowrap" title="Học Flashcard">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 shrink-0"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M21 9H3"/><path d="M9 21V9"/></svg>
            Flashcard
        </a>
        <a href="/notebooks/practice?id=<?= $notebook['id'] ?>" class="flex-[1_1_auto] inline-flex items-center justify-center rounded-md text-[13px] font-medium transition-colors border border-input bg-secondary hover:bg-secondary/80 text-secondary-foreground h-9 px-2 shadow-sm whitespace-nowrap" title="Luyện tập">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 shrink-0"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
            Luyện tập
        </a>
        <a href="/vocabularies?notebook_id=<?= $notebook['id'] ?>" class="flex-[1_1_auto] inline-flex items-center justify-center rounded-md text-[13px] font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-2 whitespace-nowrap" title="Quản lý từ vựng">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 shrink-0"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            Quản lý
        </a>
    </div>
</div>
