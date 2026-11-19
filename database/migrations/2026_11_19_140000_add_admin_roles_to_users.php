<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('admin_role', 24)->nullable()->index();
            $table->json('admin_permissions')->nullable();
            $table->boolean('admin_enabled')->default(true);
        });
        // Giữ nguyên các tài khoản Admin cũ. Lệnh bootstrap chỉ định
        // một Super Admin sau khi chủ dự án xác nhận email của mình.
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['admin_role', 'admin_permissions', 'admin_enabled']);
        });
    }
};
