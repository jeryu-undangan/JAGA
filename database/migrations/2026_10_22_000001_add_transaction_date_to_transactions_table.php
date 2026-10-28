<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('transaction_date')->nullable();
        });

        DB::table('transactions')
            ->select(['id', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function ($transactions): void {
                foreach ($transactions as $transaction) {
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'transaction_date' => substr($transaction->created_at, 0, 10),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_date');
        });
    }
};
