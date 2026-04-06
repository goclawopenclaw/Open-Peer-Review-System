<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_decisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id');
            $table->uuid('editor_id');
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->foreign('editor_id')->references('id')->on('users');
            $table->enum('decision', ['accept', 'minor_revisions', 'major_revisions', 'desk_reject', 'reject']);
            $table->text('decision_letter')->nullable();
            $table->timestamp('revision_deadline_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index('decision');
        });

        Schema::create('author_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id');
            $table->integer('revision_version');
            $table->string('response_document_url')->nullable();
            $table->text('response_text')->nullable();
            $table->string('new_manuscript_url')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            $table->index('revision_version');
        });

        Schema::create('reviewer_suggestions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id');
            $table->string('reviewer_name');
            $table->string('reviewer_email');
            $table->string('institution')->nullable();
            $table->text('rationale')->nullable();
            $table->boolean('conflict_of_interest')->default(false);
            $table->timestamps();
            
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('action');
            $table->string('resource_type')->nullable();
            $table->uuid('resource_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('reviewer_suggestions');
        Schema::dropIfExists('author_responses');
        Schema::dropIfExists('editorial_decisions');
    }
};
