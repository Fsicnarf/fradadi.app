<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dental_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('key');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->index(['key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dental_records');
    }
};
