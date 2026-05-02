<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes();
            // Drop current string column and create enum
            $table->dropColumn('nudge_tone');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('nudge_tone', ['amigavel', 'formal', 'direto'])->default('amigavel');
        });

        Schema::table('athletes', function (Blueprint $table) {
            $table->timestamp('last_attendance_at')->nullable();
            $table->date('birth_date')->nullable(false)->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable();
            
            // For enum changes, we drop and recreate to be safe across DB versions, 
            // but since status data might be lost, if this was production we would use change()
            // or raw SQL. Since the app is in dev, we drop/add.
            $table->dropColumn('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid_at');
            $table->dropColumn('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
        });

        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn('last_attendance_at');
            $table->date('birth_date')->nullable()->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('nudge_tone');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->string('nudge_tone')->default('friendly');
        });
    }
};
