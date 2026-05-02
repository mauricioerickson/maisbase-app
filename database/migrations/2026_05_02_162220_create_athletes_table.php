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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
            $table->integer('risk_score')->default(0); // 0-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
