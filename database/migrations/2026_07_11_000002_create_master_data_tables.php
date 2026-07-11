<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('status')->default('Aktif');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('student_id')->unique();
                $table->string('name');
                $table->string('gender')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('Aktif');
                $table->string('class_name')->nullable()->index();
                $table->string('year')->nullable()->index();
                $table->string('semester')->nullable();
                $table->unsignedTinyInteger('attendance')->default(0);
                $table->decimal('cognitive', 4, 1)->default(0);
                $table->decimal('affective', 4, 1)->default(0);
                $table->decimal('psychomotor', 4, 1)->default(0);
                $table->decimal('final_score', 5, 1)->default(0);
                $table->string('predicate')->nullable();
                $table->string('predicate_class')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('nip')->unique();
                $table->string('name');
                $table->string('gender')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('Aktif');
                $table->string('role')->default('Guru PJOK')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('assessments')) {
            Schema::create('assessments', function (Blueprint $table) {
                $table->id();
                $table->string('year')->index();
                $table->string('semester')->index();
                $table->string('class_name')->index();
                $table->string('meeting', 20)->index();
                $table->string('type')->index();
                $table->string('materi')->nullable();
                $table->text('tujuan')->nullable();
                $table->string('aspect')->nullable()->index();
                $table->json('criteria')->nullable();
                $table->timestamps();

                $table->unique(['year', 'semester', 'class_name', 'meeting', 'type', 'aspect'], 'assessments_unique_plan');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('classes');
    }
};
