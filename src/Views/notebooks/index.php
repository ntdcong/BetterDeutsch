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

// Function to render notebooks with "show more" logic
function renderNotebookGrid($notebooksList, $groupId = '') {
    $html = '<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 notebook-grid" data-group-id="' . $groupId . '">';
    $limit = 6;
    $count = 0;
    foreach ($notebooksList as $notebook) {
        $count++;
        $hiddenClass = $count > $limit ? 'hidden notebook-extra' : '';
        $html .= '<div class="notebook-item-wrapper ' . $hiddenClass . '" data-name="' . strtolower(htmlspecialchars($notebook['name'], ENT_QUOTES, 'UTF-8')) . '">';
        ob_start();
        include __DIR__ . '/_notebook_card.php';
        $html .= ob_get_clean();
        $html .= '</div>';
    }
    $html .= '</div>';

    if (count($notebooksList) > $limit) {
        $html .= '<div class="mt-6 flex justify-center btn-show-more-wrapper" data-group-id="' . $groupId . '">';
        $html .= '<button onclick="toggleShowMore(\'' . $groupId . '\')" class="inline-flex items-center justify-center rounded-full text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-6">';
        $html .= 'Xem thêm ' . (count($notebooksList) - $limit) . ' sổ tay';
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 transition-transform duration-200 icon-chevron"><path d="m6 9 6 6 6-6"/></svg>';
        $html .= '</button></div>';
    }
    return $html;
}
?>
<div class="py-10 w-full max-w-6xl mx-auto px-4 md:px-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-10 gap-6">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-foreground">Sổ tay từ vựng</h2>
            <p class="text-muted-foreground mt-2 text-base">Quản lý và học các bộ từ vựng của bạn một cách dễ dàng.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="search-notebook" placeholder="Tìm sổ tay..." class="flex h-10 w-full rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-shadow">
            </div>
            <button onclick="openModal('modal-group')" class="flex-1 md:flex-none inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" title="Quản lý nhóm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                Nhóm
            </button>
            <button onclick="openModal('modal-notebook')" class="flex-1 md:flex-none inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tạo sổ tay
            </button>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-search-state" class="hidden text-center py-16 border rounded-xl bg-card border-dashed">
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-muted-foreground opacity-50 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <h3 class="text-lg font-medium text-foreground">Không tìm thấy sổ tay nào</h3>
        <p class="text-muted-foreground mt-1 text-sm">Thử tìm kiếm với từ khóa khác.</p>
    </div>

    <?php if (empty($notebooks)): ?>
        <div class="text-center py-20 border rounded-xl bg-card shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
            </div>
            <h3 class="text-xl font-semibold text-foreground">Chưa có sổ tay nào</h3>
            <p class="text-muted-foreground mt-2 max-w-sm mx-auto">Tạo sổ tay đầu tiên của bạn để bắt đầu lưu trữ và học từ vựng tiếng Đức.</p>
            <button onclick="openModal('modal-notebook')" class="mt-6 inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-6 py-2 shadow-sm">
                Tạo sổ tay ngay
            </button>
        </div>
    <?php else: ?>

        <div id="notebooks-container">
            <?php $groupIndex = 0; foreach ($groupedNotebooks as $groupName => $nbs): $groupIndex++; ?>
                <div class="mb-12 group-section">
                    <div class="flex items-center justify-between mb-5 border-b pb-2">
                        <h3 class="text-xl font-bold flex items-center gap-2 text-foreground">
                            <span class="p-1.5 rounded-md bg-secondary text-secondary-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            </span>
                            <?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                            <span class="text-sm font-normal text-muted-foreground ml-2">(<?= count($nbs) ?>)</span>
                        </h3>
                    </div>
                    <?= renderNotebookGrid($nbs, 'group-' . $groupIndex) ?>
                </div>
            <?php endforeach; ?>

            <?php if (!empty($ungroupedNotebooks)): ?>
                <div class="mb-12 group-section">
                    <div class="flex items-center justify-between mb-5 border-b pb-2">
                        <h3 class="text-xl font-bold flex items-center gap-2 text-foreground">
                            <span class="p-1.5 rounded-md bg-muted text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="9" x2="9" y1="21" y2="9"/></svg>
                            </span>
                            Sổ tay tự do
                            <span class="text-sm font-normal text-muted-foreground ml-2">(<?= count($ungroupedNotebooks) ?>)</span>
                        </h3>
                    </div>
                    <?= renderNotebookGrid($ungroupedNotebooks, 'group-ungrouped') ?>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<!-- Modal: Tạo / Sửa Sổ tay -->
