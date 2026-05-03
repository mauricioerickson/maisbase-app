<?php

// filepath: database/migrations/2026_05_02_192259_create_maisbase_schema.php

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
        // 1. Tenants
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('document')->nullable();
            $table->string('pix_key')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->enum('nudge_tone', ['amigavel', 'formal'])->default('amigavel');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Users (Altering existing table from Laravel)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->enum('role', ['admin', 'professor', 'financeiro'])->default('admin');
        });

        // 3. Guardians
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('whatsapp_number')->index();
            $table->string('document')->nullable();
            $table->timestamps();
        });

        // 4. Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->integer('billing_cycle_days')->default(30);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. Athletes
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
            $table->integer('risk_score')->default(0);
            $table->timestamps();
        });

        // 6. Categorias
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->timestamps();
        });

        // 7. Grades de Horários (Schedules)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->enum('day_of_week', ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_capacity')->default(20);
            $table->timestamps();
        });

        // 8. Matrículas (Enrollments)
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->boolean('technical_exception')->default(false);
            $table->timestamps();
            
            $table->unique(['athlete_id', 'schedule_id']);
        });

        // 9. Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->string('external_id')->nullable()->index();
            $table->text('pix_copy_paste')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 10. Webhook Logs
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->json('payload');
            $table->timestamps();
        });

        // 11. Presenças (Attendance)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('date')->index();
            $table->boolean('is_present')->default(false);
            $table->string('justification')->nullable();
            $table->timestamps();
            
            $table->unique(['athlete_id', 'schedule_id', 'date']);
        });

        // 12. Atestados Médicos (Medical Clearances)
        Schema::create('medical_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->date('expiry_date')->index();
            $table->string('file_path')->nullable();
            $table->enum('status', ['valid', 'expired', 'pending'])->default('valid');
            $table->timestamps();
        });

        // 13. Logs de IA e Nudges
        Schema::create('ai_nudge_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->string('type'); // billing, retention, health
            $table->text('message');
            $table->string('status')->default('sent'); // sent, delivered, read
            $table->decimal('recovered_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_nudge_logs');
        Schema::dropIfExists('medical_clearances');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('athletes');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('guardians');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'role']);
        });
        Schema::dropIfExists('tenants');
    }
};
