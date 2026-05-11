<?php ob_start(); ?>
<div class="py-12 w-full max-w-6xl mx-auto px-4" id="app">
    <div class="mb-8">
        <a href="/vocabularies?notebook_id=<?= $notebook['id'] ?>" class="text-sm font-medium text-muted-foreground hover:text-foreground inline-flex items-center mb-4 transition-colors">
            ← Trở lại danh sách từ vựng
        </a>
        <h2 class="text-3xl font-bold tracking-tight">Xem trước dữ liệu nhập</h2>
        <p class="text-muted-foreground mt-1">Sổ tay: <?= htmlspecialchars($notebook['name']) ?></p>
    </div>

    <!-- Progress bar -->
    <div id="progress-container" class="hidden mb-6">
        <div class="flex justify-between text-sm mb-2 font-medium">
            <span>Đang xử lý nhập dữ liệu...</span>
            <span id="progress-text">0%</span>
        </div>
        <div class="h-2 w-full bg-secondary rounded-full overflow-hidden">
            <div id="progress-bar" class="h-full bg-primary transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between bg-card p-4 rounded-xl border shadow-sm" id="toolbar">
        <div class="flex items-center gap-4">
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="filter-duplicates" class="h-4 w-4 rounded border-input text-primary focus:ring-primary" onchange="renderTable()">
                <label for="filter-duplicates" class="text-sm font-medium leading-none">Chỉ hiện từ trùng lặp</label>
            </div>
            <div class="text-sm text-muted-foreground">
                Đã chọn: <span id="selected-count" class="font-bold text-foreground">0</span> / <span id="total-count">0</span> từ
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="selectAll()" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">Chọn tất cả</button>
            <button onclick="deselectAll()" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">Bỏ chọn tất cả</button>
            <button onclick="startImport()" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 ml-2">Nhập dữ liệu đã chọn</button>
        </div>
    </div>

    <!-- Table -->
    <div class="border rounded-xl bg-card overflow-hidden shadow-sm" id="table-container">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[800px]">
                <thead class="bg-muted/50 text-muted-foreground text-xs uppercase font-medium whitespace-nowrap">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">
                            <input type="checkbox" id="check-all" class="h-4 w-4 rounded border-input" onchange="toggleAll(this)">
                        </th>
                        <th class="px-4 py-3">Từ vựng (Đức)</th>
                        <th class="px-4 py-3">Nghĩa (Việt)</th>
                        <th class="px-4 py-3">Loại từ</th>
                        <th class="px-4 py-3">Giống</th>
                        <th class="px-4 py-3">Số nhiều</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border" id="preview-tbody">
                    <!-- Rows rendered by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const rawData = <?= json_encode($previewData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
    const notebookId = <?= $notebook['id'] ?>;
    let selectedIds = new Set();
    
    // Auto-select non-duplicate words by default
    rawData.forEach(item => {
        if (!item.is_duplicate) {
            selectedIds.add(item.id);
        }
    });

    function renderTable() {
        const tbody = document.getElementById('preview-tbody');
        const filterDuplicates = document.getElementById('filter-duplicates').checked;
        
        tbody.innerHTML = '';
        
        let visibleCount = 0;
        let visibleSelectedCount = 0;

        rawData.forEach(item => {
            if (filterDuplicates && !item.is_duplicate) return;
            
            visibleCount++;
            const isSelected = selectedIds.has(item.id);
            if (isSelected) visibleSelectedCount++;

            const tr = document.createElement('tr');
            tr.className = `transition-colors hover:bg-muted/50 ${item.is_duplicate ? 'bg-red-50/50 dark:bg-red-950/20' : ''}`;
            
            tr.innerHTML = `
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="h-4 w-4 rounded border-input row-checkbox" value="${item.id}" ${isSelected ? 'checked' : ''} onchange="toggleRow('${item.id}', this)">
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <input type="text" value="${item.word}" class="w-full bg-transparent border-0 border-b border-transparent hover:border-input focus:border-primary focus:ring-0 px-1 py-0.5" onchange="updateData('${item.id}', 'word', this.value)">
                        ${item.is_duplicate ? '<span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 whitespace-nowrap">Trùng lặp</span>' : ''}
                    </div>
                </td>
                <td class="px-4 py-3">
                    <input type="text" value="${item.translation_vn}" class="w-full bg-transparent border-0 border-b border-transparent hover:border-input focus:border-primary focus:ring-0 px-1 py-0.5" onchange="updateData('${item.id}', 'translation_vn', this.value)">
                </td>
                <td class="px-4 py-3">
                    <input type="text" value="${item.word_type}" class="w-20 bg-transparent border-0 border-b border-transparent hover:border-input focus:border-primary focus:ring-0 px-1 py-0.5" onchange="updateData('${item.id}', 'word_type', this.value)">
                </td>
                <td class="px-4 py-3">
                    <input type="text" value="${item.article}" class="w-12 bg-transparent border-0 border-b border-transparent hover:border-input focus:border-primary focus:ring-0 px-1 py-0.5" onchange="updateData('${item.id}', 'article', this.value)">
                </td>
                <td class="px-4 py-3">
                    <input type="text" value="${item.plural_form}" class="w-full bg-transparent border-0 border-b border-transparent hover:border-input focus:border-primary focus:ring-0 px-1 py-0.5" onchange="updateData('${item.id}', 'plural_form', this.value)">
                </td>
            `;
            tbody.appendChild(tr);
        });

        if (visibleCount === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-muted-foreground">Không có dữ liệu phù hợp.</td></tr>';
        }

        document.getElementById('total-count').textContent = rawData.length;
        document.getElementById('selected-count').textContent = selectedIds.size;
        
        const checkAll = document.getElementById('check-all');
        checkAll.checked = visibleCount > 0 && visibleSelectedCount === visibleCount;
        checkAll.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visibleCount;
    }

    function toggleRow(id, el) {
        if (el.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }
        document.getElementById('selected-count').textContent = selectedIds.size;
        updateCheckAllState();
    }

    function updateCheckAllState() {
        const filterDuplicates = document.getElementById('filter-duplicates').checked;
        let visibleCount = 0;
        let visibleSelectedCount = 0;
        
        rawData.forEach(item => {
            if (filterDuplicates && !item.is_duplicate) return;
            visibleCount++;
            if (selectedIds.has(item.id)) visibleSelectedCount++;
        });

        const checkAll = document.getElementById('check-all');
        checkAll.checked = visibleCount > 0 && visibleSelectedCount === visibleCount;
        checkAll.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visibleCount;
    }

    function toggleAll(el) {
        const filterDuplicates = document.getElementById('filter-duplicates').checked;
        const isChecked = el.checked;
        
        rawData.forEach(item => {
            if (filterDuplicates && !item.is_duplicate) return;
            
            if (isChecked) {
                selectedIds.add(item.id);
            } else {
                selectedIds.delete(item.id);
            }
        });
        
        renderTable();
    }

    function selectAll() {
        rawData.forEach(item => selectedIds.add(item.id));
        document.getElementById('filter-duplicates').checked = false;
        renderTable();
    }

    function deselectAll() {
        selectedIds.clear();
        renderTable();
    }

    function updateData(id, field, value) {
        const item = rawData.find(i => i.id === id);
        if (item) {
            item[field] = value;
        }
    }

    async function startImport() {
        if (selectedIds.size === 0) {
            alert('Vui lòng chọn ít nhất 1 từ vựng để nhập.');
            return;
        }

        const itemsToImport = rawData.filter(item => selectedIds.has(item.id));
        
        document.getElementById('toolbar').classList.add('opacity-50', 'pointer-events-none');
        document.getElementById('table-container').classList.add('opacity-50', 'pointer-events-none');
        document.getElementById('progress-container').classList.remove('hidden');
        
        // Chunk processing to show progress bar properly, wait a bit for UI to update
        await new Promise(resolve => setTimeout(resolve, 50));
        
        const chunkSize = 50; // process 50 items per request
        let importedCount = 0;
        const total = itemsToImport.length;

        for (let i = 0; i < total; i += chunkSize) {
            const chunk = itemsToImport.slice(i, i + chunkSize);
            
            try {
                const response = await fetch('/vocabularies/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        notebook_id: notebookId,
                        items: chunk
                    })
                });
                
                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message);
                }
                
                importedCount += result.count;
                
                const progress = Math.round((Math.min(i + chunkSize, total) / total) * 100);
                document.getElementById('progress-bar').style.width = `${progress}%`;
                document.getElementById('progress-text').textContent = `${progress}%`;
                
            } catch (err) {
                alert('Có lỗi xảy ra trong quá trình nhập: ' + err.message);
                break;
            }
        }
        
        // Done
        document.getElementById('progress-text').textContent = 'Hoàn tất!';
        setTimeout(() => {
            window.location.href = '/vocabularies?notebook_id=' + notebookId;
        }, 1000);
    }

    // Initial render
    renderTable();
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
