<?php ob_start(); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Quản lý Admin</h1>
            <p class="text-muted-foreground mt-1">Thống kê hệ thống, quản lý thành viên, phân quyền và cài lại mật khẩu.</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tổng người dùng</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold"><?= number_format($stats['total_users']) ?></div>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tổng sổ tay</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold"><?= number_format($stats['total_notebooks']) ?></div>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Sổ tay chung</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold"><?= number_format($stats['total_public_notebooks']) ?></div>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tổng từ vựng</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold"><?= number_format($stats['total_vocabularies']) ?></div>
            </div>
        </div>
    </div>

    <div class="rounded-md border border-border bg-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-muted/50 text-muted-foreground">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">ID</th>
                        <th scope="col" class="px-6 py-3 font-medium">Tên</th>
                        <th scope="col" class="px-6 py-3 font-medium">Email</th>
                        <th scope="col" class="px-6 py-3 font-medium">Ngày tham gia</th>
                        <th scope="col" class="px-6 py-3 font-medium">Quyền</th>
                        <th scope="col" class="px-6 py-3 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-4"><?= $user['id'] ?></td>
                        <td class="px-6 py-4 font-medium"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-6 py-4 text-muted-foreground"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                        <td class="px-6 py-4">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="inline-flex items-center rounded-full border border-transparent bg-primary px-2.5 py-0.5 text-xs font-semibold text-primary-foreground">Admin</span>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full border border-transparent bg-secondary px-2.5 py-0.5 text-xs font-semibold text-secondary-foreground">User</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <?php if ($user['id'] !== $currentUserId): ?>
                                <button onclick="changeRole(<?= $user['id'] ?>, '<?= $user['role'] ?>')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3">
                                    Đổi quyền
                                </button>
                                <button onclick="resetPassword(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['name'])) ?>')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/90 h-8 px-3">
                                    Đổi mật khẩu
                                </button>
                            <?php else: ?>
                                <span class="text-muted-foreground text-xs italic">Bạn</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">Không có dữ liệu</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function changeRole(userId, currentRole) {
        const newRole = currentRole === 'admin' ? 'user' : 'admin';
        const roleName = newRole === 'admin' ? 'Quản trị viên (Admin)' : 'Người dùng (User)';
        
        const result = await Swal.fire({
            title: 'Xác nhận thay đổi',
            text: `Bạn có chắc chắn muốn chuyển quyền của tài khoản này thành ${roleName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch('/admin/update-role', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, role: newRole })
                });

                const data = await response.json();
                
                if (data.success) {
                    await Swal.fire('Thành công', data.message, 'success');
                    window.location.reload();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại sau.', 'error');
            }
        }
    }

    async function resetPassword(userId, userName) {
        const result = await Swal.fire({
            title: `Cài lại mật khẩu cho ${userName}`,
            text: "Nhập mật khẩu mới (ít nhất 6 ký tự):",
            input: 'password',
            inputAttributes: {
                minlength: 6,
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Lưu mật khẩu',
            cancelButtonText: 'Hủy',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password || password.length < 6) {
                    Swal.showValidationMessage('Mật khẩu phải có ít nhất 6 ký tự');
                    return false;
                }
                return password;
            }
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch('/admin/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, password: result.value })
                });

                const data = await response.json();
                
                if (data.success) {
                    Swal.fire('Thành công', data.message, 'success');
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại sau.', 'error');
            }
        }
    }
</script>
<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/admin.php';

