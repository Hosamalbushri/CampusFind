<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_found_report_responses', function (Blueprint $table): void {
            $table->id();
            $table->string('public_reference', 64);
            $table->string('public_reference_key', 64)->unique();
            $table->unsignedBigInteger('lost_report_id');
            $table->unsignedBigInteger('responder_student_id');
            $table->unsignedInteger('reviewer_user_id')->nullable();
            $table->unsignedBigInteger('resulting_found_item_id')->nullable();
            $table->string('status', 32)->default('submitted');
            $table->string('found_location', 255);
            $table->dateTime('found_at')->nullable();
            $table->string('dropoff_location', 255);
            $table->text('message')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('review_started_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('lost_report_id')->references('id')->on('lost_found_reports')->restrictOnDelete();
            $table->foreign('responder_student_id')->references('id')->on('students')->restrictOnDelete();
            $table->foreign('reviewer_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('resulting_found_item_id')->references('id')->on('lost_found_items')->restrictOnDelete();

            $table->unique(['lost_report_id', 'responder_student_id'], 'lf_response_report_responder_unique');
            $table->unique('resulting_found_item_id', 'lf_response_result_item_unique');
            $table->index(['status', 'submitted_at'], 'lf_response_status_time_index');
            $table->index(['lost_report_id', 'status'], 'lf_response_report_status_index');
        });

        Schema::create('lost_found_report_response_images', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('response_id');
            $table->text('storage_key');
            $table->char('storage_key_hash', 64)->unique();
            $table->string('mime_type', 64);
            $table->unsignedBigInteger('byte_size');
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('lost_found_report_responses')->restrictOnDelete();
            $table->index(['response_id', 'submitted_at'], 'lf_response_image_time_index');
        });

        Schema::create('lost_found_report_response_reviews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('response_id');
            $table->unsignedInteger('reviewer_user_id');
            $table->string('from_status', 32);
            $table->string('to_status', 32);
            $table->text('staff_notes')->nullable();
            $table->dateTime('reviewed_at');
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('lost_found_report_responses')->restrictOnDelete();
            $table->foreign('reviewer_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->index(['response_id', 'reviewed_at'], 'lf_response_review_time_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_found_report_response_reviews');
        Schema::dropIfExists('lost_found_report_response_images');
        Schema::dropIfExists('lost_found_report_responses');
    }
};
