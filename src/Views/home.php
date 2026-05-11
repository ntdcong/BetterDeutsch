<?php
use App\Core\Auth;
ob_start();
?>

<?php if (Auth::check() && isset($stats)): ?>
    <!-- Dashboard for Logged-In Users -->
    <div class="space-y-8 w-full max-w-5xl mx-auto py-6">
        <!-- Welcome Banner Card -->
        <div class="relative overflow-hidden rounded-2xl border bg-card p-6 md:p-8 shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-teal-500/5 to-transparent"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-foreground">
                        Chào mừng quay trở lại, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-500 dark:from-blue-400 dark:to-teal-300"><?= htmlspecialchars($stats['username'], ENT_QUOTES, 'UTF-8') ?></span>!
                    </h2>
                    <p class="text-muted-foreground mt-2 text-base">Hôm nay bạn muốn học thêm bao nhiêu từ vựng tiếng Đức mới?</p>
                </div>
                <a href="/notebooks" class="inline-flex items-center justify-center rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-6 font-medium shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5 shrink-0">
                    Vào Sổ Tay Của Bạn
                </a>
            </div> 
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid gap-6 sm:grid-cols-2">
            <!-- Notebooks Stats Card -->
            <div class="group relative overflow-hidden rounded-2xl border bg-card p-6 shadow-sm transition-all hover:shadow-md hover:border-blue-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Sổ Tay</p>
                        <h3 class="text-4xl font-extrabold text-foreground group-hover:text-blue-500 transition-colors"><?= $stats['total_notebooks'] ?></h3>
                        <p class="text-xs text-muted-foreground">Số sổ tay bạn tạo.</p>
                    </div>
                    <div class="h-14 w-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-border flex justify-end relative z-10">
                    <a href="/notebooks" class="text-sm font-semibold text-blue-500 hover:text-blue-600 inline-flex items-center gap-1 transition-colors">
                        Quản lý sổ tay
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </div>

            <!-- Vocabulary Stats Card -->
            <div class="group relative overflow-hidden rounded-2xl border bg-card p-6 shadow-sm transition-all hover:shadow-md hover:border-teal-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">Từ Vựng</p>
                        <h3 class="text-4xl font-extrabold text-foreground group-hover:text-teal-500 transition-colors"><?= $stats['total_vocabularies'] ?></h3>
                        <p class="text-xs text-muted-foreground">Tổng số từ vựng bạn đã thêm.</p>
                    </div>
                    <div class="h-14 w-14 rounded-2xl bg-teal-500/10 flex items-center justify-center text-teal-500 group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="42" viewBox="-10 -206 524 452"><path d="M198 2c-10 0-18 8-18 18s8 18 18 18h108c10 0 18-8 18-18s-8-18-18-18H198zM0-124c0-40 32-72 72-72h360c40 0 72 32 72 72 0 27-14 50-36 62v226c0 40-32 72-72 72H108c-40 0-72-32-72-72V-62C15-74 0-97 0-124zm468 0c0-20-16-36-36-36H72c-20 0-36 16-36 36s16 36 36 36h360c20 0 36-16 36-36zM72 164c0 20 16 36 36 36h288c20 0 36-16 36-36V-52H72v216z" fill="#0F9589"/></svg>                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-border flex justify-end relative z-10">
                    <a href="/notebooks" class="text-sm font-semibold text-teal-500 hover:text-teal-600 inline-flex items-center gap-1 transition-colors">
                        Xem chi tiết từ vựng
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Interactive Tips & Spaced Repetition Info -->
        <div class="rounded-2xl border bg-gradient-to-br from-card to-muted/20 p-6 shadow-sm">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
                Mẹo học thông minh 
            </h3>
            <ul class="space-y-3.5 text-sm text-muted-foreground list-none pl-1">
                <li class="flex gap-2.5 items-start">
                    <span class="h-5 w-5 rounded-full bg-blue-500/15 flex items-center justify-center text-blue-500 shrink-0 font-bold text-xs mt-0.5">1</span>
                    <span>Sử dụng phương pháp <strong class="text-foreground">Lập lại ngắt quãng (Spaced Repetition)</strong> bằng cách ôn tập lại các thẻ flashcard sau 1 ngày, 3 ngày, và 7 ngày.</span>
                </li>
                <li class="flex gap-2.5 items-start">
                    <span class="h-5 w-5 rounded-full bg-blue-500/15 flex items-center justify-center text-blue-500 shrink-0 font-bold text-xs mt-0.5">2</span>
                    <span>Mỗi ngày học từ 5-10 từ vựng sẽ hiệu quả hơn nhiều so với việc cố gắng học 50 từ trong một buổi duy nhất.</span>
                </li>
                <li class="flex gap-2.5 items-start">
                    <span class="h-5 w-5 rounded-full bg-blue-500/15 flex items-center justify-center text-blue-500 shrink-0 font-bold text-xs mt-0.5">3</span>
                    <span>Thường xuyên ôn tập cách chia các động từ ở các thì quá khứ (Perfekt, Präteritum) để nói tiếng Đức tự nhiên hơn.</span>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <!-- Landing layout for Guests (Not Logged In) -->
    <div class="flex-1 flex flex-col items-center justify-center py-20 md:py-32 w-full relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-4xl bg-primary/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="space-y-6 max-w-4xl px-4 text-center">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-foreground">
                Học tiếng Đức <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-teal-400">thông minh và hiệu quả hơn</span>
            </h1>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-10 pt-4">
                <a href="/login" class="inline-flex items-center justify-center rounded-xl text-base font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring bg-primary text-primary-foreground hover:bg-primary/90 h-12 px-8 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    Đăng nhập để học
                </a>
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
<?php endif; ?>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
