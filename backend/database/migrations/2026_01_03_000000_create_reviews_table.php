<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id')->index();
            $table->uuid('reviewer_id')->nullable();
            $table->uuid('assigned_by_editor_id');
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by_editor_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamp('deadline_at');
            $table->enum('status', ['pending', 'accepted', 'declined', 'submitted', 'overdue'])->default('pending');
            $table->timestamp('invitation_sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->string('alternative_reviewer_name')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assignment_id');
            $table->uuid('submission_id')->index();
            $table->uuid('reviewer_id')->index();
            $table->foreign('assignment_id')->references('id')->on('review_assignments')->onDelete('cascade');
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('summary')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('detailed_comments')->nullable();
            $table->enum('recommendation', ['accept', 'minor_revisions', 'major_revisions', 'reject'])->nullable();
            $table->enum('confidence', ['high', 'medium', 'low'])->nullable();
            $table->boolean('is_signed')->default(true);
            $table->boolean('is_public')->default(true);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->index('submitted_at');
        });

        Schema::create('inline_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('review_id');
            $table->uuid('submission_id');
            $table->uuid('reviewer_id');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->integer('paragraph_number')->nullable();
            $table->string('text_excerpt', 500)->nullable();
            $table->text('comment_text');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            
            $table->index('paragraph_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inline_comments');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('review_assignments');
    }
};
