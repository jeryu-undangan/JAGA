<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('users', 'email_verification_code_hash') ? 'email_verification_code_hash' : null,
            Schema::hasColumn('users', 'email_verification_expires_at') ? 'email_verification_expires_at' : null,
        ]));

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_verification_code_hash')) {
                $table->string('email_verification_code_hash')->nullable();
            }
            if (! Schema::hasColumn('users', 'email_verification_expires_at')) {
                $table->timestamp('email_verification_expires_at')->nullable();
            }
        });
    }
};
