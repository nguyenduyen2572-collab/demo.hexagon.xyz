<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saas_inquiries', function (Blueprint $table): void {
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('follow_up_at')->nullable(); // UTC; UI displays Asia/Ho_Chi_Minh.
            $table->index(['status', 'follow_up_at'], 'saas_inquiries_status_followup_idx');
        });

        Schema::create('saas_inquiry_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('saas_inquiry_id')->constrained('saas_inquiries')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admin_name', 255);
            $table->string('status_from', 20)->nullable();
            $table->string('status_to', 20)->nullable();
            $table->string('assignee_from', 255)->nullable();
            $table->string('assignee_to', 255)->nullable();
            $table->dateTime('follow_up_from')->nullable();
            $table->dateTime('follow_up_to')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at');
            $table->index(['saas_inquiry_id', 'created_at'], 'saas_activities_inquiry_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_inquiry_activities');
        Schema::table('saas_inquiries', function (Blueprint $table): void {
            $table->dropIndex('saas_inquiries_status_followup_idx');
            $table->dropConstrainedForeignId('assigned_admin_id');
            $table->dropColumn('follow_up_at');
        });
    }
};
