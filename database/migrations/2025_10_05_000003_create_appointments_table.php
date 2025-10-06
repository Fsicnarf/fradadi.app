<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->default('Cita');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->string('specialty')->nullable(); // Odontología, etc.
            $table->string('appointment_type')->nullable(); // Primera vez, Control, etc.
            $table->unsignedInteger('duration_min')->default(30);
            $table->string('channel')->nullable(); // WhatsApp, Teléfono, Web
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
