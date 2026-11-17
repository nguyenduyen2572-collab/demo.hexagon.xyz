<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('domain_price_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('domain_price_id')->constrained('domain_prices')->cascadeOnDelete();
            $table->string('extension', 30);
            $table->string('action', 16);
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admin_name', 255);
            $table->unsignedBigInteger('old_registration_price')->nullable();
            $table->unsignedBigInteger('new_registration_price');
            $table->unsignedBigInteger('old_renewal_price')->nullable();
            $table->unsignedBigInteger('new_renewal_price');
            $table->unsignedSmallInteger('old_renewal_period_months')->nullable();
            $table->unsignedSmallInteger('new_renewal_period_months');
            $table->boolean('old_is_active')->nullable();
            $table->boolean('new_is_active');
            $table->timestamp('created_at');
            $table->index(['domain_price_id', 'created_at'], 'domain_price_history_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_price_histories');
    }
};
