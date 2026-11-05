<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'hexagon:create-admin';

    protected $description = 'Create a HEXAGON admin account';

    public function handle(): int
    {
        $name = trim($this->ask('Tên quản trị viên', 'HEXAGON Admin'));
        $email = strtolower(trim($this->ask('Email Admin')));
        $password = $this->secret('Mật khẩu Admin (ít nhất 12 ký tự)');

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($password ?? '') < 12
        ) {
            $this->error('Email không hợp lệ hoặc mật khẩu quá ngắn.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Email đã tồn tại.');
            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->is_admin = true;
        $user->save();

        $this->info('Đã tạo tài khoản Admin thành công!');

        return self::SUCCESS;
    }
}
