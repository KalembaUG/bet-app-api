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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->unsignedInteger('candidate_id')->nullable(); #this makes the field optional
            $table->string('candidate_name');
            $table->string('location_name');
            $table->dateTime('date');
            $table->decimal('longitude', $precision = 12, $scale = 7);
            $table->decimal('latitude', $precision = 12, $scale = 7);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};