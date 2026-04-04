<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('author_id')->index();
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->text('abstract');
            $table->json('keywords')->default('[]');
            $table->string('research_field')->nullable();
            $table->text('funding_source')->nullable();
            $table->text('competing_interests')->nullable();
            $table->text('data_availability')->nullable();
            $table->string('manuscript_url')->nullable();
            $table->enum('status', ['draft', 'submitted', 'screening', 'under_review', 'revision_requested', 'published', 'desk_rejected', 'rejected'])->default('draft');
            $table->integer('version')->default(1);
            $table->string('doi')->unique()->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('desk_rejected_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('research_field');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
