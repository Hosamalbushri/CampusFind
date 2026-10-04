<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_found_match_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('potential_match_id');
            $table->unsignedInteger('generated_by_user_id');
            $table->string('algorithm_version', 32);
            $table->unsignedInteger('score_basis_points');
            $table->json('signals');
            $table->char('input_fingerprint', 64);
            $table->dateTime('generated_at');
            $table->timestamps();

            $table->foreign('potential_match_id')->references('id')->on('lost_found_potential_matches')->restrictOnDelete();
            $table->foreign('generated_by_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->unique(['potential_match_id', 'input_fingerprint'], 'lf_match_snapshot_fingerprint_unique');
            $table->index(['potential_match_id', 'generated_at'], 'lf_match_snapshot_time_index');
            $table->index(['score_basis_points', 'generated_at'], 'lf_match_snapshot_score_index');
        });

        Schema::create('lost_found_match_reviews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('potential_match_id');
            $table->unsignedInteger('reviewer_user_id');
            $table->string('decision', 20);
            $table->text('notes')->nullable();
            $table->dateTime('reviewed_at');
            $table->timestamps();

            $table->foreign('potential_match_id')->references('id')->on('lost_found_potential_matches')->restrictOnDelete();
            $table->foreign('reviewer_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->index(['potential_match_id', 'reviewed_at', 'id'], 'lf_match_review_time_index');
            $table->index(['decision', 'reviewed_at'], 'lf_match_review_decision_index');
        });

        Schema::table('lost_found_items', function (Blueprint $table): void {
            $table->index(['category_id', 'status', 'found_at', 'id'], 'lf_items_match_retrieval_index');
        });

        Schema::table('lost_found_reports', function (Blueprint $table): void {
            $table->index(['category_id', 'status', 'lost_at', 'id'], 'lf_reports_match_retrieval_index');
        });
    }

    public function down(): void
    {
        Schema::table('lost_found_reports', function (Blueprint $table): void {
            $table->dropIndex('lf_reports_match_retrieval_index');
        });

        Schema::table('lost_found_items', function (Blueprint $table): void {
            $table->dropIndex('lf_items_match_retrieval_index');
        });

        Schema::dropIfExists('lost_found_match_reviews');
        Schema::dropIfExists('lost_found_match_snapshots');
    }
};
