<?php ob_start(); ?>
<div class="py-12 w-full max-w-6xl mx-auto px-4">
    <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Sổ tay từ vựng</h2>
            <p class="text-muted-foreground mt-1">Danh sách các bài học và bộ từ vựng của bạn.</p>
        </div>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
            Tạo sổ tay mới
        </button>
    </div>

    <?php if (empty($notebooks)): ?>
        <div class="text-center py-12 border rounded-xl bg-card">
            <h3 class="text-lg font-medium">Chưa có sổ tay nào</h3>
            <p class="text-muted-foreground mt-1">Hãy tạo một sổ tay mới để bắt đầu học.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($notebooks as $notebook): ?>
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col transition-all hover:shadow-md">
                    <div class="p-6 flex-1">
                        <h3 class="font-semibold text-xl leading-none tracking-tight mb-3"><?= htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8') ?></h3>
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
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" title="Làm Quiz (Đang phát triển)">
                            Quiz
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
