<?php
use App\Core\Auth;
ob_start();
?>
<div class="flex-1 flex flex-col items-center justify-center py-20 md:py-32 w-full relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-4xl bg-primary/5 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="space-y-6 max-w-4xl px-4 text-center">
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-foreground">
            Học tiếng Đức <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-teal-400">thông minh và hiệu quả hơn</span>
        </h1>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10 pt-4">
            <?php if(Auth::check()): ?>
                <a href="/notebooks" class="inline-flex items-center justify-center rounded-xl text-base font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-8 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Vào sổ tay của bạn
                </a>
            <?php else: ?>
                <a href="/login" class="inline-flex items-center justify-center rounded-xl text-base font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-8 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Đăng nhập để học
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 max-w-6xl mx-auto mb-24 w-full px-4 relative z-10">
    <!-- Feature 1 -->
    <div class="group relative overflow-hidden rounded-2xl border bg-card text-card-foreground shadow-sm transition-all hover:shadow-md hover:border-primary/30">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="flex flex-col space-y-1.5 p-6 relative z-10">
            <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 text-primary group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="9" x2="9" y1="21" y2="9"/></svg>
            </div>
            <h3 class="font-bold text-xl leading-none tracking-tight">Flashcard Tốc Độ</h3>
            <p class="text-sm text-muted-foreground pt-4 leading-relaxed">Giao diện fullscreen, hiệu ứng lật mượt mà và tự động phát âm chuẩn xác. Giúp bạn tập trung 100% vào việc nhớ từ mà không bị phân tâm.</p>
        </div>
    </div>

    <!-- Feature 2 -->
    <div class="group relative overflow-hidden rounded-2xl border bg-card text-card-foreground shadow-sm transition-all hover:shadow-md hover:border-primary/30">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="flex flex-col space-y-1.5 p-6 relative z-10">
            <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 text-primary group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h3 class="font-bold text-xl leading-none tracking-tight">Tra Cứu Động Từ</h3>
            <p class="text-sm text-muted-foreground pt-4 leading-relaxed">Tích hợp sẵn bộ dữ liệu chia động từ ở mọi thì (Präsens, Perfekt, Präteritum...). Nhấn nút tra cứu ngay trên thẻ học cực kỳ tiện lợi.</p>
        </div>
    </div>

    <!-- Feature 3 -->
    <div class="group relative overflow-hidden rounded-2xl border bg-card text-card-foreground shadow-sm transition-all hover:shadow-md hover:border-primary/30 sm:col-span-2 lg:col-span-1">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="flex flex-col space-y-1.5 p-6 relative z-10">
            <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 text-primary group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            </div>
            <h3 class="font-bold text-xl leading-none tracking-tight">Quản Lý Tối Ưu</h3>
            <p class="text-sm text-muted-foreground pt-4 leading-relaxed">Tạo vô số sổ tay, chia nhóm linh hoạt. Thêm từ mới cực nhanh ngay trong lúc học, tìm kiếm và phân trang từ vựng không độ trễ.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
