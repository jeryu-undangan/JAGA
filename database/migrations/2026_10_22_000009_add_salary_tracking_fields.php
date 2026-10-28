<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('base_salary')->default(0);
            $table->unsignedTinyInteger('salary_payday_day')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('application_mode', 20)->default('kos');
            $table->index(['user_id', 'application_mode', 'transaction_date']);
        });

        Schema::create('salary_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('budget_month', 7);
            $table->string('category', 50);
            $table->unsignedBigInteger('amount');
            $table->timestamps();
            $table->unique(['user_id', 'budget_month', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_budgets');

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'application_mode', 'transaction_date']);
            $table->dropColumn('application_mode');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['base_salary', 'salary_payday_day']);
        });
    }
};
