<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('account_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            
            // Business documents
            $table->string('business_license_path')->nullable();
            $table->string('tax_certificate_path')->nullable();
            $table->string('registration_certificate_path')->nullable();
            
            // Representative documents
            $table->string('id_document_path')->nullable();
            $table->string('id_document_type')->nullable(); // passport, national_id, etc.
            $table->string('id_document_number')->nullable();
            
            // Status and review
            $table->string('status')->default('pending'); // pending, in_review, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            // Additional verification data
            $table->json('additional_data')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_verifications');
    }
};
