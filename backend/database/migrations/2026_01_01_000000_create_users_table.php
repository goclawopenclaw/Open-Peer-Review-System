<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('affiliation')->nullable();
            $table->string('orcid')->unique()->nullable();
            $table->json('expertise_areas')->default('[]');
            $table->string('profile_picture_url')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_editor')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('email');
            $table->index('is_editor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
