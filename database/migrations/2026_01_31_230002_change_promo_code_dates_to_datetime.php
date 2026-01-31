<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('promo_codes')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE promo_codes MODIFY starts_at DATETIME NULL');
            DB::statement('ALTER TABLE promo_codes MODIFY ends_at DATETIME NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('promo_codes')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE promo_codes MODIFY starts_at TIMESTAMP NULL');
            DB::statement('ALTER TABLE promo_codes MODIFY ends_at TIMESTAMP NULL');
        }
    }
};