<div id="modal-notebook" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4 sm:p-6 transition-opacity">
    <div class="bg-card text-card-foreground shadow-xl border rounded-xl w-full max-w-md animate-in fade-in zoom-in-95 relative overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-muted/30">
            <h3 class="font-semibold text-lg tracking-tight" id="modal-notebook-title">Tạo sổ tay mới</h3>
            <button onclick="closeModal('modal-notebook')" class="text-muted-foreground hover:text-foreground hover:bg-muted p-1.5 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <form id="form-notebook" action="/notebooks/create" method="POST" class="space-y-5">
                <input type="hidden" name="id" id="notebook_id">
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Tên sổ tay <span class="text-destructive">*</span></label>
                    <input type="text" name="name" id="notebook_name" required placeholder="Nhập tên sổ tay..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-shadow">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none">Nhóm (Tùy chọn)</label>
                    <select name="notebook_group_id" id="notebook_group_id" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-shadow">
                        <option value="">-- Không có nhóm --</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
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

<!-- Modal: Quản lý nhóm -->
<div id="modal-group" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4 sm:p-6 transition-opacity">
    <div class="bg-card text-card-foreground shadow-xl border rounded-xl w-full max-w-lg animate-in fade-in zoom-in-95 relative overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-muted/30">
            <h3 class="font-semibold text-lg tracking-tight">Quản lý Nhóm sổ tay</h3>
            <button onclick="closeModal('modal-group')" class="text-muted-foreground hover:text-foreground hover:bg-muted p-1.5 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
            <div class="mb-6">
                <h4 class="text-sm font-medium mb-3 text-foreground">Tạo nhóm mới</h4>
                <form action="/notebook-groups/create" method="POST" class="flex gap-2">
                    <input type="text" name="name" required placeholder="Nhập tên nhóm..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-shadow">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Thêm
                    </button>
                </form>
            </div>

            <div>
                <h4 class="text-sm font-medium mb-3 text-foreground">Danh sách nhóm hiện tại</h4>
                <div class="space-y-2">
                    <?php if(empty($groups)): ?>
                        <div class="text-center py-6 border rounded-lg border-dashed bg-muted/20">
                            <p class="text-sm text-muted-foreground">Chưa có nhóm nào.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($groups as $g): ?>
                        <div class="flex items-center justify-between p-3 border rounded-lg group-item bg-background hover:bg-muted/20 transition-colors">
                            <span class="text-sm font-medium group-name-display truncate pr-4"><?= htmlspecialchars($g['name']) ?></span>
                            <form action="/notebook-groups/update" method="POST" class="hidden flex-1 group-edit-form gap-2 mr-2">
                                <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($g['name']) ?>" class="flex h-8 w-full rounded-md border border-input bg-background px-2 py-1 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-8 px-3 shrink-0">Lưu</button>
                                <button type="button" class="inline-flex items-center justify-center rounded-md text-xs font-medium border bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3 shrink-0 btn-cancel-edit">Hủy</button>
                            </form>
                            <div class="flex items-center gap-1 group-actions shrink-0">
                                <button type="button" class="text-muted-foreground hover:text-primary hover:bg-primary/10 p-1.5 rounded-md transition-colors btn-edit-group" title="Sửa tên nhóm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <form action="/notebook-groups/delete" method="POST" class="inline" onsubmit="return confirm('Xóa nhóm này? Các sổ tay bên trong sẽ không bị xóa.')">
                                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                    <button type="submit" class="text-muted-foreground hover:text-destructive hover:bg-destructive/10 p-1.5 rounded-md transition-colors" title="Xóa nhóm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Chia sẻ Sổ tay -->
