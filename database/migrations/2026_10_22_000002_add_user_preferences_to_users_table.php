<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable();
            $table->string('date_format', 20)->default('d/m/Y');
            $table->unsignedTinyInteger('low_balance_percent')->default(20);
            $table->boolean('dark_mode')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path',
                'date_format',
                'low_balance_percent',
                'dark_mode',
            ]);
        });
    }
};
