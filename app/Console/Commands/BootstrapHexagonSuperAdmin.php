<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\HexagonAdminRoles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class BootstrapHexagonSuperAdmin extends Command
{
    protected $signature = 'hexagon:admin-bootstrap {email : Email tài khoản Admin sẽ là Super Admin} {--dry-run : Chỉ kiểm tra}';
    protected $description = 'Chỉ định Super Admin, chuyển các Admin cũ khác thành quản lý có quyền nghiệp vụ';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $user = DB::table('users')->where('email', $email)->where('is_admin', true)->first();
        if ($user === null) {
            $this->error('Không tìm thấy tài khoản có quyền Admin với email này.');
            return self::FAILURE;
        }
        $others = DB::table('users')->where('is_admin', true)->where('id', '<>', $user->id)
            ->where(static function ($query): void {
                $query->whereNull('admin_role')->orWhere('admin_role', 'super_admin');
            })->count();
        $this->line('Super Admin được chỉ định: '. $user->name.' ('. $user->email .')');
        $this->line('Admin cũ khác chuyển thành quản lý (giữ quyền nghiệp vụ): '.$others);
        if ($this->option('dry-run')) {
            $this->info('DRY RUN PASS — không thay đổi dữ liệu.');
            return self::SUCCESS;
        }
        if (! $this->confirm('Bạn đã xác minh email và đồng ý thay đổi quyền quản trị?', false)) {
            $this->warn('Đã hủy, không có thay đổi.');
            return self::FAILURE;
        }
        DB::transaction(function () use ($user): void {
            DB::table('users')->where('is_admin', true)->where('id', '<>', $user->id)
                ->where(static function ($query): void {
                    $query->whereNull('admin_role')->orWhere('admin_role', 'super_admin');
                })->update([
                    'admin_role' => 'manager',
                    'admin_permissions' => json_encode(array_keys(HexagonAdminRoles::CAPABILITIES), JSON_THROW_ON_ERROR),
                    'updated_at' => now(),
                ]);
            DB::table('users')->where('id', $user->id)->update([
                'admin_role' => 'super_admin',
                'admin_permissions' => null,
                'admin_enabled' => true,
                'updated_at' => now(),
            ]);
        });
        $this->info('BOOTSTRAP PASS — Đã chỉ định một Super Admin.');
        return self::SUCCESS;
    }
}
