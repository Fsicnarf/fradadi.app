<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('dni', 20)->nullable()->after('user_id');
            $table->string('patient_name')->nullable()->after('dni');
            $table->unsignedInteger('patient_age')->nullable()->after('patient_name');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['dni','patient_name','patient_age']);
        });
    }
};
