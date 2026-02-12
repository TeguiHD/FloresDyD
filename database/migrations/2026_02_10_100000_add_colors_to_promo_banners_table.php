<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->string('bg_color', 20)->default('#1a1a2e')->after('icon');
            $table->string('text_color', 20)->default('#ffffff')->after('bg_color');
        });
    }

    public function down(): void
    {
        Schema::table('promo_banners', function (Blueprint $table) {
            $table->dropColumn(['bg_color', 'text_color']);
        });
    }
};
