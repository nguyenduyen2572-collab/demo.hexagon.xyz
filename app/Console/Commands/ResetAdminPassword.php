<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'hexagon:reset-admin-password';

    protected $description = 'Reset HEXAGON admin password';

    public function handle(): int
    {
        $email = strtolower(trim(
            $this->ask('Email tài khoản Admin')
        ));

        $user = User::where('email', $email)->first();

        if (!$user || !$user->is_admin) {
            $this->error('Không tìm thấy tài khoản Admin.');
            return self::FAILURE;
        }

        $password = $this->secret('Nhập mật khẩu mới');

        $confirm = $this->secret('Nhập lại mật khẩu mới');

        if (
            !$password ||
            strlen($password) < 12 ||
            $password !== $confirm
        ) {
            $this->error(
                'Mật khẩu phải có ít nhất 12 ký tự và khớp nhau.'
            );

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info('Đặt lại mật khẩu Admin thành công!');

        return self::SUCCESS;
    }
}
