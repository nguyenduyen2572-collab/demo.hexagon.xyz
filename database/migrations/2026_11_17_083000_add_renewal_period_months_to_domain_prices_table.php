<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('domain_prices', 'renewal_period_months')) {
            Schema::table('domain_prices', function (Blueprint $table): void {
                $table->unsignedSmallInteger('renewal_period_months')
                    ->default(12)
                    ->after('renewal_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('domain_prices', 'renewal_period_months')) {
            Schema::table('domain_prices', function (Blueprint $table): void {
                $table->dropColumn('renewal_period_months');
            });
        }
    }
};