<div id="modal-share" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm hidden flex items-center justify-center p-4 sm:p-6 transition-opacity">
    <div class="bg-card text-card-foreground shadow-xl border rounded-xl w-full max-w-md animate-in fade-in zoom-in-95 relative overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-muted/30">
            <h3 class="font-semibold text-lg tracking-tight">Chia sẻ Sổ tay</h3>
            <button onclick="closeModal('modal-share')" class="text-muted-foreground hover:text-foreground hover:bg-muted p-1.5 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-muted-foreground" id="share-modal-desc">
                Bật chia sẻ để tạo liên kết công khai. Bất kỳ ai có liên kết này đều có thể xem và học từ vựng trong sổ tay này mà không cần đăng nhập.
            </p>
            
            <div class="flex items-center justify-between p-4 border rounded-lg bg-secondary/20">
                <div>
                    <h4 class="font-medium text-sm text-foreground">Trạng thái chia sẻ</h4>
                    <p class="text-xs text-muted-foreground mt-1" id="share-status-text">Đang tắt</p>
                </div>
                <button id="btn-toggle-share" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors bg-muted" onclick="executeToggleShare()">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-1" id="share-toggle-knob"></span>
                </button>
            </div>

            <div id="share-link-container" class="hidden space-y-3 mt-4">
                <div>
                    <label class="text-xs font-medium text-foreground">Link học Flashcard</label>
                    <div class="flex gap-2 mt-1">
                        <input type="text" id="share-link-flashcard" readonly class="flex h-9 w-full rounded-md border border-input bg-muted px-3 py-1 text-sm shadow-sm" value="">
                        <button onclick="copyShareLink('share-link-flashcard')" class="inline-flex items-center justify-center rounded-md text-sm font-medium border bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">Copy</button>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-foreground">Link Luyện tập</label>
                    <div class="flex gap-2 mt-1">
                        <input type="text" id="share-link-practice" readonly class="flex h-9 w-full rounded-md border border-input bg-muted px-3 py-1 text-sm shadow-sm" value="">
                        <button onclick="copyShareLink('share-link-practice')" class="inline-flex items-center justify-center rounded-md text-sm font-medium border bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    // small timeout to allow display:block to apply before animating opacity if needed
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

let currentShareNotebookId = null;

function toggleShareNotebook(id, name, isShared, token) {
    currentShareNotebookId = id;
    openModal('modal-share');
    updateShareUI(isShared, token);
}

function updateShareUI(isShared, token) {
    const knob = document.getElementById('share-toggle-knob');
    const btn = document.getElementById('btn-toggle-share');
    const statusText = document.getElementById('share-status-text');
    const linkContainer = document.getElementById('share-link-container');
    const inputFlashcard = document.getElementById('share-link-flashcard');
    const inputPractice = document.getElementById('share-link-practice');

    if (isShared) {
        knob.classList.replace('translate-x-1', 'translate-x-6');
        btn.classList.replace('bg-muted', 'bg-primary');
        statusText.textContent = 'Đang bật';
        statusText.classList.add('text-primary');
        linkContainer.classList.remove('hidden');
        
        const baseUrl = window.location.origin;
        inputFlashcard.value = `${baseUrl}/shared/flashcard?token=${token}`;
        inputPractice.value = `${baseUrl}/shared/practice?token=${token}`;
    } else {
        knob.classList.replace('translate-x-6', 'translate-x-1');
        btn.classList.replace('bg-primary', 'bg-muted');
        statusText.textContent = 'Đang tắt';
        statusText.classList.remove('text-primary');
        linkContainer.classList.add('hidden');
    }
}

