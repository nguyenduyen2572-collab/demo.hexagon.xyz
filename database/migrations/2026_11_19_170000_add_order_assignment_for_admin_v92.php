<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('assigned_admin_id')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
        });
        Schema::create('order_assignment_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('from_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_assignment_histories');
        Schema::table('orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('assigned_admin_id'));
    }
};
