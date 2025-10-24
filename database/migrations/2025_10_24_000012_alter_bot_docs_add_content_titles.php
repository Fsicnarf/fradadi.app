<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bot_docs', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('size');
            $table->json('auto_titles')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('bot_docs', function (Blueprint $table) {
            $table->dropColumn(['content','auto_titles']);
        });
    }
};