async function executeToggleShare() {
    if (!currentShareNotebookId) return;
    
    try {
        const formData = new FormData();
        formData.append('id', currentShareNotebookId);
        
        const response = await fetch('/notebooks/toggle-share', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        if (data.success) {
            updateShareUI(data.is_shared, data.share_token);
            // Optionally reload page on close to update the notebook card data attributes, 
            // but for now the user can just use the modal. A page reload is safer.
        } else {
            alert('Có lỗi xảy ra: ' + (data.error || 'Unknown error'));
        }
    } catch (e) {
        alert('Lỗi kết nối.');
    }
}

function copyShareLink(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    const btn = input.nextElementSibling;
    const originalText = btn.textContent;
    btn.textContent = 'Copied!';
    btn.classList.add('text-green-600', 'border-green-600');
    
    setTimeout(() => {
        btn.textContent = originalText;
        btn.classList.remove('text-green-600', 'border-green-600');
    }, 2000);
}

// Group Edit toggle
document.querySelectorAll('.btn-edit-group').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.group-item');
        item.querySelector('.group-name-display').classList.add('hidden');
        item.querySelector('.group-actions').classList.add('hidden');
        item.querySelector('.group-edit-form').classList.remove('hidden');
        item.querySelector('.group-edit-form').classList.add('flex');
        item.querySelector('.group-edit-form input[type="text"]').focus();
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

// Show more / less logic
function toggleShowMore(groupId) {
    const grid = document.querySelector(`.notebook-grid[data-group-id="${groupId}"]`);
    const btnWrapper = document.querySelector(`.btn-show-more-wrapper[data-group-id="${groupId}"]`);
    const btn = btnWrapper.querySelector('button');
    const icon = btn.querySelector('.icon-chevron');
    
    const hiddenItems = grid.querySelectorAll('.notebook-item-wrapper.notebook-extra');
    
    if (grid.classList.contains('expanded')) {
        // Collapse
        hiddenItems.forEach(item => item.classList.add('hidden'));
        grid.classList.remove('expanded');
        btn.innerHTML = `Xem thêm ${hiddenItems.length} sổ tay <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 transition-transform duration-200 icon-chevron"><path d="m6 9 6 6 6-6"/></svg>`;
    } else {
        // Expand
        hiddenItems.forEach(item => item.classList.remove('hidden'));
        grid.classList.add('expanded');
        btn.innerHTML = `Thu gọn <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 transition-transform duration-200 icon-chevron rotate-180"><path d="m6 9 6 6 6-6"/></svg>`;
    }
}

// Search functionality
document.getElementById('search-notebook').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const sections = document.querySelectorAll('.group-section');
    let totalVisible = 0;

    sections.forEach(section => {
        const items = section.querySelectorAll('.notebook-item-wrapper');
        let visibleInGroup = 0;

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            // If searching, show all matches regardless of "show more" state
            // If empty search, restore "show more" state
            if (searchTerm === '') {
                if (item.classList.contains('notebook-extra')) {
                    const grid = item.closest('.notebook-grid');
                    if (!grid.classList.contains('expanded')) {
                        item.classList.add('hidden');
                    } else {
                        item.classList.remove('hidden');
                    }
                } else {
                    item.classList.remove('hidden');
                }
                visibleInGroup++;
            } else {
                if (name.includes(searchTerm)) {
                    item.classList.remove('hidden');
                    visibleInGroup++;
                } else {
                    item.classList.add('hidden');
                }
            }
        });

        // Toggle section visibility based on if any items are visible
        if (visibleInGroup > 0) {
            section.classList.remove('hidden');
            totalVisible += visibleInGroup;
        } else {
            section.classList.add('hidden');
        }

        // Toggle 'show more' button visibility based on search
        const btnWrapper = section.querySelector('.btn-show-more-wrapper');
        if (btnWrapper) {
            if (searchTerm !== '') {
                btnWrapper.classList.add('hidden');
            } else {
                btnWrapper.classList.remove('hidden');
            }
        }
    });

    // Handle empty state
    const emptyState = document.getElementById('empty-search-state');
    const container = document.getElementById('notebooks-container');
    
    if (searchTerm !== '' && totalVisible === 0) {
        emptyState.classList.remove('hidden');
        if(container) container.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        if(container) container.classList.remove('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';

