<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('repository_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('project_skill', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_skill');
        Schema::dropIfExists('projects');
    }
};
