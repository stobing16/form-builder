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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_type_id')->constrained()->onDelete('cascade');

            $table->longText('question');
            $table->longText('slug');
            $table->longText('catatan')->nullable();
            $table->boolean('is_required');

            $table->json('options')->nullable();
            $table->boolean('has_additional_question');

            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
