<?php
ob_start();
?>
<div class="flex-1 flex flex-col items-center justify-center py-12 md:py-24 lg:py-32 text-center w-full">
    <div class="space-y-4 max-w-3xl px-4">
        <h1 class="text-4xl font-extrabold tracking-tight lg:text-5xl">Học tiếng Đức chưa bao giờ dễ dàng đến thế</h1>
        <p class="text-xl text-muted-foreground">
            Hệ thống flashcard, bài học và quiz trắc nghiệm được thiết kế tối giản, 
            tập trung tối đa vào hiệu quả học tập của bạn.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
            <a href="/register" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-11 px-8">
                Bắt đầu học ngay
            </a>
            <a href="/lessons" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-11 px-8">
                Xem bài học
            </a>
        </div>
    </div>
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 max-w-5xl mx-auto mb-20 w-full px-4">
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="font-semibold leading-none tracking-tight">Flashcard Thông Minh</h3>
            <p class="text-sm text-muted-foreground pt-2">Ôn tập từ vựng hiệu quả với thuật toán lặp lại ngắt quãng.</p>
        </div>
    </div>
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="font-semibold leading-none tracking-tight">Bài Học Có Hệ Thống</h3>
            <p class="text-sm text-muted-foreground pt-2">Các bài học được tổ chức từ cơ bản đến nâng cao theo trình độ.</p>
        </div>
    </div>
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="font-semibold leading-none tracking-tight">Kiểm Tra Năng Lực</h3>
            <p class="text-sm text-muted-foreground pt-2">Làm bài quiz để đánh giá mức độ tiếp thu sau mỗi bài học.</p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
