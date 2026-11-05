<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 180)->unique();
            $table->string('name_vi', 150);
            $table->string('name_en', 150)->nullable();
            $table->string('summary_vi', 500)->nullable();
            $table->string('summary_en', 500)->nullable();
            $table->text('description_vi')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedBigInteger('price_monthly')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_services');
    }
};
