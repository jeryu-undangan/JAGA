<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payday_interval', 20)->nullable();
            $table->date('payday_anchor_date')->nullable();
        });

        $today = CarbonImmutable::today();

        DB::table('users')
            ->whereNotNull('payday_day')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($today): void {
                foreach ($users as $user) {
                    $day = (int) $user->payday_day;
                    $candidate = $today->startOfMonth()->setDay(min($day, $today->daysInMonth));

                    if ($candidate->lessThanOrEqualTo($today)) {
                        $nextMonth = $today->startOfMonth()->addMonth();
                        $candidate = $nextMonth->setDay(min($day, $nextMonth->daysInMonth));
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'payday_interval' => 'monthly',
                            'payday_anchor_date' => $candidate->toDateString(),
                        ]);
                }
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('payday_day');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('payday_day')->nullable();
        });

        DB::table('users')
            ->whereNotNull('payday_anchor_date')
            ->update(['payday_day' => DB::raw('CAST(substr(payday_anchor_date, 9, 2) AS INTEGER)')]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payday_interval', 'payday_anchor_date']);
        });
    }
};
