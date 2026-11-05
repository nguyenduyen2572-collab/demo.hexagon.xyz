<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();

            // Mã đơn hàng
            $table->uuid('order_code')->unique();

            // Thông tin khách hàng
            $table->string('customer_name', 150);
            $table->string('customer_email', 190);
            $table->string('customer_phone', 30);

            // Tổng tiền VND
            $table->unsignedBigInteger('total_amount');

            // Trạng thái đơn hàng demo
            $table->string('status', 30)
                  ->default('pending_demo');

            $table->boolean('is_mock')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
