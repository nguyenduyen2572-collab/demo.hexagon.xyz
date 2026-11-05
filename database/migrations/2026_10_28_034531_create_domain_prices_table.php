
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_prices', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('extension', 30)->unique();
            $table->unsignedBigInteger('registration_price');
            $table->unsignedBigInteger('renewal_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_prices');
    }
};
