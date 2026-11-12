<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saas_inquiries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('saas_service_id')->nullable()->constrained('saas_services')->nullOnDelete();
            $table->string('service_name', 150);
            $table->string('customer_name', 150);
            $table->string('customer_email', 190);
            $table->string('customer_phone', 30);
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['saas_service_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_inquiries');
    }
};
