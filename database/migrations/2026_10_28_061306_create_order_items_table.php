<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // Liên kết đơn hàng
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // Thông tin tên miền
            $table->string('domain_name', 190);
            $table->string('extension', 30);

            // Giá tại thời điểm đặt
            $table->unsignedBigInteger('registration_price');

            // Thời hạn đăng ký
            $table->unsignedInteger('registration_years')
                  ->default(1);

            // Thành tiền
            $table->unsignedBigInteger('line_total');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
