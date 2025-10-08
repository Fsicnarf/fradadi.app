<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patient_files', function (Blueprint $table) {
            $table->string('title', 150)->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('patient_files', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
