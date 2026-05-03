<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['tenant_id', 'status', 'due_date'], 'invoices_tenant_status_due_idx');
            $table->index(['tenant_id', 'status', 'paid_at'], 'invoices_tenant_status_paid_idx');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_tenant_status_due_idx');
            $table->dropIndex('invoices_tenant_status_paid_idx');
        });
    }
};
