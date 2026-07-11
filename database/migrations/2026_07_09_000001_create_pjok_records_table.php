<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pjok_records', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->index();
            $table->string('code', 120)->nullable()->index();
            $table->string('name')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['type', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pjok_records');
    }
};
