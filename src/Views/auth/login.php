<?php
use App\Core\Security;
ob_start();
?>
<div class="flex-1 flex items-center justify-center py-12 px-4">
    <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="font-semibold tracking-tight text-2xl">Đăng nhập</h3>
            <p class="text-sm text-muted-foreground">Nhập email của bạn để truy cập vào tài khoản</p>
        </div>
        <div class="p-6 pt-0">
            <form action="/login" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium leading-none">Email</label>
                    <input type="email" id="email" name="email" required placeholder="m@example.com" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium leading-none">Mật khẩu</label>
                    <input type="password" id="password" name="password" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                </div>
                
                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full mt-2">
                    Đăng nhập
                </button>
            </form>
            
            <div class="mt-4 text-center text-sm">
                Chưa có tài khoản? <a href="/register" class="underline underline-offset-4 hover:text-primary">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/main.php';
