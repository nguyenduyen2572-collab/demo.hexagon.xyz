
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_domain_statuses', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('domain', 190)->unique();
            $table->boolean('is_available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_domain_statuses');
    }
};
